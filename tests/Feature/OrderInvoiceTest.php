<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderInvoiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_printable_delivery_challan_and_invoice(): void
    {
        $admin = User::factory()->create([
            'is_admin' => 1,
        ]);

        $order = Order::create([
            'order_number' => 'PAT-TEST-001',
            'customer_name' => 'Goa Beach Resort & Bar',
            'customer_phone' => '9876543210',
            'delivery_address' => 'Morjim Beach Road, Goa',
            'total_amount' => 1200.00,
            'status' => 'approved',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_name' => 'Kokum Agal Pure Extract',
            'size' => '750ml Box of 12',
            'unit_price' => 100.00,
            'quantity' => 12,
            'subtotal' => 1200.00,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.orders.invoice', $order->id));

        $response->assertStatus(200);
        $response->assertSee('DEV CAN FRUIT PRODUCTS');
        $response->assertSee('PAT-TEST-001');
        $response->assertSee('10613001000730'); // FSSAI
        $response->assertSee('30BAGPP8577A1ZS'); // GSTIN
        $response->assertSee('Delivery Challan & Tax Invoice', false);
    }
}
