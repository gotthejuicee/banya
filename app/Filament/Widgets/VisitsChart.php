<?php

namespace App\Filament\Widgets;

use App\Models\Visit;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class VisitsChart extends ChartWidget
{
    protected ?string $heading = 'Відвідувачі за 30 днів';

    protected static ?int $sort = 2;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $from = Carbon::today()->subDays(29)->toDateString();

        $rows = Visit::where('day', '>=', $from)
            ->selectRaw('day, count(distinct visitor_id) as u, sum(hits) as v')
            ->groupBy('day')
            ->get()
            ->keyBy(fn ($r) => (string) $r->getRawOriginal('day'));

        $labels = [];
        $unique = [];
        $views = [];

        foreach (range(29, 0) as $i) {
            $date = Carbon::today()->subDays($i);
            $key = $date->toDateString();
            $labels[] = $date->format('d.m');
            $unique[] = (int) ($rows[$key]->u ?? 0);
            $views[] = (int) ($rows[$key]->v ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Унікальні відвідувачі',
                    'data' => $unique,
                    'borderColor' => '#d6ff41',
                    'backgroundColor' => 'rgba(214, 255, 65, 0.15)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Перегляди',
                    'data' => $views,
                    'borderColor' => '#5b57d6',
                    'backgroundColor' => 'rgba(91, 87, 214, 0.10)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
