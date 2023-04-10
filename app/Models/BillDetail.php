<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_id',
        'item_id',
        'rate',
        'quantity',
        'amount',
        'created_by',
        'updated_by',
    ];

    public function items()
    {
        return $this->belongsTo(Item::class,'item_id');
    }
}
