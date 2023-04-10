<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemType extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'description',
        'created_by',
        'updated_by'
    ];

    public function items()
    {
        return $this->hasMany(Item::class);
    }

}
