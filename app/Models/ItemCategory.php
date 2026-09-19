<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemCategory extends Model
{
    protected $table = 'kb_item_categories';
    protected $primaryKey = 'item_category_id';
    public $timestamps = false;

    public function items()
    {
        return $this->hasMany(Item::class, 'category_id', 'item_category_id');
    }
}
