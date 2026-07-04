<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Набір')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Назва')
                            ->required()
                            ->maxLength(100),
                        Select::make('category')
                            ->label('Категорія')
                            ->options([
                                'male' => 'Чоловічий набір',
                                'female' => 'Жіночий набір',
                            ])
                            ->required()
                            ->selectablePlaceholder(false),
                        TextInput::make('tagline')
                            ->label('Підзаголовок')
                            ->maxLength(120)
                            ->placeholder('Подарунковий банний набір'),
                        TextInput::make('badge')
                            ->label('Бейдж на фото')
                            ->maxLength(30)
                            ->placeholder('ХІТ ПРОДАЖІВ')
                            ->helperText('Порожньо — картка без бейджа'),
                        TextInput::make('price')
                            ->label('Ціна, грн')
                            ->numeric()
                            ->required()
                            ->minValue(1),
                        TextInput::make('old_price')
                            ->label('Стара ціна, грн')
                            ->numeric()
                            ->minValue(1)
                            ->helperText('Показується закресленою поруч із ціною'),
                        TextInput::make('sort')
                            ->label('Порядок у секції')
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_active')
                            ->label('Показувати на сайті')
                            ->default(true)
                            ->inline(false),
                    ]),

                Section::make('Вміст набору')
                    ->schema([
                        Repeater::make('contents')
                            ->label('Складові')
                            ->simple(
                                TextInput::make('item')
                                    ->required()
                                    ->maxLength(120),
                            )
                            ->reorderable()
                            ->addActionLabel('Додати складову')
                            ->helperText('Зараз показується лише менеджеру і в майбутніх версіях картки'),
                        Textarea::make('description')
                            ->label('Опис')
                            ->rows(3)
                            ->maxLength(500),
                    ]),

                Section::make('Фото картки')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('photo_dark')
                            ->label('Темний бокс')
                            ->image()
                            ->disk('public')
                            ->directory('products')
                            ->maxSize(4096)
                            ->helperText('Квадратне фото на білому тлі. Порожньо — стандартне.'),
                        FileUpload::make('photo_light')
                            ->label('Світлий бокс')
                            ->image()
                            ->disk('public')
                            ->directory('products')
                            ->maxSize(4096)
                            ->helperText('Квадратне фото на білому тлі. Порожньо — стандартне.'),
                    ]),
            ]);
    }
}
