<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Enums\OrderStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Дата')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Ім’я')
                    ->weight('bold')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Телефон')
                    ->icon('heroicon-m-phone')
                    ->copyable()
                    ->copyMessage('Скопійовано')
                    ->searchable(),
                TextColumn::make('product.name')
                    ->label('Набір')
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('comment')
                    ->label('Коментар')
                    ->limit(40)
                    ->tooltip(fn (TextColumn $column): ?string => mb_strlen((string) $column->getState()) > 40 ? $column->getState() : null)
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('status')
                    ->label('Статус')
                    ->badge(),
                IconColumn::make('notified_at')
                    ->label('Telegram')
                    ->boolean()
                    ->trueIcon('heroicon-o-paper-airplane')
                    ->falseIcon('heroicon-o-exclamation-triangle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->tooltip(fn ($record): string => $record->notified_at ? 'Надіслано в Telegram' : 'Не доставлено в Telegram')
                    ->sortable(),
                TextColumn::make('ip')
                    ->label('IP')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Статус')
                    ->options(OrderStatus::class),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
