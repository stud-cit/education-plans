<?php

namespace App\Http\Requests\CatalogSpeciality;

use Illuminate\Foundation\Http\FormRequest;

class StoreCatalogSpecialityVerificationRequest extends FormRequest
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
            'status' => 'required',
            'user_id' => 'required|exists:App\Models\User,id',
            'verification_status_id' => 'required|exists:App\Models\VerificationStatuses,id',

            'id' => 'nullable|exists:App\Models\CatalogVerification,id',
            'catalog_id' => 'required|exists:App\Models\CatalogSpeciality,id',
            'comment' => 'nullable|required_if:status,false|string|max:255'
        ];
    }
}
