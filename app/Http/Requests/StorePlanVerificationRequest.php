<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePlanVerificationRequest extends FormRequest
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
            'verification_status_id' => 'exists:App\Models\VerificationStatuses,id',
            'user_id' => 'exists:App\Models\User,id',
            'plan_id' => 'exists:App\Models\Plan,id',
            'status' => 'required',
            'comment' => 'string|nullable'
        ];
    }
}
