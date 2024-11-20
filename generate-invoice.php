<?php
require "init.php"; 

$customers = $stripe->customers->all();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_id = $_POST['customer_id'];
    $products_selected = isset($_POST['products']) ? $_POST['products'] : [];

    if (empty($customer_id) || empty($products_selected)) {
        die('Please select a customer and products.');
    }
    try {
        
        $customer = $stripe->customers->retrieve($customer_id);
        
        $invoice = $stripe->invoices->create([
            'customer' => $customer_id,
        ]);
        
        foreach ($products_selected as $product_id) {
            $product = $stripe->products->retrieve($product_id); 
            $price = $product->default_price; 
            
            $stripe->invoiceItems->create([
                'customer' => $customer_id,
                'price' => $price,
                'invoice' => $invoice->id,
            ]);
        }   

        $stripe->invoices->finalizeInvoice($invoice->id);

        $invoice = $stripe->invoices->retrieve($invoice->id);
        
        $invoice_data = [
            'invoice_id' => htmlspecialchars($invoice->id),
            'amount_due' => strtoupper($invoice->currency) . ' ' . number_format($invoice->amount_due / 100, 2),
            'invoice_pdf' => $invoice->invoice_pdf,
            'hosted_invoice_url' => $invoice->hosted_invoice_url,
        ];

    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Invoice</title>
    <link rel="stylesheet" href="Stylesheets/generate-invoice.css">
</head>
<body class="bg-light">

    <div class="container">
        <h1 class="header">Generate Invoice</h1>
        
        <form action="generate-invoice.php" method="POST" id="invoice-form">
            <div class="form-group">
                <label for="customer">Select Customer:</label>
                <select id="customer" name="customer_id" class="form-control" required>
                    <option value="">Select a customer</option>
                    <?php
                    foreach ($customers->data as $customer) {
                        echo "<option value='" . $customer->id . "'>" . htmlspecialchars($customer->name) . "</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label>Select Products:</label><br>
                <?php
                try {
                    $products = $stripe->products->all();
                    foreach ($products->data as $product) {
                        echo "<div class='form-check'>
                                <input class='form-check-input' type='checkbox' name='products[]' value='" . $product->id . "' id='product_" . $product->id . "'>
                                <label class='form-check-label' for='product_" . $product->id . "'>" . htmlspecialchars($product->name) . "</label>
                              </div>";
                    }
                } catch (Exception $e) {
                    echo "<p>No products found</p>";
                }
                ?>
            </div>

            <button type="submit" class="btn" id="generate-invoice-btn">Generate Invoice</button>
        </form>
    </div>

    <?php if (isset($invoice_data)): ?>
        <div id="invoice-modal" class="modal">
            <div class="modal-content">
                <span class="close-btn" id="close-btn">&times;</span>
                <h3>Invoice Generated</h3>
                <p><strong>Invoice ID:</strong> <?= $invoice_data['invoice_id'] ?></p>
                <p><strong>Amount Due:</strong> <?= $invoice_data['amount_due'] ?></p>
                <p><a href="<?= $invoice_data['invoice_pdf'] ?>" class="btn btn-success" download>Download Invoice PDF</a></p>
                <p><a href="<?= $invoice_data['hosted_invoice_url'] ?>" class="btn btn-primary" target="_blank">Go to Payment Page</a></p>
            </div>
        </div>
    <?php elseif (isset($error_message)): ?>
        <div class="error-message"><?= $error_message ?></div>
    <?php endif; ?>

    <script>
        document.querySelector('form').addEventListener('submit', function(event) {
            var selectedProducts = document.querySelectorAll('input[name="products[]"]:checked');
            if (selectedProducts.length === 0) {
                alert('Please select at least one product.');
                event.preventDefault(); 
            }
        });

        const modal = document.getElementById('invoice-modal');
        const closeBtn = document.getElementById('close-btn');

        <?php if (isset($invoice_data)): ?>
            modal.style.display = 'block';
        <?php endif; ?>

        closeBtn.addEventListener('click', function() {
            modal.style.display = 'none';
        });

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        };
    </script>

</body>
</html>