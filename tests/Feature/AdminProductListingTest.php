<?php

namespace Tests\Feature;

use App\Models\ProductCatalog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProductListingTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create([
            'is_admin' => 1,
        ]);
    }

    public function test_admin_can_view_products_listing_with_kpi_stats(): void
    {
        ProductCatalog::create([
            'product_name' => 'Kokum Syrup Premium',
            'slug' => 'kokum-syrup-premium',
            'category' => 'Syrup',
            'size' => '750ml Bottle',
            'price' => 140.00,
            'units_per_box' => 12,
            'is_active' => true,
            'in_stock' => true,
            'is_featured' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.products'));

        $response->assertStatus(200);
        $response->assertSee('Product Catalog & Wholesale Inventory', false);
        $response->assertSee('Kokum Syrup Premium');
        $response->assertSee('Export CSV');
    }

    public function test_admin_can_export_catalog_to_csv(): void
    {
        ProductCatalog::create([
            'product_name' => 'Cashew Apple Squash',
            'slug' => 'cashew-apple-squash',
            'category' => 'Squash',
            'size' => '1 Litre',
            'price' => 180.00,
            'units_per_box' => 6,
            'is_active' => true,
            'in_stock' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.products.export'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('Cashew Apple Squash', $response->streamedContent());
        $this->assertStringContainsString('Squash', $response->streamedContent());
    }

    public function test_admin_can_ajax_toggle_product_status(): void
    {
        $product = ProductCatalog::create([
            'product_name' => 'Mango Agal Extract',
            'slug' => 'mango-agal-extract',
            'category' => 'Extract',
            'size' => '500ml',
            'price' => 110.00,
            'units_per_box' => 12,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)
            ->json('PATCH', route('admin.products.toggleStatus', $product->id));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'is_active' => false,
        ]);

        $this->assertDatabaseHas('product_catalog', [
            'id' => $product->id,
            'is_active' => 0,
        ]);
    }

    public function test_admin_can_ajax_toggle_product_stock(): void
    {
        $product = ProductCatalog::create([
            'product_name' => 'Jeera Soda Cordial',
            'slug' => 'jeera-soda-cordial',
            'category' => 'Cordial',
            'size' => '500ml',
            'price' => 90.00,
            'units_per_box' => 24,
            'in_stock' => true,
        ]);

        $response = $this->actingAs($this->admin)
            ->json('PATCH', route('admin.products.toggleStock', $product->id));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'in_stock' => false,
        ]);

        $this->assertDatabaseHas('product_catalog', [
            'id' => $product->id,
            'in_stock' => 0,
        ]);
    }

    public function test_admin_can_ajax_toggle_product_featured(): void
    {
        $product = ProductCatalog::create([
            'product_name' => 'Triphala Juice',
            'slug' => 'triphala-juice',
            'category' => 'Herbal',
            'size' => '500ml',
            'price' => 150.00,
            'units_per_box' => 12,
            'is_featured' => false,
        ]);

        $response = $this->actingAs($this->admin)
            ->json('PATCH', route('admin.products.toggleFeatured', $product->id));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'is_featured' => true,
        ]);

        $this->assertDatabaseHas('product_catalog', [
            'id' => $product->id,
            'is_featured' => 1,
        ]);
    }

    public function test_admin_can_quick_update_variant_price_and_box_units(): void
    {
        $product = ProductCatalog::create([
            'product_name' => 'Ginger Lime Cordial',
            'slug' => 'ginger-lime-cordial',
            'category' => 'Cordial',
            'size' => '750ml',
            'price' => 120.00,
            'units_per_box' => 12,
        ]);

        $response = $this->actingAs($this->admin)
            ->json('PATCH', route('admin.products.quickUpdate', $product->id), [
                'price' => 135.50,
                'units_per_box' => 15,
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'price' => '135.50',
            'units_per_box' => 15,
        ]);

        $this->assertDatabaseHas('product_catalog', [
            'id' => $product->id,
            'price' => 135.50,
            'units_per_box' => 15,
        ]);
    }

    public function test_admin_can_duplicate_product_variant(): void
    {
        $product = ProductCatalog::create([
            'product_name' => 'Kokum Agal Classic',
            'slug' => 'kokum-agal-classic',
            'category' => 'Extract',
            'size' => '500ml Bottle',
            'price' => 110.00,
            'units_per_box' => 12,
            'description' => '100% natural Goan kokum extract.',
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.products.duplicate', $product->id));

        $response->assertStatus(302);
        $this->assertDatabaseHas('product_catalog', [
            'product_name' => 'Kokum Agal Classic (Copy)',
            'category' => 'Extract',
            'is_active' => 0,
        ]);
    }

    public function test_admin_can_perform_bulk_actions(): void
    {
        $p1 = ProductCatalog::create([
            'product_name' => 'Bulk Product 1',
            'slug' => 'bulk-product-1',
            'category' => 'Squash',
            'size' => '500ml',
            'price' => 100.00,
            'units_per_box' => 12,
            'is_active' => false,
            'in_stock' => false,
        ]);

        $p2 = ProductCatalog::create([
            'product_name' => 'Bulk Product 2',
            'slug' => 'bulk-product-2',
            'category' => 'Squash',
            'size' => '500ml',
            'price' => 100.00,
            'units_per_box' => 12,
            'is_active' => false,
            'in_stock' => false,
        ]);

        // Bulk activate
        $response = $this->actingAs($this->admin)
            ->json('POST', route('admin.products.bulkAction'), [
                'action' => 'activate',
                'slugs' => ['bulk-product-1', 'bulk-product-2'],
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('product_catalog', ['slug' => 'bulk-product-1', 'is_active' => 1]);
        $this->assertDatabaseHas('product_catalog', ['slug' => 'bulk-product-2', 'is_active' => 1]);

        // Bulk mark in stock
        $response2 = $this->actingAs($this->admin)
            ->json('POST', route('admin.products.bulkAction'), [
                'action' => 'mark_in_stock',
                'slugs' => ['bulk-product-1', 'bulk-product-2'],
            ]);

        $response2->assertStatus(200);
        $this->assertDatabaseHas('product_catalog', ['slug' => 'bulk-product-1', 'in_stock' => 1]);
        $this->assertDatabaseHas('product_catalog', ['slug' => 'bulk-product-2', 'in_stock' => 1]);
    }
}
