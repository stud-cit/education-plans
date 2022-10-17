<?php

namespace App\Http\Requests;

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
            'position_id' => 'required|exists:App\Models\Position,id',
            'plan_id' => 'required|exists:App\Models\Plan,id',
            'manual_position' => 'nullable|string|max:255',
            'asu_id' => 'nullable|max:60',
        ];
    }
}
