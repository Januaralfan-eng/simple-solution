<?php

namespace App\Http\Requests\Contact;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:100', 'min:2'],
            'email'    => ['required', 'email:rfc,dns', 'max:255'],
            'phone'    => ['nullable', 'string', 'max:20', 'regex:/^[+\d\s\-()]+$/'],
            'subject'  => ['nullable', 'string', 'max:200'],
            'message'  => ['required', 'string', 'min:20', 'max:3000'],
            'source'   => ['nullable', 'string', 'in:website,whatsapp,email,referral'],

            // Honeypot — bots fill this, humans don't
            '_website' => ['nullable', 'string', 'max:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'    => 'Please enter your name.',
            'email.required'   => 'Please enter your email address.',
            'email.email'      => 'Please enter a valid email address.',
            'message.required' => 'Please enter your message.',
            'message.min'      => 'Your message should be at least 20 characters.',
            '_website.max'     => 'Bot detected. Submission rejected.',
        ];
    }

    /**
     * Reject if honeypot is filled.
     */
    protected function prepareForValidation(): void
    {
        if ($this->filled('_website')) {
            abort(422, 'Spam detected.');
        }
    }
}
