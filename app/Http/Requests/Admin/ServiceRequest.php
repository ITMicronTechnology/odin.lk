<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return (bool) $this->user()?->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $serviceId = $this->route('service')?->id;

        return [
            'service_category_id' => ['required', 'integer', 'exists:service_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('services')->ignore($serviceId)],
            'icon' => ['nullable', 'string', 'max:50', 'regex:/^bi-[a-z0-9-]+$/'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:20000'],
            'features' => ['nullable', 'string', 'max:5000'],
            'starting_price' => ['nullable', 'numeric', 'min:0', 'max:99999999'],
            'price_note' => ['nullable', 'string', 'max:50'],
            'delivery_time' => ['nullable', 'string', 'max:50'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_featured' => ['boolean'],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'icon.regex' => 'Use a Bootstrap icon class name such as bi-globe2.',
        ];
    }

    /**
     * The "What's included" box, one feature per line.
     *
     * @return list<string>
     */
    public function features(): array
    {
        return collect(preg_split('/\r\n|\r|\n/', (string) $this->input('features')))
            ->map(fn (string $line): string => trim($line, " \t-•"))
            ->filter()
            ->values()
            ->all();
    }
}
