<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OwnerCatalogSubject extends Model
{
    use HasFactory;

    protected $table = 'owner_catalog_subjects';

    protected $fillable = [
        'catalog_subject_id',
        'department_id',
    ];
}
