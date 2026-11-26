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
        'in_stock',
        'image_status',
        'auto_approve_override',
        'auto_approve_min_qty',
        'vyapar_item_id',
    ];

    protected $casts = [
        'images' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'in_stock' => 'boolean',
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
            return asset('images/products/default-product.webp');
        }

        if (Str::startsWith($raw, ['http://', 'https://'])) {
            return $raw;
        }

        // 1. Direct storage upload check
        if (file_exists(public_path('storage/' . $raw))) {
            return asset('storage/' . $raw);
        }

        // 2. Standard products directory (case-insensitive & cPanel-aware fallback)
        if (
            file_exists(public_path('images/products/' . $raw)) ||
            file_exists(public_path('images/Products/' . $raw)) ||
            file_exists(base_path('../../public_html/images/products/' . $raw)) ||
            file_exists(base_path('../public_html/images/products/' . $raw)) ||
            (isset($_SERVER['DOCUMENT_ROOT']) && file_exists(rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/images/products/' . $raw))
        ) {
            return asset('images/products/' . $raw);
        }

        // 3. Root images folder check
        if (
            file_exists(public_path('images/' . $raw)) ||
            file_exists(base_path('../../public_html/images/' . $raw)) ||
            (isset($_SERVER['DOCUMENT_ROOT']) && file_exists(rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/images/' . $raw))
        ) {
            return asset('images/' . $raw);
        }

        if (file_exists(public_path($raw))) {
            return asset($raw);
        }

        // 4. Default live catalog fallback (all storefront images exist at /images/products/)
        return asset('images/products/' . $raw);
    }


    public function getBoxPriceAttribute(): float
    {
        $units = $this->units_per_box ?: 12;
        return (float) ($this->price * $units);
    }
}