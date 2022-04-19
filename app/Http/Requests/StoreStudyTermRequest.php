<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudyTermRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'year' => 'required|numeric',
            'month' => 'required|numeric',
            'course' => 'required|numeric',
            'module' => 'required|numeric',
            'number_semesters' => 'required|numeric',
        ];
    }
}
