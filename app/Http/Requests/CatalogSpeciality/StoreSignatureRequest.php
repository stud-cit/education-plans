<?php

namespace App\Http\Requests\CatalogSpeciality;

use Illuminate\Foundation\Http\FormRequest;

class StoreSignatureRequest extends FormRequest
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
            'signatures' => 'required|array'
//            'signatures.*.faculty_id' => 'required|numeric',
//            'signatures.*.department_id' => 'required|numeric',
//            'signatures.*.catalog_subject_id' => 'required|numeric',
//            'signatures.*.catalog_signature_type_id' => 'required|numeric',
//            'signatures.*.asu_id' => 'required|string',
        ];
    }
}
