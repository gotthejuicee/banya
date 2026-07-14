<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    /**
     * Коди українських мобільних операторів.
     * Kyivstar 039/067/068/077/096/097/098, Vodafone 050/066/075/095/099,
     * lifecell 063/073/093, 3Mob 091, PEOPLEnet 092, Intertelecom 094.
     * Міських номерів (044 тощо) тут немає — форма приймає лише мобільні.
     */
    private const OPERATOR_CODES = [
        '39', '50', '63', '66', '67', '68', '73', '75', '77',
        '91', '92', '93', '94', '95', '96', '97', '98', '99',
    ];

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
            /*
             * prepareForValidation() уже звів номер до 0XXXXXXXXX, тож тут
             * лишається перевірити код оператора й довжину.
             *
             * Старе правило /^(\+?38)?0\d{9}$/ пропускало БУДЬ-ЯКІ 9 цифр
             * після нуля — тому «0000000000» проходив. Тепер після 0 має йти
             * код реального оператора, далі рівно 7 цифр: ні більше, ні менше.
             */
            'phone' => ['required', 'string', 'regex:'.$this->phonePattern()],
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
            'phone.regex' => 'Введіть мобільний номер українського оператора: 0XX XXX XX XX.',
            'comment.max' => 'Коментар задовгий (до 1000 символів).',
        ];
    }

    /** Номер у вигляді 0XXXXXXXXX з кодом реального оператора. */
    private function phonePattern(): string
    {
        return '/^0('.implode('|', self::OPERATOR_CODES).')\d{7}$/';
    }

    protected function prepareForValidation(): void
    {
        if (! $this->filled('phone')) {
            return;
        }

        // Лишаємо самі цифри: пробіли, дужки, дефіси й «+» тут неважливі
        $digits = preg_replace('/\D/', '', (string) $this->input('phone'));

        /*
         * Зводимо до національного 0XXXXXXXXX. Свідомо НЕ обрізаємо зайве:
         * якщо цифр більше чи менше, ніж треба, номер лишається «як є» й не
         * пройде валідацію. Раніше orderData() брав ОСТАННІ 9 цифр
         * (substr($digits, -9)), тож зайві цифри тихо відкидались.
         */
        $phone = match (true) {
            // 380XXXXXXXXX (з «+» чи без)
            strlen($digits) === 12 && str_starts_with($digits, '380') => '0'.substr($digits, 3),
            // 80XXXXXXXXX — старий міжміський формат
            strlen($digits) === 11 && str_starts_with($digits, '80') => substr($digits, 1),
            default => $digits,
        };

        $this->merge(['phone' => $phone]);
    }

    /**
     * @return array<string, mixed>
     */
    public function orderData(): array
    {
        $data = $this->safe()->only(['name', 'phone', 'comment', 'product_id']);

        // Після валідації тут гарантовано 0XXXXXXXXX → +380XXXXXXXXX
        $data['phone'] = '+38'.$data['phone'];

        return $data;
    }
}
