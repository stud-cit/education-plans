<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSignatureRequest extends FormRequest
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
            'plan_id' => 'required|exists:App\Models\Plan,id',
            'position_id' => 'required|exists:App\Models\Position,id',
            'manual_position' => 'required|string|max:255',
            'asu_id' => 'required|max:60',
        ];
    }
}
