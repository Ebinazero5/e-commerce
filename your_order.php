<?php
include 'db_connection.php';

// Handle order deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_order_id'])) {
    $order_id = mysqli_real_escape_string($connection, $_POST['delete_order_id']);

    // Delete the order
    $deleteQuery = "DELETE FROM orders WHERE id = '$order_id'";
    if (mysqli_query($connection, $deleteQuery)) {
        echo "<div class='alert alert-success'>Order deleted successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . mysqli_error($connection) . "</div>";
    }
}

// Fetch all orders
$query = "
    SELECT o.id, o.payment_mode, o.delivery_date, o.order_date, 
           o.street, o.city, o.state, o.pin_code, o.phone_numbers, u.first_name
    FROM orders o
    JOIN users u ON o.user_id = u.id
";

$result = mysqli_query($connection, $query);

if (!$result) {
    die("Error fetching orders: " . mysqli_error($connection));
}

mysqli_close($connection);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1 class="my-4">Orders List</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>First Name</th>
                    <th>Payment Mode</th>
                    <th>Delivery Date</th>
                    <th>Order Date</th>
                    <th>Street</th>
                    <th>City</th>
                    <th>State</th>
                    <th>Pin Code</th>
                    <th>Phone Numbers</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['payment_mode']); ?></td>
                            <td><?php echo htmlspecialchars($row['delivery_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['order_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['street']); ?></td>
                            <td><?php echo htmlspecialchars($row['city']); ?></td>
                            <td><?php echo htmlspecialchars($row['state']); ?></td>
                            <td><?php echo htmlspecialchars($row['pin_code']); ?></td>
                            <td><?php echo htmlspecialchars($row['phone_numbers']); ?></td>
                            <td>
                                <!-- Delete button form -->
                                <form action="" method="post" style="display:inline;">
                                    <input type="hidden" name="delete_order_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">cancel your order</button>
                                </form>
                            </td>
                            
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="11" class="text-center">No orders found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
