<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class IndexLogRequest extends FormRequest
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
            'page' => 'integer',
            'items_per_page' => 'integer',
            'sort_by' => ['nullable', Rule::in(['created_at'])],
            'sort_desc' => ['nullable', Rule::in(['true', 'false'])]
        ];
    }
}
