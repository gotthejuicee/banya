<?php

namespace App\Filament\Resources\FaqItems;

use App\Filament\Resources\FaqItems\Pages\ManageFaqItems;
use App\Models\FaqItem;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class FaqItemResource extends Resource
{
    protected static ?string $model = FaqItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;

    protected static ?string $navigationLabel = 'Питання (FAQ)';

    protected static ?string $modelLabel = 'питання';

    protected static ?string $pluralModelLabel = 'Питання (FAQ)';

    protected static ?string $recordTitleAttribute = 'question';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('question')
                    ->label('Питання')
                    ->required()
                    ->maxLength(200)
                    ->columnSpanFull(),
                Textarea::make('answer')
                    ->label('Відповідь')
                    ->required()
                    ->rows(10)
                    ->helperText('Порожній рядок між абзацами = новий абзац на сайті')
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label('Показувати на сайті')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort')
            ->reorderable('sort')
            ->columns([
                TextColumn::make('question')
                    ->label('Питання')
                    ->weight('bold')
                    ->limit(70)
                    ->searchable(),
                TextColumn::make('answer')
                    ->label('Відповідь')
                    ->limit(60)
                    ->toggleable(),
                ToggleColumn::make('is_active')
                    ->label('На сайті'),
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

    public static function getPages(): array
    {
        return [
            'index' => ManageFaqItems::route('/'),
        ];
    }
}
