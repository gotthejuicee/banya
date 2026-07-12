<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\FaqItems\Pages\ManageFaqItems;
use App\Models\FaqItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\Concerns\CreatesAdmin;
use Tests\Concerns\SeedsLanding;
use Tests\TestCase;

class AdminFaqTest extends TestCase
{
    use CreatesAdmin;
    use RefreshDatabase;
    use SeedsLanding;

    public function test_admin_can_create_faq_item(): void
    {
        $admin = $this->createAdmin();

        Livewire::actingAs($admin)
            ->test(ManageFaqItems::class)
            ->callAction('create', data: [
                'question' => 'Чи є доставка?',
                'answer' => "Так.\n\nПо всій Україні.",
                'is_active' => true,
            ]);

        $item = FaqItem::query()->where('question', 'Чи є доставка?')->first();

        $this->assertNotNull($item);
        $this->assertTrue($item->is_active);
        $this->assertSame(['Так.', 'По всій Україні.'], $item->paragraphs);
    }

    public function test_landing_shows_faq_from_config(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Не знаєте, що подарувати?', false)
            ->assertSee('Подарунковий банний набір «Іди в баню»', false);
    }
}