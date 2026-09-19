<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Item extends Model
{
    protected $table = 'kb_items';
    protected $primaryKey = 'item_id';
    public $timestamps = false;

    // Scope to get active catalog items
    public function scopeActive($query)
    {
        return $query->where('item_is_active', 1);
    }

    // SEO-friendly slug
    public function getSlugAttribute()
    {
        return Str::slug($this->item_name) . '-' . $this->item_id;
    }

    public function category()
    {
        return $this->belongsTo(ItemCategory::class, 'category_id', 'item_category_id');
    }
}