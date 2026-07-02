<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100'],
            // Український мобільний: +380XXXXXXXXX, 380XXXXXXXXX або 0XXXXXXXXX
            'phone' => ['required', 'string', 'regex:/^(\+?38)?0\d{9}$/'],
            'comment' => ['nullable', 'string', 'max:1000'],
            'product_id' => [
                'nullable',
                'integer',
                Rule::exists('products', 'id')->where('is_active', true),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Вкажіть, як до вас звертатися.',
            'name.min' => 'Ім’я закоротке.',
            'phone.required' => 'Без телефону ми не додзвонимось :)',
            'phone.regex' => 'Введіть номер у форматі 0XX XXX XX XX.',
            'comment.max' => 'Коментар задовгий (до 1000 символів).',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Прибираємо пробіли, дужки й дефіси з телефону перед перевіркою
        if ($this->filled('phone')) {
            $this->merge([
                'phone' => preg_replace('/[\s\(\)\-]+/', '', (string) $this->input('phone')),
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function orderData(): array
    {
        $data = $this->safe()->only(['name', 'phone', 'comment', 'product_id']);

        // Зводимо телефон до єдиного формату +380XXXXXXXXX
        $digits = ltrim(preg_replace('/\D/', '', $data['phone']), '0');
        $data['phone'] = '+380'.substr($digits, -9);

        return $data;
    }
}
