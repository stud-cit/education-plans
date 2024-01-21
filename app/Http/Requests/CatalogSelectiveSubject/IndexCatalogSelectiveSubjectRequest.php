<?php

namespace App\Http\Requests\CatalogSelectiveSubject;

use Illuminate\Foundation\Http\FormRequest;

class IndexCatalogSelectiveSubjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'year' => 'required|integer',
            'group' => 'integer',
            'faculty' => 'integer',
            'department' => 'integer',
            'divisionWithStatus' => 'string',
            'page' => 'integer',
            'items_per_page' => 'integer',
        ];
    }
}
