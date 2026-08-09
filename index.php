<?php
require __DIR__ . '/db-connect.php';

$products = $pdo->query('SELECT item_id, item_name, price, stock_qty FROM inventory ORDER BY item_id')->fetchAll();
$orderSuccess = isset($_GET['order']) && $_GET['order'] === 'success';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Fresh Foods</title>
<style>
body { font-family: Arial, sans-serif; max-width: 600px; margin: 40px auto; padding: 0 20px; }
.product { border: 1px solid #ddd; padding: 16px; border-radius: 8px; margin-bottom: 24px; }
.product img { max-width: 100%; border-radius: 6px; }
form label { display: block; margin-top: 10px; }
input, select { width: 100%; padding: 8px; margin-top: 4px; box-sizing: border-box; }
button { margin-top: 16px; padding: 10px 20px; }
.success { background: #e6ffed; border: 1px solid #34c759; padding: 12px; border-radius: 6px; }
</style>
</head>
<body>
<h1>Fresh Foods</h1>

<?php if ($orderSuccess): ?>
<p class="success">Thanks — your test order was placed successfully.</p>
<?php endif; ?>

<?php foreach ($products as $product): ?>
<div class="product">
    <img src="https://freshfoods-jbeaty-assets.s3.us-east-2.amazonaws.com/products/banana-2449019_640.jpg" alt="<?= htmlspecialchars($product['item_name']) ?>">
    <h2><?= htmlspecialchars($product['item_name']) ?></h2>
    <p>$<?= htmlspecialchars(number_format($product['price'], 2)) ?> — <?= (int)$product['stock_qty'] ?> in stock <?php if ((int)$product['stock_qty'] <= 5): ?>(Low stock!)<?php endif; ?></p>
</div>
<?php endforeach; ?>

<h2>Place a Test Order</h2>
<form action="order.php" method="post">
    <label>Name
        <input type="text" name="name" required>
    </label>
    <label>Email
        <input type="email" name="email" required>
    </label>
    <label>Product
        <select name="item_id" required>
            <?php foreach ($products as $product): ?>
            <option value="<?= (int)$product['item_id'] ?>"><?= htmlspecialchars($product['item_name']) ?> — $<?= htmlspecialchars(number_format($product['price'], 2)) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>Quantity
        <input type="number" name="quantity" value="1" min="1" required>
    </label>
    <button type="submit">Place Test Order</button>
</form>

</body>
</html>
