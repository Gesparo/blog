# Блог [![Build Status](https://travis-ci.com/Gesparo/blog.svg?branch=master)](https://travis-ci.com/Gesparo/blog) [![StyleCI](https://github.styleci.io/repos/144756721/shield?branch=master)](https://github.styleci.io/repos/144756721)

Это тестовое задание по созданию JSON API блога

## Требования
- git >= 2.13.1
- composer >= 1.4.2
- PostgreSQL >= 10.0.0
- Redis => 3.2.0
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

Настроить подключение к Redis
```
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
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
Дополнительно принимает параметры (опционально):
- **visualization** со значением '1' - позволяет визуализировать процесс и отображает интервалы времени, за которые был выполнен каждый шаг операции
- **posts_limit** с любым *положительным* числом - кол-во добавляемых постов, по умолчанию - 100
- **ips_limit** с любым *положительным* числом - кол-во уникальных ip адресов, по умолчанию - 100
- **users_limit** с любым *положительным* числом - кол-во уникальных пользователей, по умолчанию - 100
- **rating_limit** не должно превышать *posts_limit* - кол-во постов, у которых должен быть рейтинг, по умолнчанию - 50
- **rating_range_limit** с любым *положительным* числом - кол-во уникальных пользователей, по умолчанию - 50

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