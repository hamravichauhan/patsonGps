<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_phone',
        'delivery_address',
        'total_amount',
        'status',
        'admin_notes',
        'reorder_token',
        'order_items', // Added for backward compatibility
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }
}