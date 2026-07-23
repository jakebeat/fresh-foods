<?php
require __DIR__ . '/db-connect.php';

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$itemId = (int)($_POST['item_id'] ?? 0);
$quantity = max(1, (int)($_POST['quantity'] ?? 1));

if ($name === '' || $email === '' || $itemId <= 0) {
    http_response_code(400);
    die('Missing required fields.');
}

$stmt = $pdo->prepare('SELECT price FROM inventory WHERE item_id = ?');
$stmt->execute([$itemId]);
$item = $stmt->fetch();

if (!$item) {
    http_response_code(400);
    die('Selected product not found.');
}

$total = $item['price'] * $quantity;

$stmt = $pdo->prepare('SELECT customer_id FROM customers WHERE email = ?');
$stmt->execute([$email]);
$customer = $stmt->fetch();

if ($customer) {
    $customerId = $customer['customer_id'];
} else {
    $stmt = $pdo->prepare('INSERT INTO customers (name, email) VALUES (?, ?)');
    $stmt->execute([$name, $email]);
    $customerId = $pdo->lastInsertId();
}

$stmt = $pdo->prepare('INSERT INTO orders (customer_id, total) VALUES (?, ?)');
$stmt->execute([$customerId, $total]);

header('Location: index.php?order=success');
exit;
