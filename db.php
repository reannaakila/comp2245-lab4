<?php

require __DIR__ . '/config.php';

$dsn = "mysql:host=localhost;dbname=catalogue;charset=utf8mb4";
$user = "comp2245";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
} catch (PDOException $e) {
    error_log("Database connection failed.");
    http_response_code(500);
    echo json_encode(["error" => "Could not connect to the database."]);
    exit;
}
