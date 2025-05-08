# Что нужно для поднятия проекта

1. wsl Ubuntu
2. docker desktop

# Как поднять проект
## Все дальнейшие действия осуществляются в wsl

1. git clone https://github.com/botanikn/laravel-to-do.git
2. В корне проекта docker-compose up -d
3. docker exec -it backend bash
4. php artisan migrate

Адрес backend api - localhost:8000/api

Адрес клиентского фронтенда - localhost:3000

# Также в корне проекта лежит openapi.yaml с описанием api