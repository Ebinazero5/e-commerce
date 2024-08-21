<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = mysqli_real_escape_string($connection, $_POST['id']);
    $name = mysqli_real_escape_string($connection, $_POST['name']);
    $description = mysqli_real_escape_string($connection, $_POST['description']);
    $price = mysqli_real_escape_string($connection, $_POST['price']);
    $category = mysqli_real_escape_string($connection, $_POST['category']);
    
    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageTmpName = $_FILES['image']['tmp_name'];
        $imageType = $_FILES['image']['type'];
        $imageSize = $_FILES['image']['size'];

        // Validate image file type and size
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($imageType, $allowedTypes) && $imageSize <= 2 * 1024 * 1024) { // 2MB max size
            $imageData = file_get_contents($imageTmpName);
            $imageData = mysqli_real_escape_string($connection, $imageData);
            $imageQuery = ", image='$imageData'";
        } else {
            echo "<div class='alert alert-danger'>Invalid image type or size. Allowed types: JPEG, PNG, GIF. Max size: 2MB.</div>";
            exit;
        }
    } else {
        $imageQuery = ''; // No new image uploaded
    }

    // Update query
    $query = "UPDATE products SET name='$name', description='$description', price='$price', category='$category' $imageQuery WHERE id='$id'";

    if (mysqli_query($connection, $query)) {
        echo "<div class='alert alert-success'>Product updated successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . mysqli_error($connection) . "</div>";
        echo "<div class='alert alert-info'>Query: $query</div>";
    }

    mysqli_close($connection);
} else {
    $id = mysqli_real_escape_string($connection, $_GET['id']);
    $query = "SELECT * FROM products WHERE id='$id'";
    $result = mysqli_query($connection, $query);
    $product = mysqli_fetch_assoc($result);
    mysqli_close($connection);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Edit Product</h1>
        <form action="list_products.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($product['id']); ?>">
            <div class="mb-3">
                <label for="name" class="form-label">Product Name</label>
                <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($product['name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" class="form-control" required><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="text" name="price" id="price" class="form-control" value="<?php echo htmlspecialchars($product['price']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <input type="text" name="category" id="category" class="form-control" value="<?php echo htmlspecialchars($product['category']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Product Image</label>
                <input type="file" name="image" id="image" class="form-control" accept="image/*">
                <?php if (!empty($product['image'])): ?>
                <img src="data:image/jpeg;base64,<?php echo base64_encode($product['image']); ?>" alt="Product Image" style="max-height: 200px;" class="mt-3">
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary">Update Product</button>
        </form>
    </div>
</body>
</html>
