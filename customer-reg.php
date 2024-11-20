<?php
require "init.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $postal_code = $_POST['postal_code'];

    try {
        $customer = $stripe->customers->create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'address' => [
                'line1' => $address,
                'city' => $city,
                'postal_code' => $postal_code,
                'country' => 'PH'
            ]
        ]);

        $message = "Customer Created Successfully!<br>";
        $message .= "Customer ID: " . htmlspecialchars($customer->id) . "<br>";
        $message .= "Name: " . htmlspecialchars($customer->name) . "<br>";
        $message .= "Email: " . htmlspecialchars($customer->email) . "<br>";

    } catch (Exception $e) {
        $error_message = "Error: " . htmlspecialchars($e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="Stylesheets/customer-reg.css"> 
</head>
<body>

    <div class="image-section"></div>
    
    <div class="form-section">
        <div class="container">
            <h1 class="text-center text-primary mb-4">Customer Registration</h1>

            <?php if (!empty($message)): ?>
                <div class="alert alert-success">
                    <?php echo $message; ?>
                </div>
            <?php elseif (!empty($error_message)): ?>
                <div class="alert alert-danger">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="tel" id="phone" name="phone" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" id="city" name="city" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="postal_code">Postal Code</label>
                    <input type="text" id="postal_code" name="postal_code" class="form-control" required>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn-register">Register</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>