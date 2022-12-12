<?php

namespace App\Http\Requests\CatalogEducationProgram;

use Illuminate\Foundation\Http\FormRequest;

class PdfCatalogRequest extends FormRequest
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
            'catalog_id' => 'required_if:year,null',
            'year' => 'required_if:catalog_id,null',
            'education_program_id' => 'required_if:catalog_id,null',
        ];
    }
}
