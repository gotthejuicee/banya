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

    public function test_landline_area_codes_are_accepted(): void
    {
        $codes = [
            '031', '032', '033', '034', '035', '036', '037', '038',
            '041', '043', '044', '045', '046', '047', '048',
            '051', '052', '053', '054', '055', '056', '057',
            '061', '062', '064', '065', '069',
        ];

        foreach ($codes as $code) {
            $this->assertFalse($this->phoneFails($code.'1234567'), "Код області {$code} мав пройти");
        }
    }

    public function test_numbers_with_unknown_codes_are_rejected(): void
    {
        // 040/042/049/058/060/070/080/090 — не існує ні як оператор, ні як область
        foreach ([
            '0401234567', '0421234567', '0491234567', '0581234567',
            '0601234567', '0701234567', '0801234567', '0901234567',
        ] as $phone) {
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
