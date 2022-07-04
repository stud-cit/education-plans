<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCycleRequest extends FormRequest
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
            'cycle_id' => 'exists:App\Models\Cycle,id',
            'list_cycle_id' => 'required|exists:App\Models\ListCycle,id',
            'plan_id' => 'exists:App\Models\Plan,id',
            'has_discipline' => 'boolean'
        ];
    }
}
