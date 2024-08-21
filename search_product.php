<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
   
</head>

<style>
        .topright {
  position: absolute;
  top: 8px;
  right: 16px;
  font-size: 18px;
}
    </style>

<body>
    <div class="container">
        <div class="row">
            <div class="card" style="max-width: 1500px;">
                <div class="card-header">
                    <h1>Search Products</h1>
                    <a href="your_order.php"><button  class="btn btn-primary">your order</button></a>
                    <div class="topright"><a href="cart.php"><i class="fa fa-shopping-cart" style="font-size:36px"></i></a></div>
                    
                </div>
                <div class="card-body">
                    <form action="search_product.php" method="get">
                        <div class="form-group">
                            <label for="search">Search by Name</label>
                            <input type="text" name="search" id="search" class="form-control" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        </div>
                        <br/>
                        <div class="form-group">
                            <label for="category">Filter by Category:</label><br/>
                            <?php
                            // Fetch unique categories from the database
                            $connection = mysqli_connect("localhost:4306", "root", "", "e_com");
                            $categoryQuery = "SELECT DISTINCT category FROM products";
                            $categoryResult = mysqli_query($connection, $categoryQuery);

                            while ($row = mysqli_fetch_assoc($categoryResult)) {
                                $category = $row['category'];
                                echo '<input type="checkbox" name="category[]" value="' . htmlspecialchars($category) . '" '
                                    . (isset($_GET['category']) && in_array($category, $_GET['category']) ? 'checked' : '') . '> '
                                    . htmlspecialchars($category) . '<br/>';
                            }
                            mysqli_close($connection);
                            ?>
                        </div>
                        <br/>
                        <input type="submit" class="btn btn-primary" value="Search">
                    </form>
                    <br/>
                    <?php if (isset($_GET['search']) || isset($_GET['category'])): ?>
                    <div class="row">
                        <?php
                        $connection = mysqli_connect("localhost:4306", "root", "", "e_com");

                        $search = isset($_GET['search']) ? mysqli_real_escape_string($connection, $_GET['search']) : '';
                        $categories = isset($_GET['category']) ? $_GET['category'] : [];

                        $sql = "SELECT * FROM products WHERE name LIKE '%$search%'";

                        if (!empty($categories)) {
                            $categories = array_map(function($cat) use ($connection) {
                                return "'" . mysqli_real_escape_string($connection, $cat) . "'";
                            }, $categories);
                            $categoryString = implode(',', $categories);
                            $sql .= " AND category IN ($categoryString)";
                        }

                        $run = mysqli_query($connection, $sql);

                        if (mysqli_num_rows($run) > 0) {
                            while ($row = mysqli_fetch_assoc($run)) {
                                $uid = $row['id'];
                                $name = $row['name'];
                                $description = $row['description'];
                                $price = $row['price'];
                                $image = $row['image'];

                                // Check if image data exists and encode it
                                if (!empty($image)) {
                                    $imageData = base64_encode($image);
                                    $imageSrc = 'data:image/jpeg;base64,' . $imageData;
                                } else {
                                    $imageSrc = 'https://via.placeholder.com/150'; // Placeholder image if no image data
                                }
                        ?>
                        <div class="col-md-4 mb-4">
                            <div class="card product-card">
                                <img src="<?php echo $imageSrc; ?>" class="card-img-top" alt="Product Image">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($name); ?></h5>
                                    <p class="card-text"><?php echo htmlspecialchars($description); ?></p>
                                    <p class="card-text"><strong>Price:</strong> <?php echo htmlspecialchars($price); ?></p>
                                    <form action="add_to_cart.php" method="post">
                                        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($uid); ?>">
                                        <button type="submit" class="btn btn-info">Add to Cart</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php
                            }
                        } else {
                            echo '<div class="alert alert-info">No products found.</div>';
                        }
                        mysqli_close($connection);
                        ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
