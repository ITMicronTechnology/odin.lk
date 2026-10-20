<?php

namespace App\Http\Requests;

use App\Models\ServiceInquiry;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServiceInquiryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'service_id' => ['nullable', 'integer', Rule::exists('services', 'id')->where('is_active', true)],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^[0-9+\s-]{9,15}$/'],
            'company' => ['nullable', 'string', 'max:255'],
            'budget' => ['nullable', Rule::in(ServiceInquiry::BUDGETS)],
            'timeline' => ['nullable', Rule::in(ServiceInquiry::TIMELINES)],
            'message' => ['required', 'string', 'min:10', 'max:3000'],
            // Hidden "honeypot" field: real people leave it empty, spam bots fill it in.
            'website' => ['prohibited'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'phone.regex' => 'Please enter a valid phone number, e.g. 077 123 4567.',
            'message.min' => 'Please tell us a little more about what you need (at least 10 characters).',
        ];
    }
}
