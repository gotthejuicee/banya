<?php

namespace Tests\Unit;

use App\Http\Requests\StoreOrderRequest;
use Illuminate\Routing\Redirector;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class StoreOrderRequestTest extends TestCase
{
    /**
     * Проганяємо номер тим самим шляхом, що й справжній запит:
     * prepareForValidation() → rules(). Інакше тест перевіряв би regex у
     * вакуумі й не помітив би, що нормалізація тихо щось обрізала.
     */
    private function phoneFails(string $phone): bool
    {
        $request = StoreOrderRequest::create('/', 'POST', ['name' => 'Марія', 'phone' => $phone]);
        $request->setContainer($this->app);
        // При провалі валідації FormRequest збирає redirect-відповідь — без
        // редиректора впаде на getUrlGenerator() замість ValidationException
        $request->setRedirector($this->app->make(Redirector::class));

        try {
            $request->validateResolved();
        } catch (ValidationException) {
            return true;
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function validatedOrderData(array $data): array
    {
        $request = StoreOrderRequest::create('/', 'POST', $data);
        $request->setContainer($this->app);
        $request->validateResolved();

        return $request->orderData();
    }

    public function test_phone_is_normalized_to_international_format(): void
    {
        $data = $this->validatedOrderData([
            'name' => 'Марія',
            'phone' => '0 (97) 477-29-19',
        ]);

        $this->assertSame('+380974772919', $data['phone']);
    }

    public function test_validator_accepts_common_ukrainian_phone_formats(): void
    {
        foreach (['0974772919', '+380974772919', '380974772919', '80974772919'] as $phone) {
            $this->assertFalse($this->phoneFails($phone), "Номер {$phone} мав пройти");
        }
    }

    public function test_all_ukrainian_operator_codes_are_accepted(): void
    {
        $codes = [
            '039', '067', '068', '077', '096', '097', '098', // Kyivstar
            '050', '066', '075', '095', '099',               // Vodafone
            '063', '073', '093',                             // lifecell
            '091',                                           // 3Mob
            '092',                                           // PEOPLEnet
            '094',                                           // Intertelecom
        ];

        foreach ($codes as $code) {
            $this->assertFalse($this->phoneFails($code.'1234567'), "Код {$code} мав пройти");
        }
    }

    /** Саме на цьому замовник упіймав форму: десять нулів проходили. */
    public function test_ten_zeros_are_rejected(): void
    {
        $this->assertTrue($this->phoneFails('0000000000'));
    }

    /** Замовник: форма збирає лише мобільні, міські номери не приймаємо. */
    public function test_landline_numbers_are_rejected(): void
    {
        // 044 Київ, 032 Львів, 048 Одеса, 057 Харків, 061 Запоріжжя
        foreach (['0441234567', '0322334455', '0481234567', '0571234567', '0612345678'] as $phone) {
            $this->assertTrue($this->phoneFails($phone), "Міський {$phone} мав бути відхилений");
        }
    }

    public function test_numbers_with_unknown_codes_are_rejected(): void
    {
        foreach (['0401234567', '0701234567', '0801234567', '0901234567'] as $phone) {
            $this->assertTrue($this->phoneFails($phone), "Номер {$phone} мав бути відхилений");
        }
    }

    public function test_wrong_length_is_rejected(): void
    {
        foreach ([
            '097477291',       // на цифру менше
            '09747729199',     // на цифру більше
            '+38097477291',    // на цифру менше з кодом країни
            '+3809747729199',  // на цифру більше з кодом країни
        ] as $phone) {
            $this->assertTrue($this->phoneFails($phone), "Номер {$phone} мав бути відхилений");
        }
    }

    public function test_foreign_numbers_are_rejected(): void
    {
        foreach (['+79261234567', '+48123456789', '+12025550123'] as $phone) {
            $this->assertTrue($this->phoneFails($phone), "Номер {$phone} мав бути відхилений");
        }
    }
}
