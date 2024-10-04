<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{

    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'document' => 'required|string|max:20|unique:users',
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip' => 'nullable|string|max:20',
        ];
    }

    public function messages() {
        return [
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Email is invalid',
            'email.unique' => 'Email is already taken',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 8 characters',
            'password.confirmed' => 'Passwords do not match',
            'document.required' => 'Document is required',
            'document.unique' => 'Document is already taken',
            'phone.max' => 'Phone number must not exceed 15 characters',
            'address.max' => 'Address must not exceed 255 characters',
            'city.max' => 'City name must not exceed 100 characters',
            'state.max' => 'State name must not exceed 100 characters',
            'zip.max' => 'ZIP code must not exceed 20 characters',
        ];
    }
}
