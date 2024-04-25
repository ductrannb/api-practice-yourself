<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\CustomRuleUnique;
use Illuminate\Validation\Rule;

class UserRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required', 'string', 'email', 'max:255',
                new CustomRuleUnique('users', id: $this->route('user'))
            ],
            'password' => 'required|string|min:6',
            'role_id' => ['required', Rule::in([User::ROLE_USER, User::ROLE_TEACHER])],
            'phone' => 'nullable|string|min:10|max:12',
        ];
    }
}
