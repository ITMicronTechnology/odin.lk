<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
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
        $productId = $this->route('product')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('products')->ignore($productId)],
            'sku' => ['required', 'string', 'max:100', Rule::unique('products')->ignore($productId)],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'brand_id' => ['nullable', 'integer', 'exists:brands,id'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:20000'],
            'specifications' => ['nullable', 'string', 'max:5000'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999999'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'stock' => ['required', 'integer', 'min:0', 'max:1000000'],
            'warranty' => ['nullable', 'string', 'max:100'],
            'is_featured' => ['boolean'],
            'is_active' => ['boolean'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'sale_price.lt' => 'The sale price must be lower than the regular price.',
            'images.*.max' => 'Each image must be 4 MB or smaller.',
        ];
    }

    /**
     * Turn the "Key: Value" lines typed in the specifications box into an array.
     *
     * @return array<string, string>
     */
    public function specifications(): array
    {
        return collect(preg_split('/\r\n|\r|\n/', (string) $this->input('specifications')))
            ->map(fn (string $line): array => array_map('trim', explode(':', $line, 2)))
            ->filter(fn (array $parts): bool => count($parts) === 2 && $parts[0] !== '' && $parts[1] !== '')
            ->mapWithKeys(fn (array $parts): array => [$parts[0] => $parts[1]])
            ->all();
    }
}
