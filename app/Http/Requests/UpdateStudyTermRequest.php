<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStudyTermRequest extends FormRequest
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
            'id' => Rule::unique('study_terms', 'id')->ignore($this->id),
            'title' => [
                'required',
                Rule::unique('study_terms', 'title')->ignore($this->id),
                'string',
                'max:255',
            ],
            'year' => [
                'required',
                'numeric',
                Rule::unique('study_terms')->where(function ($query) {
                    return $query->where([
                        ['year', $this->year],
                        ['month', $this->month],
                    ]);
                })->ignore($this->id)
            ],
            'month' => 'required|numeric',
            'course' => 'required|numeric',
            'module' => 'required|numeric',
            'semesters' => 'required|numeric',
        ];
    }
}
