<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillType extends Model
{
    use HasFactory;
    protected $fillable = [
        'type',
        'description',
        'created_by',
        'updated_by'
    ];
}
