<?php

namespace App\Http\Requests;

use App\Models\SubjectHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UpdateSubjectHelperRequest extends FormRequest
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
    public function rules(Request $request)
    {
        return [
            'id' => 'required|numeric|integer',
            'type' => 'required|numeric|integer',
            'title' =>  [
                'required',
                Rule::unique('subject_helpers')->where(function ($query) use ($request) {
                    return $query->where('title', $request->title)->where('catalog_helper_type_id', $request->type);
                })
            ],
        ];
    }
}
