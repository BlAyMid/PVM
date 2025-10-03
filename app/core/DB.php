<?php
namespace App\core;

use PDO;
use PDOException;

// класс-обёртка для подключения к базе данных через pdo
class DB
{
    // @var PDO|null одиночный экземпляр подключения pdo
    private static $pdo = null;

    // возвращает соединение pdo с mysql, используя параметры из окружения
    // @return PDO подключение pdo
    public static function conn()
    {
        if (self::$pdo === null) {
            $host = isset($_ENV['DB_HOST']) ? $_ENV['DB_HOST'] : 'db';
            $port = isset($_ENV['DB_PORT']) ? (int)$_ENV['DB_PORT'] : 3306;
            $db = isset($_ENV['DB_DATABASE']) ? $_ENV['DB_DATABASE'] : 'pvm';
            $user = isset($_ENV['DB_USERNAME']) ? $_ENV['DB_USERNAME'] : 'pvm';
            $pass = isset($_ENV['DB_PASSWORD']) ? $_ENV['DB_PASSWORD'] : 'pvm';
            $charset = isset($_ENV['DB_CHARSET']) ? $_ENV['DB_CHARSET'] : 'utf8mb4';
            $dsn = "mysql:host={$host};port={$port};dbname={$db};charset={$charset}";
            try {
                self::$pdo = new PDO($dsn, $user, $pass, array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ));
            } catch (PDOException $e) {
                // если соединение не удалось, отдаём 500 и описание ошибки
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode(array('error' => 'Database connection failed', 'details' => $e->getMessage()));
                exit;
            }
        }
        return self::$pdo;
    }
}
