<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductCatalog extends Model
{
    use HasFactory;

    protected $table = 'product_catalog';

    public $timestamps = false;

    protected $fillable = [
        'product_name',
        'slug',
        'category',
        'size',
        'price',
        'units_per_box',
        'description',
        'image',
        'images',
        'is_featured',
        'is_active',
        'image_status',
        'auto_approve_override',
        'auto_approve_min_qty',
    ];

    protected $casts = [
        'images' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'auto_approve_override' => 'boolean',
        'price' => 'decimal:2',
        'units_per_box' => 'integer',
    ];

    /**
     * Scope query to active/live products for client-side storefront
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_active', 1);
    }

    /**
     * Compute effective image status: manual tag if set, else auto-detect
     */
    public function getEffectiveStatusAttribute()
    {
        if (!empty($this->image_status) && $this->image_status !== 'auto') {
            return $this->image_status;
        }

        $raw = $this->image;
        if (empty($raw) || $raw === 'default-product.webp') {
            return 'default';
        }

        $cleanSlug = str_replace('-', '', strtolower($this->slug));
        $cleanImgName = strtolower(basename($raw));
        if (!Str::contains($cleanImgName, substr($cleanSlug, 0, 7))) {
            return 'mismatched';
        }

        return 'matched';
    }

    /**
     * Helper accessor for primary image URL with multi-folder fallback
     */
    public function getImageUrlAttribute()
    {
        $raw = $this->image;

        if (empty($raw) || $raw === 'default-product.webp') {
            return asset('images/default-product.webp');
        }

        if (Str::startsWith($raw, ['http://', 'https://'])) {
            return $raw;
        }

        if (file_exists(public_path('storage/' . $raw))) {
            return asset('storage/' . $raw);
        }

        if (file_exists(public_path('images/products/' . $raw))) {
            return asset('images/products/' . $raw);
        }

        if (file_exists(public_path('images/' . $raw))) {
            return asset('images/' . $raw);
        }

        if (file_exists(public_path($raw))) {
            return asset($raw);
        }

        return asset('storage/' . $raw);
    }


    public function getBoxPriceAttribute(): float
    {
        $units = $this->units_per_box ?: 12;
        return (float) ($this->price * $units);
    }
}