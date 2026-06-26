<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('create_clients');
    }

    public function rules(): array
    {
        return [
            'client_name' => 'required|string|max:150|min:2',
            'client_email' => 'nullable|email|max:150|unique:clients,client_email',
            'client_address' => 'nullable|string|max:255',
            'client_contact' => 'nullable|string|max:20|regex:/^[0-9\s\-\+\(\)]*$/',
            'client_representative' => 'nullable|string|max:150',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'client_name.required' => 'Client name is required.',
            'client_name.min' => 'Client name must be at least 2 characters.',
            'client_name.max' => 'Client name cannot exceed 150 characters.',
            'client_email.email' => 'Please provide a valid email address.',
            'client_email.unique' => 'This email address is already registered.',
            'client_email.max' => 'Email cannot exceed 150 characters.',
            'client_contact.regex' => 'Contact number contains invalid characters.',
            'client_address.max' => 'Address cannot exceed 255 characters.',
            'client_representative.max' => 'Representative name cannot exceed 150 characters.',
        ];
    }

    public function attributes(): array
    {
        return [
            'client_name' => 'Client Name',
            'client_email' => 'Email Address',
            'client_address' => 'Address',
            'client_contact' => 'Contact Number',
            'client_representative' => 'Representative Name',
            'is_active' => 'Active Status',
        ];
    }
}
