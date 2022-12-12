<?php

namespace App\Http\Requests\CatalogSubject;

use Illuminate\Foundation\Http\FormRequest;

class IndexCatalogSubjectRequest extends FormRequest
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
            'year' => 'integer',
            'group' => 'integer',
            'page' => 'integer',
            'items_per_page' => 'integer',
        ];
    }
}
