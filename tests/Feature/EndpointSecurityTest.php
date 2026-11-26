<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\ProductCatalog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Tests\TestCase;

class EndpointSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        \Illuminate\Support\Facades\Bus::fake([\App\Jobs\SendWhatsAppAlert::class]);
    }

    public function test_public_whatsapp_qr_endpoint_is_blocked(): void
    {
        // Public /whatsapp/qr must return 404
        $response = $this->get('/whatsapp/qr');
        $response->assertStatus(404);
    }

    public function test_admin_whatsapp_qr_endpoint_requires_authentication(): void
    {
        $adminPath = env('ADMIN_SECRET_PATH', 'ps-control-morjim');
        $response = $this->get("/{$adminPath}/whatsapp/qr");
        // Must redirect to login or be 403 forbidden
        $this->assertTrue(in_array($response->status(), [302, 403]));
    }

    public function test_insecure_phone_login_bypass_route_is_removed(): void
    {
        $response = $this->post('/my-account/login', [
            'phone' => '9876543210'
        ]);
        $this->assertTrue(in_array($response->status(), [404, 405]));
    }

    public function test_send_otp_does_not_leak_customer_pii(): void
    {
        Order::create([
            'order_number' => 'PAT-TEST-001',
            'customer_name' => 'Secret Customer Name',
            'customer_phone' => '9823000001',
            'delivery_address' => 'Secret Villa Morjim Goa',
            'total_amount' => 500,
            'status' => 'pending',
            'reorder_token' => Str::random(24),
        ]);

        $response = $this->postJson(route('order.sendOtp'), [
            'phone' => '9823000001'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
        ]);

        // Assert that private name and address are NOT exposed
        $response->assertJsonMissing(['existing_name' => 'Secret Customer Name']);
        $response->assertJsonMissing(['existing_address' => 'Secret Villa Morjim Goa']);
    }

    public function test_verify_otp_returns_customer_info_only_after_valid_code(): void
    {
        Order::create([
            'order_number' => 'PAT-TEST-002',
            'customer_name' => 'Verified Customer',
            'customer_phone' => '9823000002',
            'delivery_address' => 'Morjim Beach Road',
            'total_amount' => 1200,
            'status' => 'approved',
            'reorder_token' => Str::random(24),
        ]);

        Cache::put('otp_9823000002', '1234', now()->addMinutes(10));

        // Wrong OTP should fail
        $wrongRes = $this->postJson(route('order.verifyOtp'), [
            'phone' => '9823000002',
            'otp' => '9999'
        ]);
        $wrongRes->assertStatus(422);

        // Correct OTP should succeed and return returning customer info
        $correctRes = $this->postJson(route('order.verifyOtp'), [
            'phone' => '9823000002',
            'otp' => '1234'
        ]);
        $correctRes->assertStatus(200);
        $correctRes->assertJsonFragment([
            'status' => 'success',
            'is_returning' => true,
            'existing_name' => 'Verified Customer',
            'existing_address' => 'Morjim Beach Road',
        ]);
    }

    public function test_verify_otp_locks_out_after_too_many_attempts(): void
    {
        Cache::put('otp_9823000003', '1234', now()->addMinutes(10));

        for ($i = 0; $i < 5; $i++) {
            $this->postJson(route('order.verifyOtp'), [
                'phone' => '9823000003',
                'otp' => '0000'
            ]);
        }

        // 6th attempt should be blocked with 429
        $blockedRes = $this->postJson(route('order.verifyOtp'), [
            'phone' => '9823000003',
            'otp' => '1234'
        ]);
        $blockedRes->assertStatus(429);
    }

    public function test_order_submission_requires_verified_phone_in_session(): void
    {
        $response = $this->postJson(route('order.store'), [
            'customer_phone' => '9823000004',
            'customer_name' => 'Hacker Name',
            'delivery_address' => 'Nowhere',
            'cart_items' => [
                ['name' => 'Jam', 'price' => 100, 'quantity' => 1]
            ]
        ]);

        // Should be rejected with 403 Forbidden because phone was not verified via OTP
        $response->assertStatus(403);
    }

    public function test_order_submission_succeeds_with_verified_session(): void
    {
        $response = $this->withSession([
            'verified_customer_phone' => '9823000005'
        ])->postJson(route('order.store'), [
            'customer_phone' => '9823000005',
            'customer_name' => 'Legit Customer',
            'delivery_address' => 'Morjim Goa',
            'cart_items' => [
                ['name' => 'Jam', 'price' => 100, 'quantity' => 2]
            ]
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['status' => 'success']);
        $this->assertDatabaseHas('orders', ['customer_phone' => '9823000005']);
    }

    public function test_idor_protection_on_load_reorder_endpoint(): void
    {
        $order = Order::create([
            'order_number' => 'PAT-SECRET-888',
            'customer_name' => 'Victim Customer',
            'customer_phone' => '9823999999',
            'delivery_address' => 'Private House',
            'total_amount' => 999,
            'status' => 'delivered',
            'reorder_token' => Str::random(24),
            'order_items' => json_encode([
                ['name' => 'Cashew Feni Flavour', 'price' => 500, 'quantity' => 2]
            ])
        ]);

        // Unauthenticated visitor querying another user's order
        $response = $this->getJson(route('api.reorder', ['orderNumber' => 'PAT-SECRET-888']));
        $response->assertStatus(403);

        // User with matching phone session can access
        $authResponse = $this->withSession([
            'customer_phone' => '9823999999'
        ])->getJson(route('api.reorder', ['orderNumber' => 'PAT-SECRET-888']));
        $authResponse->assertStatus(200);
    }

    public function test_token_reorder_route_resolves_cleanly(): void
    {
        $token = Str::random(24);
        $order = Order::create([
            'order_number' => 'PAT-REORDER-123',
            'customer_name' => 'Reorder Customer',
            'customer_phone' => '9823777777',
            'delivery_address' => 'Goa',
            'total_amount' => 250,
            'status' => 'delivered',
            'reorder_token' => $token,
        ]);

        $response = $this->get(route('order.reorder.token', ['token' => $token]));
        $response->assertRedirect(route('customer.account'));
        $this->assertEquals('9823777777', session('customer_phone'));
    }

    public function test_shop_and_catalog_render_cleanly_without_type_errors(): void
    {
        ProductCatalog::create([
            'product_name' => 'Kokum Agal',
            'slug' => 'kokum-agal',
            'category' => 'Kokum & Agal',
            'size' => '500ml',
            'price' => 50,
            'is_active' => true,
        ]);

        $resShop = $this->get('/shop');
        $resShop->assertStatus(200);
        $resShop->assertSee('Kokum & Agal');

        $resCatalog = $this->get('/catalog');
        $resCatalog->assertStatus(200);
    }

    public function test_product_catalog_image_url_resolves_reliably_with_safe_fallbacks(): void
    {
        $productWithImage = new ProductCatalog([
            'product_name' => 'Amala Squash',
            'image' => 'amala-squash.webp'
        ]);
        $this->assertStringContainsString('images/products/amala-squash.webp', $productWithImage->image_url);

        $productWithNull = new ProductCatalog([
            'product_name' => 'Unknown Product',
            'image' => null
        ]);
        $this->assertStringContainsString('images/products/default-product.webp', $productWithNull->image_url);

        $productWithDefault = new ProductCatalog([
            'product_name' => 'Default Product',
            'image' => 'default-product.webp'
        ]);
        $this->assertStringContainsString('images/products/default-product.webp', $productWithDefault->image_url);
    }

    public function test_pricing_service_resolves_mrp_for_guests_and_wholesale_for_wholesalers(): void
    {
        $product = ProductCatalog::create([
            'product_name' => 'Kokum Squash Test',
            'slug' => 'kokum-squash-test',
            'category' => 'Squashes',
            'size' => '1 Litre Bottle',
            'price' => 75.00,
            'mrp' => 200.00,
            'units_per_box' => 12,
            'is_active' => true,
        ]);

        // Guest visitor: sees MRP
        $this->assertFalse(\App\Services\PricingService::isWholesaler());
        $this->assertEquals(200.00, \App\Services\PricingService::getEffectivePrice($product));
        $this->assertEquals(2400.00, \App\Services\PricingService::getEffectiveBoxPrice($product));

        // Approved wholesaler user
        $wholesaler = User::create([
            'name' => 'Goa Distributors',
            'email' => 'goadist@example.com',
            'phone' => '9822112233',
            'is_wholesaler' => true,
            'password' => bcrypt('secret123'),
        ]);

        $this->actingAs($wholesaler);
        $this->assertTrue(\App\Services\PricingService::isWholesaler());
        $this->assertEquals(75.00, \App\Services\PricingService::getEffectivePrice($product));
        $this->assertEquals(900.00, \App\Services\PricingService::getEffectiveBoxPrice($product));
    }

    public function test_admin_can_toggle_customer_wholesaler_status(): void
    {
        $admin = User::create([
            'name' => 'Admin Boss',
            'email' => 'boss@patsonsgoa.com',
            'is_admin' => true,
            'password' => bcrypt('password123'),
        ]);

        $customer = User::create([
            'name' => 'Retail Customer',
            'email' => 'retail@example.com',
            'phone' => '9890001122',
            'is_wholesaler' => false,
            'password' => bcrypt('password123'),
        ]);

        $adminPath = env('ADMIN_SECRET_PATH', 'ps-control-morjim');
        $response = $this->actingAs($admin)->post("/{$adminPath}/users/{$customer->id}/toggle-wholesaler");
        $response->assertSessionHas('success');

        $customer->refresh();
        $this->assertTrue($customer->is_wholesaler);
    }

    public function test_customer_can_submit_wholesale_application(): void
    {
        $response = $this->withSession(['customer_phone' => '9823998877', 'customer_name' => 'Hotel Coastal'])
            ->post('/my-account/request-wholesale', [
                'business_name' => 'Hotel Coastal Retreat',
                'gstin' => '30ABCDE1234F1Z5',
            ]);

        $response->assertSessionHas('success');

        $user = User::where('phone', 'like', '%9823998877%')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->wholesale_requested);
        $this->assertEquals('Hotel Coastal Retreat', $user->business_name);
        $this->assertEquals('30ABCDE1234F1Z5', $user->gstin);
    }

    public function test_seeder_syncs_exact_price_sheet_sizes_and_deactivates_duplicates(): void
    {
        // Seed sample products matching real catalog
        ProductCatalog::create(['product_name' => 'Kokum Squash', 'slug' => 'kokum-squash-500', 'category' => 'Squashes', 'size' => '500ml Bottle', 'price' => 40, 'is_active' => true]);
        ProductCatalog::create(['product_name' => 'Kokum Squash', 'slug' => 'kokum-squash-500can', 'category' => 'Squashes', 'size' => '500ml Can', 'price' => 50, 'is_active' => true]);
        ProductCatalog::create(['product_name' => 'Kokum Squash', 'slug' => 'kokum-squash-1l', 'category' => 'Squashes', 'size' => '1 Litre Bottle', 'price' => 75, 'is_active' => true]);
        ProductCatalog::create(['product_name' => 'Kokum Squash', 'slug' => 'kokum-squash-5l', 'category' => 'Squashes', 'size' => '5 Litre Can', 'price' => 300, 'is_active' => true]);
        ProductCatalog::create(['product_name' => 'Sweet Kokam', 'slug' => 'sweet-kokam-100', 'category' => 'Kokum', 'size' => '100g Pack', 'price' => 40, 'is_active' => true]);
        ProductCatalog::create(['product_name' => 'Sweet Kokam', 'slug' => 'sweet-kokam-200', 'category' => 'Kokum', 'size' => '200g Pack', 'price' => 75, 'is_active' => true]);
        $sol = ProductCatalog::create(['product_name' => 'Kokum Sol (Raw / Regular)', 'slug' => 'kokum-sol', 'category' => 'Kokum', 'size' => 'Standard Pack', 'price' => 50, 'is_active' => false]);
        // Set id = 17 for kokum sol test
        $sol->id = 17;
        $sol->save();

        $seeder = new \Database\Seeders\ProductPricingSeeder();
        $seeder->run();

        // Kokum Squash should only have 500ml Bottle and 1 Litre Bottle active
        $activeSquashes = ProductCatalog::where('product_name', 'Kokum Squash')
            ->where('is_active', 1)
            ->pluck('size')
            ->toArray();
        $this->assertContains('500ml Bottle', $activeSquashes);
        $this->assertContains('1 Litre Bottle', $activeSquashes);
        $this->assertNotContains('500ml Can', $activeSquashes);
        $this->assertNotContains('5 Litre Can', $activeSquashes);

        // Sweet Kokam should only have 100g Pack active
        $activeSweetKokam = ProductCatalog::where('product_name', 'Sweet Kokam')
            ->where('is_active', 1)
            ->pluck('size')
            ->toArray();
        $this->assertContains('100g Pack', $activeSweetKokam);
        $this->assertNotContains('200g Pack', $activeSweetKokam);

        // Kokum Sol should be active at 250g Pack with MRP 500
        $kokumSol = ProductCatalog::where('product_name', 'Kokum Sol (Raw / Regular)')->first();
        $this->assertNotNull($kokumSol);
        $this->assertEquals(1, $kokumSol->is_active);
        $this->assertEquals(500.00, $kokumSol->mrp);
        $this->assertEquals('250g Pack', $kokumSol->size);
    }

    public function test_static_image_asset_route_serves_files_with_case_insensitive_fallback(): void
    {
        $response = $this->get('/images/products/amala-squash.webp');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/webp');

        $responseLogo = $this->get('/images/logo1.webp');
        $responseLogo->assertStatus(200);
        $responseLogo->assertHeader('Content-Type', 'image/webp');

        $responseFallback = $this->get('/images/products/non-existent-sample.webp');
        $responseFallback->assertStatus(200);
        $responseFallback->assertHeader('Content-Type', 'image/webp');
    }

    public function test_admin_quick_update_updates_price_and_mrp(): void
    {
        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@patsons.com',
            'is_admin' => true,
            'password' => bcrypt('password123'),
        ]);

        $item = ProductCatalog::create([
            'product_name' => 'Quick Edit Item',
            'slug' => 'quick-edit-item',
            'category' => 'Test',
            'size' => '500ml Bottle',
            'price' => 50.00,
            'mrp' => 100.00,
            'units_per_box' => 12,
            'is_active' => true,
        ]);
        $adminPath = env('ADMIN_SECRET_PATH', 'ps-control-morjim');

        $response = $this->actingAs($admin)->patchJson("/{$adminPath}/products/{$item->id}/quick-update", [
            'price' => 99.00,
            'mrp' => 189.00,
            'units_per_box' => 15,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $item->refresh();
        $this->assertEquals(99.00, $item->price);
        $this->assertEquals(189.00, $item->mrp);
        $this->assertEquals(15, $item->units_per_box);
    }

    public function test_seo_301_redirects_for_product_aliases(): void
    {
        $response = $this->get('/products/kokum-agal');
        $response->assertStatus(301);
        $response->assertRedirect('/product/kokum-agal');

        $responseCatalog = $this->get('/catalog/kokum-agal');
        $responseCatalog->assertStatus(301);
        $responseCatalog->assertRedirect('/product/kokum-agal');
    }

    public function test_admin_login_and_admin_prefix_return_404_not_found(): void
    {
        $this->get('/admin')->assertStatus(404);
        $this->get('/admin/login')->assertStatus(404);
        $this->get('/admin/dashboard')->assertStatus(404);
        $this->get('/admin/products')->assertStatus(404);

        // Only secret path ps-control-morjim is active
        $this->get('/ps-control-morjim/login')->assertStatus(200);
    }

    public function test_product_page_seo_metadata_and_schema(): void
    {
        $item = ProductCatalog::firstOrCreate([
            'slug' => 'test-seo-kokum',
        ], [
            'product_name' => 'Kokum Agal Pure',
            'category' => 'Kokum & Agal',
            'size' => '1 Litre Bottle',
            'price' => 150.00,
            'mrp' => 200.00,
            'units_per_box' => 12,
            'is_active' => true,
        ]);

        $response = $this->get('/product/test-seo-kokum');
        $response->assertStatus(200);
        $response->assertSee('Authentic Goan', false);
        $response->assertSee('FAQPage', false);
        $response->assertSee('BreadcrumbList', false);
        $response->assertSee('Authentic Goan Culinary Guide', false);
    }
}
