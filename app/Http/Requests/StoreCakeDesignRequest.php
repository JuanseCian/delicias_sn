<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCakeDesignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $configuration = $this->input('configuration');

        if (is_string($configuration)) {
            $decoded = json_decode($configuration, true);
            $configuration = is_array($decoded) ? $decoded : [];
        }

        $this->merge(['configuration' => $configuration]);
    }

    public function rules(): array
    {
        return [
            'customer_name' => ['nullable', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:40'],
            'prompt' => ['nullable', 'string', 'max:2000'],
            'configuration' => ['required', 'array'],
            'configuration.size_id' => ['required', 'integer'],
            'configuration.coverage_id' => ['required', 'integer'],
            'configuration.color_ids' => ['required', 'array', 'min:1', 'max:2'],
            'configuration.color_ids.*' => ['integer'],
            'configuration.topping_ids' => ['nullable', 'array', 'max:8'],
            'configuration.topping_ids.*' => ['integer'],
            'configuration.template_id' => ['nullable', 'integer'],
        ];
    }
}
