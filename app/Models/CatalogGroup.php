<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CatalogGroup extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['title'];

    protected $casts = [
        'deleted_at' => 'boolean'
    ];
}
