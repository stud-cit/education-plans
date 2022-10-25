<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCatalogRequest extends FormRequest
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
            'year' => 'required|date_format:Y',
            'group_id' => 'required|exists:App\Models\CatalogGroup,id',
            'selective_discipline_id' => 'required|exists:App\Models\SelectiveDiscipline,id'
        ];
    }
}
