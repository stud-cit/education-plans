<?php

namespace App\Http\Requests\CatalogSubject;

use Illuminate\Foundation\Http\FormRequest;

class PdfCatalogSubjectRequest extends FormRequest
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
            'group_id' => 'required|integer',
        ];
    }

    public function messages()
    {
        return [
            'year.date_format' => 'Дана вказана в невірному форматі (приклад правильної дати 2022).'
        ];
    }
}