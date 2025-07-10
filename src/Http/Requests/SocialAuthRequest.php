<?php

namespace Arden28\Guardian\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SocialAuthRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Public endpoint
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // Only apply Telegram-specific validation for Telegram provider
        if (request()->route('provider') === 'telegram') {
            return [
                'id' => 'required|numeric',
                'first_name' => 'required|string',
                'last_name' => 'nullable|string',
                'username' => 'nullable|string',
                'photo_url' => 'nullable|string',
                'auth_date' => 'required|numeric',
                'hash' => 'required|string',
            ];
        }

        return []; // No validation for other providers' callbacks
    }
}