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

$pdo->beginTransaction();

try {
    // Lock the inventory row so two simultaneous orders cannot oversell the same stock.
    $stmt = $pdo->prepare('SELECT price, stock_qty FROM inventory WHERE item_id = ? FOR UPDATE');
    $stmt->execute([$itemId]);
    $item = $stmt->fetch();

    if (!$item) {
        $pdo->rollBack();
        http_response_code(400);
        die('Selected product not found.');
    }

    if ($quantity > (int)$item['stock_qty']) {
        $pdo->rollBack();
        http_response_code(409);
        die('Not enough stock. Only ' . (int)$item['stock_qty'] . ' left.');
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

    $stmt = $pdo->prepare('UPDATE inventory SET stock_qty = stock_qty - ? WHERE item_id = ?');
    $stmt->execute([$quantity, $itemId]);

    $pdo->commit();
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    die('Order could not be processed.');
}

header('Location: index.php?order=success');
exit;
