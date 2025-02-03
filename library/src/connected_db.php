<?php
require __DIR__ . '/vendor/autoload.php';


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$dsn = 'pgsql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';user=' . $_ENV['DB_USER'] . ';password=' . $_ENV['DB_PASSWORD'];
//$dsn = 'pgsql:host=$_ENV[DB_HOST];dbname=my_first_db;user=$_ENV[DB_USER];password=$_ENV[DB_PASSWORD]';
try {
    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo 'Подключение прошло успешло!';
} catch (PDOException $e) {
    echo 'Ошибка подключения: ' . $e->getMessage();
    exit;
}