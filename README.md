# Тестовое задание - Блог

## Требования
- git >= 2.13.1
- composer >= 1.4.2
- PostgreSQL >= 10.0.0
- PHP >= 7.1.3
- OpenSSL PHP Extension
- PDO PHP Extension
- Mbstring PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension
- Ctype PHP Extension
- JSON PHP Extension
- PostgreSQL PHP Extension

## Установка
```
git clone https://github.com/Gesparo/blog.git blog
cd blog
composer install
php -r "file_exists('.env') || copy('.env.example', '.env');"
php artisan key:generate
```
После установки и настойки, перейдя по http://mysite.local/ вы увидите страницу приветствия Laravel

## Настройка
В файле .env:
```
APP_URL=http://localhost
```
Установить правильный url к вашему сайту.

Настроить подключение к базе данных:
```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=homestead
DB_USERNAME=homestead
DB_PASSWORD=secret
```

Мигрировать базу данных, прописав в консоли:
```
php artisan migrate
```
## Работа с сайтом
Для запросов к серверу желательно добавлять в HEADER запроса
```
Content-Type: application/json
```
в противном случае сервер будет отвечать 302 статусом при ошибках валидации.

### Генератор тестовых данных
```
GET http://mysite.local/api/generator
```
Дополнительно принимает 3 параметра (опционально):
- *visualization* со значением *true* - позволяет визуализировать процесс и отображает интервалы времени, за которые был выполнен каждый шаг операции
- *posts_limit* с любым *положительным* числом - кол-во добавляемых постов, по умолчанию - 1000
- *rating_range* не должно превышать *posts_limit* - кол-во постов, у которых должен быть рейтинг

**Важно!** Для повторного запуска НЕОБХОДИМО пересоздать базу данных. Для этого нужно прописать команду:
```
php artisan migrate:fresh
```
### Создание постов
```
POST http://mysite.local/api/post
```
Принимает следующие параметры:
- *title* - заголовок поста
- *body* - содержание
- *login* - логин автора
- *user_ip* - ip автора

### Добавление рейтинга к посту
```
POST http://mysite.local/api/post/ratable
```
Принимает следующие параметры:
- *post_id* - id поста
- *rating* - рейтинг от 1 до 5

### Получение постов по лучшему среднему рейтингу
```
GET http://mysite.local/api/post
```
Принимает следующие параметры:
- *limit* (опционально) - кол-во возвращаемых постов (по умолчанию - 50)

### Получение списка ip, с которых псотило несколько авторов
```
GET http://mysite.local/post/ip
```

## Тестирование
Для проверки того, что все работает - запустите команду:
```
vendor/phpunit/phpunit/phpunit
```