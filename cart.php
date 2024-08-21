<?php
session_start();
include 'db_connection.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch cart items for the logged-in user
$query = "
    SELECT c.id AS cart_id, p.id, p.name, p.description, p.price, p.image, c.quantity
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = '$user_id'
";

$result = mysqli_query($connection, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Your Cart</h1>
        <?php if (mysqli_num_rows($result) > 0): ?>
        <div class="row">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <?php
                    // Display the product image
                    $imageSrc = !empty($row['image']) ? 'data:image/jpeg;base64,' . base64_encode($row['image']) : 'https://via.placeholder.com/150';
                    ?>
                    <img src="<?php echo $imageSrc; ?>" class="card-img-top" alt="Product Image">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($row['description']); ?></p>
                        <p class="card-text"><strong>Price:</strong> $<?php echo htmlspecialchars($row['price']); ?></p>
                        <p class="card-text"><strong>Quantity:</strong> <?php echo htmlspecialchars($row['quantity']); ?></p>
                        <form action="remove_from_cart.php" method="post">
                            <input type="hidden" name="cart_id" value="<?php echo htmlspecialchars($row['cart_id']); ?>">
                            <button type="submit" class="btn btn-danger">Remove from Cart</button>

                            <a href="order.php" class="btn btn-primary">Place Your Order</a>
                        </form>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
        <div class="alert alert-info">Your cart is empty.</div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
mysqli_close($connection);
?>
