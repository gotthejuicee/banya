<?php

namespace Tests\Unit;

use App\Http\Requests\StoreOrderRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreOrderRequestTest extends TestCase
{
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
        $rules = (new StoreOrderRequest)->rules();

        foreach (['0974772919', '+380974772919', '380974772919'] as $phone) {
            $validator = Validator::make(
                ['phone' => preg_replace('/[\s\(\)\-]+/', '', $phone)],
                ['phone' => $rules['phone']],
            );

            $this->assertFalse($validator->fails(), "Phone {$phone} should be valid");
        }
    }
}