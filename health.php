<?php
header('Content-Type: application/json');

try {
    require __DIR__ . '/db-connect.php';
    $pdo->query('SELECT 1');
    echo json_encode(['status' => 'ok', 'database' => 'connected']);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'database' => 'unreachable']);
}
