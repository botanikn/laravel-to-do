#!/bin/bash
set -e

# Проверяем наличие vendor/autoload.php
if [ ! -f "/app/vendor/autoload.php" ]; then
    echo "vendor/autoload.php не найден. Запускаю composer install..."
    composer install --no-interaction --prefer-dist
fi

# Выполняем переданную команду
exec "$@"

