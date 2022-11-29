<?php

namespace App\Http\Requests\CatalogSpecialization;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'year' => 'required|date_format:Y|unique:catalog_subjects,year,specialization_id',
            'specialization_id' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'year.unique' => 'Рік та спеціалізація вже існує.',
        ];
    }
}
