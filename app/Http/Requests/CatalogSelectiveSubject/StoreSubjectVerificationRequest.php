<?php

namespace App\Http\Requests\CatalogSelectiveSubject;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubjectVerificationRequest extends FormRequest
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
            'id' => 'nullable|exists:App\Models\SubjectVerification,id',
            'verification_status_id' => 'required|exists:App\Models\VerificationStatuses,id',
            'user_id' => 'required|exists:App\Models\User,id',
            'subject_id' => 'required|exists:App\Models\CatalogSelectiveSubject,id',
            'status' => 'required',
            'comment' => 'nullable|string|max:255'
        ];
    }
}
