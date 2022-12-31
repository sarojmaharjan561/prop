<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'type_id',
        'name',
        'last_price',
        'description',
        'created_by',
        'updated_by'
    ];

    // public function itemtType(){
    //     return $this->hasOne(ItemType::class, 'type_id');
    // }

    public function itemType()
    {
        return $this->belongsTo(ItemType::class,'type_id');
    }
}
