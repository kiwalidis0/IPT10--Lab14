<?php
require "init.php";

$line_items = [];

try {
    $products = $stripe->products->all();

    foreach ($products->data as $product) {
        $prices = $stripe->prices->all(['product' => $product->id]);
        
        foreach ($prices->data as $price) {
            $line_items[] = [
                'price' => $price->id, 
                'quantity' => 1,   
            ];
        }
    }

    $payment_link = $stripe->paymentLinks->create([
        'line_items' => $line_items,
        'after_completion' => [
            'type' => 'redirect',
            'redirect' => [
                'url' => 'https://your-website.com/success', 
            ],
        ],
    ]);

    echo "Payment Link: <a href='" . $payment_link->url . "' target='_blank'>Pay Now</a>";

} catch (Exception $e) {
    echo "Error creating payment link: " . $e->getMessage();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Payment Link</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container mt-5">
        <h1 class="text-center text-primary mb-4">Generate Payment Link</h1>

        <form action="generate-payment-link.php" method="POST">
            <div class="form-group">
                <label>Select Products:</label><br>
                <?php
                foreach ($product_options as $product) {
                    echo "<div class='form-check'>
                            <input class='form-check-input' type='checkbox' name='products[]' value='" . $product['id'] . "' id='product_" . $product['id'] . "'>
                            <label class='form-check-label' for='product_" . $product['id'] . "'>" . htmlspecialchars($product['name']) . "</label>
                          </div>";
                }
                ?>
            </div>

            <button type="submit" class="btn btn-primary">Generate Payment Link</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>