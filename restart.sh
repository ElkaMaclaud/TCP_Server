#!/bin/bash

# Читаем порт из config.yaml
PORT=$(php -r "
require 'vendor/autoload.php';
use Symfony\Component\Yaml\Yaml;
\$config = Yaml::parseFile('config.yaml');
echo \$config['server']['port'] ?? '';
")

if [ -z "$PORT" ]; then
  echo "Порт не найден в config.yaml"
  exit 1
fi

# Устанавливаем переменную окружения
export APP_PORT=$PORT

# Перезапускаем контейнер
docker-compose down
docker-compose up -d