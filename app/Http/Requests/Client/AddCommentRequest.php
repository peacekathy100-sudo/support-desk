<?php

declare(strict_types=1);

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class AddCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('client')->check();
    }

    public function rules(): array
    {
        return [
            'message' => 'required|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'message.required' => 'Please enter a message',
        ];
    }
}
