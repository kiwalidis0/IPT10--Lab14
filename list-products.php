<?php
require "init.php";

$products = $stripe->products->all();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List</title>
    <link rel="stylesheet" href="Stylesheets/list-products.css">
</head>
<body>

<div class="cover">
    <h1>Products</h1>
</div>

<div class="product-grid">
    <?php foreach ($products->data as $product): ?>
        <div class="product-card">
            <div class="product-image">
                <img src="<?= (isset($product->images) && count($product->images) > 0) ? $product->images[0] : 'https://via.placeholder.com/200' ?>" alt="Product Image">
            </div>
            <div class="product-details">
                <h3><?= htmlspecialchars($product->name) ?></h3>
                <p><strong>Description:</strong> <?= htmlspecialchars($product->description) ?: 'No description available' ?></p>
                
                <?php if (isset($product->default_price)): ?>
                    <?php
                        $price = $stripe->prices->retrieve($product->default_price);
                    ?>
                    <p class="price"><?= strtoupper($price->currency) . ' ' . number_format($price->unit_amount / 100, 2) ?></p>
                <?php else: ?>
                    <p class="no-price">No price available for this product.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>