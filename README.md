# IDI_V_BANYU__ — лендинг банних наборів

Лендинг за макетом Figma: чорний фон, лаймові акценти, шрифт Benzin.
Laravel 13 + SQLite + **Filament 5 адмінка**, фронтенд без збірки — CSS/JS у `public/`.

## Структура

- `routes/web.php` — головна, POST `/order` (throttle 6/хв), sitemap.xml
- `app/Http/Controllers/LandingController.php` — каталог, FAQ, налаштування з БД
- `app/Http/Controllers/OrderController.php` — прийом заявки (+ тихий honeypot)
- `app/Jobs/SendOrderToTelegram.php` — сповіщення менеджеру в Telegram
- `app/Filament/` — адмінка: Заявки, Набори, Питання (FAQ), Налаштування сайту
- `app/Models/Setting.php` — key/value налаштування (телефон, соцмережі, банери)
- `database/seeders/` — 4 набори + FAQ + налаштування + адмін із env
- `resources/views/landing.blade.php` + `partials/product-card.blade.php`
- `public/css/landing.css`, `public/js/landing.js` — верстка й модалка
- `public/images/` — банери (webp + png-фолбек), `public/fonts/` — шрифти

## Запуск

```bash
composer install
cp .env.example .env      # впиши ADMIN_PASSWORD і TELEGRAM_*
php artisan key:generate
php artisan migrate --seed
php artisan storage:link  # для фото з адмінки
php artisan serve
```

## Адмінка

`/admin` — логін з `ADMIN_EMAIL` / `ADMIN_PASSWORD` (сидеться `php artisan db:seed`).

- **Заявки** — статуси (нова/в роботі/виконана/відхилена), лічильник нових
  у меню, позначка доставки в Telegram.
- **Набори** — назва, ціни, бейдж, вміст, фото (аплоад із фолбеком на
  стандартне), вкл/викл на сайті.
- **Питання (FAQ)** — перетягування порядку, порожній рядок = новий абзац.
- **Налаштування сайту** — телефон підтримки, соцмережі (порожнє = іконка
  прихована), банери.

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
