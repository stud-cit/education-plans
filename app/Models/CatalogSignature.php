<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogSignature extends Model
{
    use HasFactory;

    protected $fillable = [
        'asu_id',
        'department_id',
        'faculty_id',
        'catalog_signature_type_id',
        'catalog_subject_id'
    ];
}
