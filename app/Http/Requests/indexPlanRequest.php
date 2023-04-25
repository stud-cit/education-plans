<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class indexPlanRequest extends FormRequest
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
            'id' => 'numeric',
            'title' => 'string',
            'faculty' => 'integer',
            'department' => 'integer',
            'divisionWithStatus' => 'string',
            'planOrTemplate' => 'numeric',
            'page' => 'integer',
            'items_per_page' => 'integer',
            'sort_by' => ['nullable', Rule::in(['title', 'year', 'created_at', 'type_id'])],
            'sort_desc' => ['nullable', Rule::in(['true', 'false'])]
        ];
    }
}
