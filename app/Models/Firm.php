<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Firm extends Model
{
    protected $table = 'kb_firms';
    protected $primaryKey = 'firm_id';
    public $timestamps = false;
}
