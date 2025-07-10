<?php

namespace Arden28\Guardian\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TwoFactorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check(); // Requires authenticated user
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'method' => 'required|in:email,sms,totp',
            'code' => 'required_if:method,totp|required_if:method,email|required_if:method,sms|string|min:6|max:6',
            'phone_number' => 'required_if:method,sms|string',
        ];
    }
}