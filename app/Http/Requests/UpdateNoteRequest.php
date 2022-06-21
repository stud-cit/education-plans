<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateNoteRequest extends FormRequest
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
            'abbreviation' => [
                'required',
                'string',
                Rule::unique('notes')->ignore($this->abbreviation, 'abbreviation'),
                'max:7'
            ],
            'explanation' => 'required|string|max:255'
        ];
    }
}
