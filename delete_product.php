<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $id = mysqli_real_escape_string($connection, $_POST['id']);
        
        // Start a transaction
        mysqli_begin_transaction($connection);

        try {
            // First, delete from cart
            $deleteCartQuery = "DELETE FROM cart WHERE product_id='$id'";
            mysqli_query($connection, $deleteCartQuery);

            // Then, delete the product
            $deleteProductQuery = "DELETE FROM products WHERE id='$id'";
            mysqli_query($connection, $deleteProductQuery);

            // Commit the transaction
            mysqli_commit($connection);

            // Redirect to list_products.php after successful deletion
            header('Location: list_products.php');
            exit();
        } catch (Exception $e) {
            // Rollback the transaction if something went wrong
            mysqli_rollback($connection);
            echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
        }

        mysqli_close($connection);
    } else {
        echo "<div class='alert alert-danger'>Product ID is required.</div>";
    }
}
?>
