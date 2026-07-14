<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Окремі заявки видаляються галочками в таблиці (Bulk actions).
            // Ця кнопка — щоб одним рухом прибрати всі тестові після здачі сайту.
            Action::make('deleteAllOrders')
                ->label('Видалити всі заявки')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->outlined()
                ->requiresConfirmation()
                ->modalHeading('Видалити всі заявки?')
                ->modalDescription(fn (): string => 'Буде видалено заявок: '.Order::query()->count()
                    .'. Дію не можна скасувати.')
                ->modalSubmitActionLabel('Так, видалити все')
                ->visible(fn (): bool => Order::query()->exists())
                ->action(function (): void {
                    $count = Order::query()->count();
                    Order::query()->delete();

                    Notification::make()
                        ->success()
                        ->title('Заявки видалено')
                        ->body("Видалено заявок: {$count}.")
                        ->send();

                    $this->redirect(static::getUrl());
                }),
        ];
    }
}
