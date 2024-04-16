<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCycleRequest extends FormRequest
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
            'credit' => 'numeric|digits_between:1,3',
            'has_discipline' => 'boolean',
            'list_cycle_id' => 'exists:App\Models\ListCycle,id'
        ];
    }
}
