<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Visit;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class VisitorsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $today = Carbon::today()->toDateString();
        $weekAgo = Carbon::today()->subDays(6)->toDateString();
        $monthAgo = Carbon::today()->subDays(29)->toDateString();

        $uniqueToday = Visit::where('day', $today)->distinct('visitor_id')->count('visitor_id');
        $unique30 = Visit::where('day', '>=', $monthAgo)->distinct('visitor_id')->count('visitor_id');
        $views30 = (int) Visit::where('day', '>=', $monthAgo)->sum('hits');
        $orders = Order::count();

        // Спарклайн: унікальні відвідувачі по днях за тиждень
        $byDay = Visit::where('day', '>=', $weekAgo)
            ->selectRaw('day, count(distinct visitor_id) as u')
            ->groupBy('day')
            ->pluck('u', 'day');

        $spark = collect(range(6, 0))
            ->map(fn ($i) => (int) ($byDay[Carbon::today()->subDays($i)->toDateString()] ?? 0))
            ->all();

        return [
            Stat::make('Відвідувачі сьогодні', $uniqueToday)
                ->description('Унікальні люди (боти не рахуються)')
                ->descriptionIcon('heroicon-m-users')
                ->color('success')
                ->chart($spark),
            Stat::make('Відвідувачі за 30 днів', $unique30)
                ->description('Унікальні за місяць')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),
            Stat::make('Переглядів за 30 днів', $views30)
                ->description('Усього відкриттів сторінок')
                ->descriptionIcon('heroicon-m-eye')
                ->color('warning'),
            Stat::make('Заявок', $orders)
                ->description('Усього з форми замовлення')
                ->descriptionIcon('heroicon-m-inbox-arrow-down')
                ->color('primary'),
        ];
    }
}
