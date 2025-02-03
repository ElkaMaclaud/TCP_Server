<?php

$configFilePath = 'config.yaml';
$lastModifiedTime = filemtime($configFilePath); 

while (true) {
    clearstatcache(); // Очищаем кэш статуса файловой системы
    $currentModifiedTime = filemtime($configFilePath); // Получаем текущее время изменения файла

    if ($currentModifiedTime !== $lastModifiedTime) {

        echo "Конфигурация изменена. Перезагрузка сервера...\n";
        $lastModifiedTime = $currentModifiedTime;
        exec('php server.php ' . $configFilePath . ' > server.log &'); 
    }

    sleep(2); // Пауза на 2 секунды
}
