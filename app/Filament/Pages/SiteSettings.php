<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class SiteSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Налаштування сайту';

    protected static ?string $title = 'Налаштування сайту';

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.site-settings';

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'support_phone' => Setting::get('support_phone'),
            'instagram_url' => Setting::get('instagram_url'),
            'tiktok_url' => Setting::get('tiktok_url'),
            'telegram_url' => Setting::get('telegram_url'),
            'banner_1_image' => Setting::get('banner_1_image'),
            'banner_2_image' => Setting::get('banner_2_image'),
            'about_text' => Setting::get('about_text', config('landing.about')),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Контакти')
                    ->schema([
                        TextInput::make('support_phone')
                            ->label('Телефон підтримки')
                            ->tel()
                            ->required()
                            ->helperText('Тільки цифри, напр. 380974772919 — показується в шапці та підвалі'),
                    ]),

                Section::make('Соціальні мережі')
                    ->columns(1)
                    ->schema([
                        TextInput::make('instagram_url')
                            ->label('Instagram')
                            ->url()
                            ->placeholder('https://instagram.com/…'),
                        TextInput::make('tiktok_url')
                            ->label('TikTok')
                            ->url()
                            ->placeholder('https://www.tiktok.com/@…'),
                        TextInput::make('telegram_url')
                            ->label('Telegram')
                            ->url()
                            ->placeholder('https://t.me/…'),
                    ])
                    ->description('Порожнє поле — іконка не показується на сайті'),

                Section::make('Текст «Не знаєш, що подарувати?»')
                    ->schema([
                        Textarea::make('about_text')
                            ->label('Текст блоку')
                            ->rows(18)
                            ->helperText('Порожній рядок між абзацами = новий абзац на сайті. Перший абзац виділяється.'),
                    ])
                    ->collapsed(),

                Section::make('Банери')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('banner_1_image')
                            ->label('Лівий банер')
                            ->image()
                            ->disk('public')
                            ->directory('banners')
                            ->maxSize(4096)
                            ->helperText('Рекомендовано 820×380. Порожньо — стандартний банер.'),
                        FileUpload::make('banner_2_image')
                            ->label('Правий банер')
                            ->image()
                            ->disk('public')
                            ->directory('banners')
                            ->maxSize(4096)
                            ->helperText('Рекомендовано 820×380. Порожньо — стандартний банер.'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        // FileUpload може віддати масив — зводимо до одного шляху
        $file = fn ($value): ?string => is_array($value) ? ($value[0] ?? null) : $value;

        Setting::set('support_phone', preg_replace('/\D/', '', (string) ($state['support_phone'] ?? '')));
        Setting::set('instagram_url', $state['instagram_url'] ?? null);
        Setting::set('tiktok_url', $state['tiktok_url'] ?? null);
        Setting::set('telegram_url', $state['telegram_url'] ?? null);
        Setting::set('banner_1_image', $file($state['banner_1_image'] ?? null));
        Setting::set('banner_2_image', $file($state['banner_2_image'] ?? null));
        Setting::set('about_text', $state['about_text'] ?? null);

        Notification::make()
            ->title('Налаштування збережено')
            ->success()
            ->send();
    }
}
