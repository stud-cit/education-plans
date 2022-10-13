<?php

namespace App\Models;

use App\Observers\CatalogGroupObserver;
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

    protected static function booted()
    {
        CatalogGroup::observe(CatalogGroupObserver::class);
    }
}
