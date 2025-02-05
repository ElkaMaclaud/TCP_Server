<?php
require 'vendor/autoload.php';

use MongoDB\Client;

// Подключение к MongoDB
$mongoClient = new Client("mongodb://127.127.126.51:27017");
echo "Подключение к MongoDB успешно!".$mongoClient;
$collection = $mongoClient->gc_logs->logs;

// Настройки
$leakArray = [];
$startTime = time();
$maxExecutionTime = 300; // 5 минут
$logInterval = 10; // Логировать каждые 10 секунд

// Статистика GC
$gcStats = [
    'young' => 0,
    'old' => 0,
    'time_spent' => 0
];

// Включаем сборку мусора
gc_enable();

echo "Приложение запущено. Ожидаем OutOfMemory...\n";

while (true) {
    // Искусственное подтекание памяти
    for ($i = 0; $i < 1000; $i++) {
        $leakArray[] = str_repeat("leak", 100); // Добавляем строки в массив
    }
    // Удаляем только половину элементов, чтобы память продолжала расти
    $leakArray = array_slice($leakArray, count($leakArray) / 2);

    // Принудительный вызов сборки мусора
    $startGcTime = microtime(true);
    $collectedCycles = gc_collect_cycles();
    $endGcTime = microtime(true);

    // Обновляем статистику
    $gcStats['time_spent'] += ($endGcTime - $startGcTime);
    if ($collectedCycles > 0) {
        $gcStats['young'] += $collectedCycles; // Условно считаем все сборки "молодыми"
    }

    // Логирование каждые $logInterval секунд
    if ((time() - $startTime) % $logInterval === 0) {
        $log = [
            'timestamp' => new MongoDB\BSON\UTCDateTime(),
            'memory_usage' => memory_get_usage(true),
            'gc_stats' => $gcStats
        ];
        $collection->insertOne($log);
        echo "Лог сохранён: " . json_encode($log) . "\n";
    }

    // Проверяем, прошло ли 5 минут
    if ((time() - $startTime) > $maxExecutionTime) {
        echo "Приложение завершено. OutOfMemory не достигнут.\n";
        break;
    }

    // Искусственная задержка для контроля скорости роста памяти
    usleep(50000); // 50ms
}