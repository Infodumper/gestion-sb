<?php
/**
 * Database Connection Helper (PDO) - BYPASS VERSION
 * Usando credenciales directas para evitar bloqueos de InfinityFree
 */

require_once 'load_env.php';

// Leer de $_ENV (donde load_env.php pone los datos)
$host = $_ENV['DB_HOST'] ?? 'localhost';
$db   = $_ENV['DB_NAME'] ?? 'consultora_belleza';
$user = $_ENV['DB_USER'] ?? 'root';
$pass = $_ENV['DB_PASS'] ?? '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'ajax_') !== false)) {
         header('Content-Type: application/json');
         echo json_encode(['success' => false, 'message' => 'Error de conexión: ' . $e->getMessage()]);
         exit;
     }
     die("Error de conexión a la DB: " . $e->getMessage());
}
?>
