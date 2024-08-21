<?php
session_start();
include 'db_connection.php';


if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); 
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['product_id'])) {
    $product_id = mysqli_real_escape_string($connection, $_POST['product_id']);

    
    $checkQuery = "SELECT * FROM cart WHERE user_id = '$user_id' AND product_id = '$product_id'";
    $checkResult = mysqli_query($connection, $checkQuery);

    if (mysqli_num_rows($checkResult) == 0) {
        
        $query = "INSERT INTO cart (user_id, product_id) VALUES ('$user_id', '$product_id')";
        if (mysqli_query($connection, $query)) {
            echo "<div class='alert alert-success'>Product added to cart!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error: " . mysqli_error($connection) . "</div>";
        }
    } else {
        echo "<div class='alert alert-info'>Product is already in your cart.</div>";
    }
}

mysqli_close($connection);
?>
