# IDI_V_BANYU__ — лендинг банних наборів

Лендинг за макетом Figma: чорний фон, лаймові акценти, шрифт Benzin.
Laravel 13 + SQLite, без збірки фронтенду — CSS/JS лежать у `public/`.

## Структура

- `routes/web.php` — головна, POST `/order` (throttle 6/хв), sitemap.xml
- `app/Http/Controllers/LandingController.php` — каталог по категоріях
- `app/Http/Controllers/OrderController.php` — прийом заявки (+ тихий honeypot)
- `app/Jobs/SendOrderToTelegram.php` — сповіщення менеджеру в Telegram
- `database/seeders/ProductSeeder.php` — 4 набори (чоловічі/жіночі)
- `resources/views/landing.blade.php` + `partials/product-card.blade.php`
- `public/css/landing.css`, `public/js/landing.js` — верстка й модалка
- `public/images/` — банери (webp + png-фолбек), `public/fonts/` — шрифти

## Запуск

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

## Telegram-заявки

1. Створи бота через **@BotFather** → `TELEGRAM_BOT_TOKEN`.
2. Дізнайся `TELEGRAM_CHAT_ID` (напр., через @getmyid_bot).
3. Впиши обидва значення в `.env`.

Без налаштованого Telegram заявки все одно зберігаються в БД
(таблиця `orders`), у лог пишеться попередження.

## Шрифти

- **Benzin Bold** (заголовки) — комерційний шрифт Supremat; webfont-конверсія
  з OnlineWebFonts (CC BY 4.0 із зазначенням авторства). Для комерційного
  використання придбай ліцензію в автора.
- **Rubik** (текст + фолбек заголовків) — Google Fonts, OFL.
