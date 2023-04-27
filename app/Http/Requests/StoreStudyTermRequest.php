<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
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
            'title' => 'required|unique:study_terms|string|max:255',
            'year' => [
                'required',
                'numeric',
                Rule::unique('study_terms')->where(function ($query) {
                    return $query->where([
                        ['year', $this->year],
                        ['month', $this->month],
                    ]);
                })
            ],
            'month' => 'required|numeric',
            'course' => 'required|numeric',
            'module' => 'required|numeric',
            'semesters' => 'required|numeric',
        ];
    }
}
