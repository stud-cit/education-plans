<?php

namespace App\Models;

use App\Helpers\Filters\FilterBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubjectHelper extends Model
{
    use HasFactory;

    public function type()
    {
        return $this->belongsTo(CatalogHelperType::class, 'catalog_helper_type_id', 'id')
            ->select('id','title');
    }
    public function scopeFilterBy($query, $filters)
    {
        $namespace = 'App\Helpers\Filters\SubjectHelperFilters';
        $filter = new FilterBuilder($query, $filters, $namespace);

        return $filter->apply();
    }
}
