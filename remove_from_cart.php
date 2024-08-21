<?php
session_start();
include 'db_connection.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['cart_id'])) {
    $cart_id = mysqli_real_escape_string($connection, $_POST['cart_id']);

    // Remove the item from the cart
    $query = "DELETE FROM cart WHERE id = '$cart_id' AND user_id = '$user_id'";
    
    if (mysqli_query($connection, $query)) {
        header('Location: cart.php'); // Redirect to cart page after removal
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error: " . mysqli_error($connection) . "</div>";
    }
}

mysqli_close($connection);
?>
