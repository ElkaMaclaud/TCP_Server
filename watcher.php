<?php

$configFilePath = 'config.yaml'; // Путь к вашему конфигурационному файлу
$lastModifiedTime = filemtime($configFilePath); // Получаем время последнего изменения

while (true) {
    clearstatcache(); // Очищаем кэш статуса файловой системы
    $currentModifiedTime = filemtime($configFilePath); // Получаем текущее время изменения файла

    if ($currentModifiedTime !== $lastModifiedTime) {
        // Если файл изменился, выполняем нужные действия
        echo "Конфигурация изменена. Перезагрузка сервера...\n";
        $lastModifiedTime = $currentModifiedTime;

        // Здесь вы можете вызвать функцию перезагрузки сервера или выполнить другие действия
        // Например, вы можете использовать exec для перезапуска вашего сервера
        exec('php server.php ' . $configFilePath . ' > server.log &'); // Запускаем сервер в фоне
    }

    // Задержка перед следующей проверкой
    sleep(2); // Пауза на 2 секунды
}
