<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubjectRequest extends FormRequest
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
            'plan_id' => 'required',
            'asu_id' => 'nullable',
            'cycle_id' => 'required|exists:App\Models\Cycle,id',
            'credits' => 'required|numeric|digits_between:1,3',
            'hours' => 'numeric',
            'practices' => 'numeric',
            'laboratories' => 'numeric',
            'selective_discipline_id' => 'nullable',
            'faculty_id' => 'nullable',
            'department_id' => 'nullable',
            'note' => 'nullable',
            'selectiveDiscipline' => 'boolean',

            'hours_modules.*.course' => 'required|numeric',
            'hours_modules.*.form_control_id' => 'required|numeric',
            'hours_modules.*.hour' => 'required|numeric',
            'hours_modules.*.individual_task_id' => 'required|numeric',
            'hours_modules.*.module' => 'required|numeric',
            'hours_modules.*.semester' => 'required|numeric',

            'semesters_credits.*.course' => 'required|numeric',
            'semesters_credits.*.credit' => 'required|numeric',
            'semesters_credits.*.semester' => 'required|numeric',

        ];
    }
}
