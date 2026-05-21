# Еда от тёти Кати

Сайт заказов домашней еды: публичное меню с корзиной и простая админка (CRM) для управления меню и заказами.

## Возможности

- **Сайт для клиентов**: меню по категориям, корзина, оформление заказа без регистрации
- **Админка** (`/admin`): заказы, статусы, меню, настройки контактов
- **Без онлайн-оплаты** — заявка и связь как в WhatsApp/Telegram

## Требования

- PHP 8.3+ с расширениями: `pdo_sqlite` или `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`
- Composer
- Node.js 18+ (для сборки фронтенда)
- Для продакшена: MySQL 8+ рекомендуется

## Установка (локально)

```bash
composer install
cp .env.example .env
php artisan key:generate
```

### База данных

**SQLite** (по умолчанию в `.env.example`):

```bash
# Windows: включите в php.ini extension=pdo_sqlite и extension=sqlite3
touch database/database.sqlite   # или New-Item database/database.sqlite
php artisan migrate
php artisan db:seed
```

**MySQL** — в `.env`:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=ekaterina_food
DB_USERNAME=root
DB_PASSWORD=...
```

```bash
php artisan migrate
php artisan db:seed
```

### Фронтенд и файлы

```bash
npm install
npm run build
php artisan storage:link
```

### Загрузка фото в админке

- В `php.ini` желательно `upload_max_filesize = 8M` и `post_max_size = 8M` (сейчас на Windows часто стоит 2M).
- `APP_URL` в `.env` должен совпадать с адресом в браузере (`http://localhost:8000`, не `127.0.0.1`, если открываете localhost).
- После выбора фото дождитесь завершения загрузки (пропадёт «Ожидание»), затем нажмите **Сохранить**.

### Запуск

```bash
php artisan serve
```

- Сайт: http://localhost:8000  
- Админка: http://localhost:8000/admin  

### Вход в админку (после сида)

| Поле | Значение |
|------|----------|
| Email | `katya@ekaterinafood.local` |
| Пароль | `password` |

**Смените пароль сразу после первого входа.**

## Деплой (Beget, Timeweb и др.)

1. Загрузите файлы проекта, укажите `public/` как корень сайта.
2. PHP 8.3+, включите `intl`, `pdo_mysql`, `fileinfo`.
3. Создайте БД MySQL, пропишите в `.env`.
4. На сервере:
   ```bash
   composer install --no-dev --optimize-autoloader
   php artisan key:generate
   php artisan migrate --force
   php artisan db:seed --force
   php artisan storage:link
   npm ci && npm run build
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
5. Права на запись: `storage/`, `bootstrap/cache/`.
6. Cron для очередей (если используете): `* * * * * php /path/artisan schedule:run`.

## Настройки сайта

В админке: **Настройки сайта** — телефон, WhatsApp, Telegram, тексты главной, адрес самовывоза.

## Создание пользователя админки

```bash
php artisan make:filament-user
```

## Структура

- `app/Http/Controllers/` — публичные страницы
- `app/Filament/` — админка
- `app/Services/CartService.php` — корзина в сессии
- `app/Services/OrderService.php` — создание заказов, текст для WhatsApp

## Лицензия

MIT
