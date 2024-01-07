<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required|min:6',
        ];
        if ($this->method() === 'PUT') {
            $rules = [
                'email' => 'required|unique:users,email,' . $this->user->id,
                'password' => 'sometimes|min:6',
            ];
        }
        return $rules;
    }
}
