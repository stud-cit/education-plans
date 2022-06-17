<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
class StorePositionRequest extends FormRequest
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
            'agreed' => 'nullable|boolean',
            'position' => 'required|unique:App\Models\Position,position|max:255'
        ];
    }
}
