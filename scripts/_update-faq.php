<?php

use App\Models\FaqItem;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$items = config('landing.faq', []);

foreach ($items as $i => $item) {
    $row = FaqItem::query()->where('sort', $i + 1)->first()
        ?? new FaqItem(['sort' => $i + 1, 'is_active' => true]);

    $row->question = $item['q'];
    $row->answer = implode("\n\n", $item['a']);
    $row->is_active = true;
    $row->sort = $i + 1;
    $row->save();

    echo ($i + 1).': '.$row->question.PHP_EOL;
}

// Deactivate any extra FAQ items beyond the new set
$keepSorts = range(1, count($items));
$extra = FaqItem::query()->whereNotIn('sort', $keepSorts)->get();
foreach ($extra as $row) {
    $row->is_active = false;
    $row->save();
    echo 'deactivated sort='.$row->sort.': '.$row->question.PHP_EOL;
}

echo 'Done. Active: '.FaqItem::query()->where('is_active', true)->count().PHP_EOL;
