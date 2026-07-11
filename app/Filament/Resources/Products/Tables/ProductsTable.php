<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('category')
            ->columns([
                ImageColumn::make('photo')
                    ->label('Фото')
                    ->getStateUsing(fn ($record) => $record->cardPhoto()['fallback']),
                TextColumn::make('name')
                    ->label('Назва')
                    ->weight('bold')
                    ->searchable(),
                TextColumn::make('category')
                    ->label('Категорія')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'male' ? 'Чоловічий' : 'Жіночий')
                    ->color(fn (string $state): string => $state === 'male' ? 'info' : 'danger'),
                TextColumn::make('price')
                    ->label('Ціна')
                    ->formatStateUsing(fn (int $state): string => number_format($state, 0, ',', ' ').' ₴')
                    ->sortable(),
                TextColumn::make('old_price')
                    ->label('Стара ціна')
                    ->formatStateUsing(fn (?int $state): string => $state ? number_format($state, 0, ',', ' ').' ₴' : '—')
                    ->toggleable(),
                ToggleColumn::make('is_active')
                    ->label('На сайті'),
                TextColumn::make('sort')
                    ->label('Порядок')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Категорія')
                    ->options([
                        'male' => 'Чоловічий',
                        'female' => 'Жіночий',
                    ]),
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
