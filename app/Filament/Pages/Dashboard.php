<?php

namespace App\Filament\Pages;

use App\Models\Visit;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard as BaseDashboard;

/**
 * Своя інфопанель — щоб додати кнопку «Очистити статистику».
 *
 * Реєструється автоматично через discoverPages() у AdminPanelProvider,
 * тому базову Filament\Pages\Dashboard там НЕ вказуємо (інакше в меню
 * було б два пункти — pages() додає без перевірки на дублі).
 */
class Dashboard extends BaseDashboard
{
    protected function getHeaderActions(): array
    {
        return [
            Action::make('clearVisits')
                ->label('Очистити статистику')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->outlined()
                // Дані незворотні — питаємо, і в модалці показуємо, скільки саме зникне
                ->requiresConfirmation()
                ->modalHeading('Очистити статистику відвідувачів?')
                ->modalDescription(fn (): string => 'Буде видалено записів: '.Visit::query()->count()
                    .'. Лічильники й графік почнуться з нуля. Заявки не постраждають. Дію не можна скасувати.')
                ->modalSubmitActionLabel('Так, очистити')
                ->visible(fn (): bool => Visit::query()->exists())
                ->action(function (): void {
                    $count = Visit::query()->count();
                    Visit::query()->truncate();

                    Notification::make()
                        ->success()
                        ->title('Статистику очищено')
                        ->body("Видалено записів: {$count}.")
                        ->send();

                    // Віджети — окремі Livewire-компоненти й самі не оновляться
                    $this->redirect(static::getUrl());
                }),
        ];
    }
}
