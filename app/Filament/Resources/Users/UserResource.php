<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\ManageUsers;
use App\Models\User;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $navigationLabel = 'Адміністратори';

    protected static ?string $modelLabel = 'адміністратор';

    protected static ?string $pluralModelLabel = 'Адміністратори';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Ім’я')
                    ->required()
                    ->maxLength(100),
                TextInput::make('email')
                    ->label('Email (логін)')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(150),
                // УВАГА: без Hash::make — у моделі cast 'password' => 'hashed',
                // ручне хешування дало б подвійний хеш і неробочий пароль
                TextInput::make('password')
                    ->label('Пароль')
                    ->password()
                    ->revealable()
                    ->minLength(8)
                    ->maxLength(72)
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn ($state): bool => filled($state))
                    ->same('passwordConfirmation')
                    ->helperText('При редагуванні: залиш порожнім — пароль не зміниться'),
                TextInput::make('passwordConfirmation')
                    ->label('Пароль ще раз')
                    ->password()
                    ->revealable()
                    ->dehydrated(false)
                    ->required(fn (string $operation): bool => $operation === 'create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Ім’я')
                    ->weight('bold')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email (логін)')
                    ->icon('heroicon-m-envelope')
                    ->copyable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Створений')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                // Себе видалити не можна — щоб не лишити адмінку без адмінів
                DeleteAction::make()
                    ->visible(fn (User $record): bool => $record->id !== auth()->id()),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageUsers::route('/'),
        ];
    }
}
