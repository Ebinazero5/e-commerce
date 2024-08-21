<?php
session_start();
include 'db_connection.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details including address
$userQuery = "SELECT street, city, state, pin_code, phone_number FROM users WHERE id = $user_id";
$userResult = mysqli_query($connection, $userQuery);
$user = mysqli_fetch_assoc($userResult);

// Calculate default delivery date (10 days from today)
$defaultDeliveryDate = date('Y-m-d', strtotime('+10 days'));

// Fetch cart items for the logged-in user
$cartQuery = "
    SELECT p.id, p.name, p.price, c.quantity
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = $user_id
";
$cartResult = mysqli_query($connection, $cartQuery);

mysqli_close($connection);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Place Order</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Place Your Order</h1>
        <h2>Delivery Address</h2>
        <p>
            <?php echo htmlspecialchars($user['street']); ?><br>
            <?php echo htmlspecialchars($user['city']); ?><br>
            <?php echo htmlspecialchars($user['state']); ?><br>
            <?php echo htmlspecialchars($user['pin_code']); ?><br>
            <?php echo htmlspecialchars($user['phone_number']); ?><br>
        </p>

        <h2>Cart Items</h2>
        <?php if (mysqli_num_rows($cartResult) > 0): ?>
        <ul class="list-group mb-3">
            <?php while ($row = mysqli_fetch_assoc($cartResult)): ?>
            <li class="list-group-item">
                <?php echo htmlspecialchars($row['name']); ?> - Quantity: <?php echo htmlspecialchars($row['quantity']); ?>
                <span class="badge bg-primary float-end">$<?php echo htmlspecialchars($row['price']); ?></span>
            </li>
            <?php endwhile; ?>
        </ul>
        <?php else: ?>
        <div class="alert alert-info">Your cart is empty.</div>
        <?php endif; ?>

        <h2>Order Details</h2>
        <form action="stripe_order_page.php" method="post">
            <div class="mb-3">
                <label for="delivery_date" class="form-label">Delivery Date</label>
                <input name="delivery_date" id="delivery_date" class="form-control" value="<?php echo $defaultDeliveryDate; ?>" required>
            </div>
            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
            <button type="submit" class="btn btn-primary">Proceed to Payment</button>
        </form>
    </div>
</body>
</html>
