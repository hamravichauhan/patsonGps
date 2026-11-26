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
}
