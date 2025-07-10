<?php

namespace Arden28\Guardian\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignPermissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check() && auth()->user()->hasPermissionTo('manage_permissions', 'api');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,id',
            'permission' => 'required|string',
            'guard' => 'nullable|string|in:' . implode(',', config('guardian.roles.guards', [])),
        ];
    }
}