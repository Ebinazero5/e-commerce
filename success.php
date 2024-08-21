<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1 class="mt-5">Payment Successful</h1>
        <p class="lead">Thank you for your purchase! Your payment has been processed successfully.</p>
        
        <h2>Order Summary</h2>
        <p>Your order has been confirmed and is being processed. You will receive a confirmation email shortly.</p>
        
        <a href="search_product.php" class="btn btn-primary">Return to Homepage</a>
    </div>
</body>
</html>
