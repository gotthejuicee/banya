<?php

namespace Tests\Unit;

use App\Models\FaqItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FaqItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_answer_is_split_into_paragraphs(): void
    {
        $item = FaqItem::create([
            'question' => 'Q',
            'answer' => "Перший.\n\nДругий.\n\nТретій.",
            'sort' => 1,
        ]);

        $this->assertSame(['Перший.', 'Другий.', 'Третій.'], $item->paragraphs);
    }

    public function test_published_scope_excludes_inactive_items(): void
    {
        FaqItem::create(['question' => 'A', 'answer' => '1', 'sort' => 1, 'is_active' => true]);
        FaqItem::create(['question' => 'B', 'answer' => '2', 'sort' => 2, 'is_active' => false]);

        $published = FaqItem::query()->published()->pluck('question')->all();

        $this->assertSame(['A'], $published);
    }
}