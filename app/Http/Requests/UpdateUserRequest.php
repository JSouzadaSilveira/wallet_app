<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{

    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        $userId = $this->route('id');
        return [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $userId,
            'document' => 'sometimes|required|string|max:20|unique:users,document,' . $userId,
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip' => 'nullable|string|max:20',
        ];
    }

    public function messages() {
        return [
            'name.required' => 'The name is required.',
            'email.required' => 'The email is required.',
            'email.unique' => 'The email is already in use.',
            'document.required' => 'The document is required.',            
        ];
    }
}
