# Деплой на Хостинг Україна (прод: idi-v-banyu.com.ua)

Той самий флоу, що й для zmova.com.ua: git clone у `www`, Composer без dev,
docroot на `www/public`. Збірки фронтенду немає — CSS/JS лежать у `public/`.
Старий тест-субдомен idi-v-banyu.zmova.com.ua віддає 301 на новий домен.

**БД = MySQL** (з 2026-07-03): база `tr461119_banya` @ `tr461119.mysql.tools`
(utf8mb4_unicode_ci), створена в панелі adm.tools → MySQL. Креденшели в `.env`.
Бекап старої SQLite лежить у `database/backup-before-mysql.sqlite` на сервері.
Локальна розробка й далі на SQLite (`.env.example`).

## Перший деплой (веб-термінал adm.tools)

```bash
cd ~/idi-v-banyu.com.ua/www
# тека має бути порожня (rm -f index.html, якщо хостинг поклав заглушку)
git clone https://github.com/gotthejuicee/banya.git .

composer install --no-dev --optimize-autoloader

cp .env.production.example .env
# впиши TELEGRAM_BOT_TOKEN і TELEGRAM_CHAT_ID (можна лишити порожніми —
# заявки все одно зберігаються в БД)
php artisan key:generate --force

touch database/database.sqlite
php artisan migrate --force
php artisan db:seed --force

php artisan optimize
```

## Панель adm.tools (разово)

1. **Налаштування сайту → Кореневий каталог** → до `.../www/` дописати `public`
   (інакше 403/лістинг — як було зі zmova).
2. **Налаштування SSL** → Let's Encrypt → увімкнути + редирект HTTP→HTTPS.
3. PHP 8.3+.

## Оновлення

```bash
cd ~/idi-v-banyu.com.ua/www
git pull && php artisan optimize:clear && php artisan optimize
# + php artisan migrate --force — якщо в коміті нові міграції
# + php artisan db:seed --force ДО optimize — якщо новий сидер (env-кеш!)
```

> GitHub: пароль-авторизація мертва — на сервері використовуй Personal Access
> Token як пароль (`git config --global credential.helper store` вже налаштовано
> для деплоїв zmova/otfk, тож pull працюватиме без запитів).
