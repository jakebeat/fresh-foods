<?php
require __DIR__ . '/db-connect.php';

$products = $pdo->query('SELECT item_id, item_name, price, stock_qty, image_url FROM inventory ORDER BY item_id')->fetchAll();
$orderSuccess = isset($_GET['order']) && $_GET['order'] === 'success';
$fallbackImg = 'https://freshfoods-jbeaty-assets.s3.us-east-2.amazonaws.com/products/banana-2449019_640.jpg';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Fresh Foods</title>
<style>
:root {
  --green: #2e7d32;
  --green-dark: #1b5e20;
  --green-light: #e8f5e9;
  --cream: #fdfbf7;
  --ink: #263228;
  --muted: #6b7a6e;
  --card-border: #e3e8e3;
  --amber: #b45309;
  --amber-bg: #fef3c7;
}
* { box-sizing: border-box; }
body { font-family: Georgia, serif; margin: 0; background: var(--cream); color: var(--ink); }
header { background: var(--green-dark); color: #fff; padding: 18px 24px; display: flex; align-items: baseline; gap: 14px; flex-wrap: wrap; }
header .logo { font-size: 1.6rem; font-weight: bold; letter-spacing: 0.5px; }
header .tagline { font-family: Arial, sans-serif; font-size: 0.85rem; color: #c8e6c9; }
main { max-width: 1000px; margin: 0 auto; padding: 28px 20px 60px; }
.success { background: var(--green-light); border: 1px solid var(--green); color: var(--green-dark); padding: 14px 18px; border-radius: 8px; margin-bottom: 24px; font-family: Arial, sans-serif; }
h2.section { font-size: 1.4rem; border-bottom: 2px solid var(--green); padding-bottom: 8px; margin: 34px 0 20px; }
.grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px; }
.product { background: #fff; border: 1px solid var(--card-border); border-radius: 10px; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 1px 3px rgba(38,50,40,0.08); transition: box-shadow 0.15s ease, transform 0.15s ease; }
.product:hover { box-shadow: 0 6px 16px rgba(38,50,40,0.14); transform: translateY(-2px); }
.product img { width: 100%; height: 160px; object-fit: cover; display: block; background: var(--green-light); }
.product .body { padding: 14px 16px 16px; display: flex; flex-direction: column; gap: 6px; flex: 1; }
.product h3 { margin: 0; font-size: 1.05rem; }
.price { font-family: Arial, sans-serif; font-weight: bold; color: var(--green-dark); font-size: 1.05rem; }
.stock { font-family: Arial, sans-serif; font-size: 0.8rem; color: var(--muted); margin-top: auto; }
.badge-low { display: inline-block; font-family: Arial, sans-serif; font-size: 0.72rem; font-weight: bold; color: var(--amber); background: var(--amber-bg); border: 1px solid #f6d698; border-radius: 999px; padding: 2px 10px; margin-left: 6px; }
.orderform { background: #fff; border: 1px solid var(--card-border); border-radius: 10px; padding: 24px; max-width: 520px; box-shadow: 0 1px 3px rgba(38,50,40,0.08); font-family: Arial, sans-serif; }
.orderform label { display: block; margin-top: 14px; font-size: 0.85rem; font-weight: bold; }
.orderform input, .orderform select { width: 100%; padding: 10px; margin-top: 6px; border: 1px solid #cdd6cd; border-radius: 6px; font-size: 0.95rem; background: var(--cream); }
.orderform button { margin-top: 20px; padding: 12px 28px; background: var(--green); color: #fff; border: none; border-radius: 6px; font-size: 1rem; font-weight: bold; cursor: pointer; }
.orderform button:hover { background: var(--green-dark); }
.sitefoot { border-top: 1px solid var(--card-border); margin-top: 40px; padding: 18px 24px; font-family: Arial, sans-serif; font-size: 0.75rem; color: var(--muted); text-align: center; }
</style>
</head>
<body>
<header>
  <span class="logo">Fresh Foods</span>
  <span class="tagline">Farm-fresh groceries, delivered direct</span>
</header>
<main>

<?php if ($orderSuccess): ?>
<p class="success">Thanks &mdash; your test order was placed successfully.</p>
<?php endif; ?>

<h2 class="section">Our Products</h2>
<div class="grid">
<?php foreach ($products as $product): ?>
  <div class="product">
    <img src="<?= htmlspecialchars($product['image_url'] ?: $fallbackImg) ?>" alt="<?= htmlspecialchars($product['item_name']) ?>" loading="lazy">
    <div class="body">
      <h3><?= htmlspecialchars($product['item_name']) ?></h3>
      <span class="price">$<?= htmlspecialchars(number_format($product['price'], 2)) ?></span>
      <span class="stock"><?= (int)$product['stock_qty'] ?> in stock<?php if ((int)$product['stock_qty'] <= 5): ?><span class="badge-low">Low stock</span><?php endif; ?></span>
    </div>
  </div>
<?php endforeach; ?>
</div>

<h2 class="section">Place a Test Order</h2>
<form class="orderform" action="order.php" method="post">
<label>Name
<input type="text" name="name" required>
</label>
<label>Email
<input type="email" name="email" required>
</label>
<label>Product
<select name="item_id" required>
<?php foreach ($products as $product): ?>
<option value="<?= (int)$product['item_id'] ?>"><?= htmlspecialchars($product['item_name']) ?> &mdash; $<?= htmlspecialchars(number_format($product['price'], 2)) ?></option>
<?php endforeach; ?>
</select>
</label>
<label>Quantity
<input type="number" name="quantity" value="1" min="1" required>
</label>
<button type="submit">Place Test Order</button>
</form>

</main>
<div class="sitefoot">
  Fresh Foods is a fictional storefront built on AWS (EC2 &middot; RDS &middot; S3) for the Purdue Global IT473 Bachelor's Capstone.
</div>
</body>
</html>
