<?php
// Load .env variables
$env = parse_ini_file(__DIR__ . '/../.env');

$host = getenv('DB_HOST') ?? $env['DB_HOST'] ?? '127.0.0.1';
$db   = getenv('DB_NAME') ?? $env['DB_NAME'] ?? 'facility_db';
$user = getenv('DB_USER') ?? $env['DB_USER'] ?? 'root';
$pass = getenv('DB_PASS') ?? $env['DB_PASS'] ?? '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("Database Connection Error: " . $e->getMessage());
}