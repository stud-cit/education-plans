<?php

namespace App\Models;

use App\ExternalServices\Asu\Worker;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasAsuDivisionsNameTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CatalogSignature extends Model
{
    use HasFactory, HasAsuDivisionsNameTrait;

    protected $fillable = [
        'asu_id',
        'department_id',
        'faculty_id',
        'catalog_signature_type_id',
        'catalog_subject_id'
    ];

    public function getNameAttribute(): string
    {
        if ($this->asu_id === null) return '';

        $asu = new Worker();
        return $asu->getFirstLastNames($this->asu_id);
    }
}
