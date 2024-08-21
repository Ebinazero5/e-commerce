<?php
session_start();
require 'db_connection.php';
require 'vendor/autoload.php'; // Include the Stripe PHP library

\Stripe\Stripe::setApiKey('sk_test_51PnF6ERudXHrdoCr72Q6mzMyD6rTdGYvwcWrXsfq7m1teqa5xD0WPscl2vwUT00HD1ARtwDkw3nGtyH89Y4LWAzy002QzKaXpe'); // Replace with your actual Secret Key

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch cart items for the logged-in user
$cartQuery = "
    SELECT p.id, p.name, p.price, c.quantity
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = $user_id
";
$cartResult = mysqli_query($connection, $cartQuery);

// Prepare line items for Stripe Checkout
$line_items = [];
while ($row = mysqli_fetch_assoc($cartResult)) {
    $line_items[] = [
        'price_data' => [
            'currency' => 'usd',
            'product_data' => [
                'name' => $row['name'],
            ],
            'unit_amount' => $row['price'] * 100, // Price in cents
        ],
        'quantity' => $row['quantity'],
    ];
}

// Replace 'yourdomain.com' with your actual domain name
$your_domain = 'http://localhost/e-com'; // Use your actual domain name when going live

// Create a new Stripe Checkout session with UPI and Netbanking options
$checkout_session = \Stripe\Checkout\Session::create([
    'payment_method_types' => ['card'], // Stripe Checkout supports specific payment methods. UPI and Netbanking are not standard options here.
    'line_items' => $line_items, // No need to wrap in another array
    'mode' => 'payment',
    'success_url' => $your_domain . '/success.php',
    'cancel_url' => $your_domain . '/cancel.php',
]);

// Redirect to Stripe Checkout
header("Location: " . $checkout_session->url);
exit();

mysqli_close($connection);
