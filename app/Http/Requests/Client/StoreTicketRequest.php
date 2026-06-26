<?php

declare(strict_types=1);

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('client')->check();
    }

    public function rules(): array
    {
        return [
            'subject' => 'required|string|max:255|min:5',
            'description' => 'required|string|max:5000|min:10',
            'category_id' => 'nullable|exists:ticket_categories,id',
            'priority' => 'nullable|in:low,normal,high,urgent',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,txt|max:10240',
        ];
    }

    public function messages(): array
    {
        return [
            'subject.required' => 'Please provide a ticket subject.',
            'subject.min' => 'Subject must be at least 5 characters.',
            'subject.max' => 'Subject cannot exceed 255 characters.',
            'description.required' => 'Please describe your issue in detail.',
            'description.min' => 'Description must be at least 10 characters.',
            'description.max' => 'Description cannot exceed 5000 characters.',
            'category_id.exists' => 'Invalid category selected.',
            'priority.in' => 'Invalid priority level.',
            'attachments.array' => 'Attachments must be an array.',
            'attachments.max' => 'You can upload a maximum of 5 files.',
            'attachments.*.file' => 'Each attachment must be a file.',
            'attachments.*.mimes' => 'Allowed file types: JPG, PNG, PDF, DOC, DOCX, XLS, XLSX, TXT.',
            'attachments.*.max' => 'Each file cannot exceed 10MB.',
        ];
    }

    public function attributes(): array
    {
        return [
            'subject' => 'Ticket Subject',
            'description' => 'Issue Description',
            'category_id' => 'Category',
            'priority' => 'Priority Level',
            'attachments' => 'Attachments',
        ];
    }
}
