<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubjectRequest extends FormRequest
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
            'asu_id' => 'nullable', 
            'cycle_id' => 'required|exists:App\Models\Cycle,id',
            'credits' => 'required|numeric|digits_between:1,3',
            'hours' => 'numeric',
            'practices' => 'numeric',
            'laboratories' => 'numeric',
            'selective_discipline_id' => 'nullable',
            'faculty_id' => 'nullable',
            'department_id' => 'nullable'
        ];
    }
}
