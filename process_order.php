<?php
// Include database connection file
include('db_connection.php');

// Include Stripe PHP library
require 'vendor/autoload.php'; // Adjust the path if necessary

// Retrieve form data
$paymentMode = mysqli_real_escape_string($connection, $_POST['payment_mode'] ?? '');
$deliveryDate = mysqli_real_escape_string($connection, $_POST['delivery_date'] ?? '');
$orderDate = date('Y-m-d');
$user_id = mysqli_real_escape_string($connection, $_POST['user_id'] ?? '');

// Check if required fields are set
$user = [
    'street' => mysqli_real_escape_string($connection, $_POST['street'] ?? ''),
    'city' => mysqli_real_escape_string($connection, $_POST['city'] ?? ''),
    'state' => mysqli_real_escape_string($connection, $_POST['state'] ?? ''),
    'pin_code' => mysqli_real_escape_string($connection, $_POST['pin_code'] ?? ''),
    'first_name' => mysqli_real_escape_string($connection, $_POST['first_name'] ?? '')
];

// Check for duplicates
$checkQuery = "
    SELECT COUNT(*) AS count
    FROM orders
    WHERE user_id = $user_id
      AND delivery_date = '$deliveryDate'
      AND payment_mode = '$paymentMode'
      AND order_date = '$orderDate'
";

$result = mysqli_query($connection, $checkQuery);
$row = mysqli_fetch_assoc($result);

if ($row['count'] > 0) {
    echo "Order already exists.";
} else {
    // Process payment and insert order
    if ($paymentMode === 'credit_card') {
        // Process payment with Stripe
        $stripeToken = $_POST['stripeToken'] ?? '';
        $totalAmount = 5000; // Replace with your logic to calculate the total amount

        if (empty($stripeToken)) {
            echo "Stripe token is missing.";
            exit();
        }

        try {
            \Stripe\Stripe::setApiKey('sk_test_51PnF6ERudXHrdoCr72Q6mzMyD6rTdGYvwcWrXsfq7m1teqa5xD0WPscl2vwUT00HD1ARtwDkw3nGtyH89Y4LWAzy002QzKaXpe');

            $charge = \Stripe\Charge::create([
                'amount' => $totalAmount * 100, // amount in cents
                'currency' => 'usd',
                'description' => 'Order description here',
                'source' => $stripeToken,
            ]);

            // Payment successful, insert order into database
            $insertOrderQuery = "
                INSERT INTO orders (user_id, payment_mode, delivery_date, order_date, street, city, state, pin_code, first_name)
                VALUES ($user_id, '$paymentMode', '$deliveryDate', '$orderDate', '{$user['street']}', '{$user['city']}', '{$user['state']}', '{$user['pin_code']}', '{$user['first_name']}')
            ";

            if (mysqli_query($connection, $insertOrderQuery)) {
                echo "Order placed successfully!";
            } else {
                echo "Error: " . mysqli_error($connection);
            }
        } catch (\Stripe\Exception\ApiErrorException $e) {
            echo "Payment failed: " . $e->getMessage();
        }
    } elseif ($paymentMode === 'paypal') {
        // Redirect to PayPal payment gateway
        header("Location: paypal_payment_page.php"); // replace with your actual PayPal integration page
        exit();
    } else {
        // Handle other payment methods (e.g., Cash on Delivery)
        $insertOrderQuery = "
            INSERT INTO orders (user_id, payment_mode, delivery_date, order_date, street, city, state, pin_code, first_name)
            VALUES ($user_id, '$paymentMode', '$deliveryDate', '$orderDate', '{$user['street']}', '{$user['city']}', '{$user['state']}', '{$user['pin_code']}', '{$user['first_name']}')
        ";

        if (mysqli_query($connection, $insertOrderQuery)) {
            echo "Order placed successfully!";
        } else {
            echo "Error: " . mysqli_error($connection);
        }
    }
}

// Close database connection
mysqli_close($connection);
?>
