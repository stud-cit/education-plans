<?php

namespace App\Http\Requests\Plan;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ShortPlanRequest extends FormRequest
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
            'shortened_by_year' => [
                'required',
                'integer',
                'min:1',
                'max:2',
                Rule::unique('shortened_plans')->where(function ($query) {
                    return $query->where('parent_id', $this->plan->id)
                        ->where('shortened_by_year', $this->shortened_by_year);
                })

            ],
        ];
    }

    public function messages()
    {
        return [
            'shortened_by_year.unique' => 'Такий скорочений план вже існує.',
        ];
    }
}
