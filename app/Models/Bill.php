<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'type_id',
        'shop_name',
        'date',
        'paid_date',
        'sub_total',
        'discount',
        'total_amount',
        'description',
        'created_by',
        'updated_by'
    ];
}
