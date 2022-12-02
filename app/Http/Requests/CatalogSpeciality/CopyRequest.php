<?php

namespace App\Http\Requests\CatalogSpeciality;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CopyRequest extends FormRequest
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
        // make true message
        return [
            'year' => [
                'required', 'date_format:Y',
                Rule::unique('catalog_subjects')->where(function ($query) {
                    return $query->where('year', $this->year)
                        ->where('speciality_id', $this->speciality_id);
                })
            ],
            'speciality_id' => 'required|integer',
        ];
    }
}
