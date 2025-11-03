<?php
require_once 'config.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Developer Dashboard</title>
        <script src="dashboard.js"></script>
    </head>
    <body>
        <header>
            <h1>Welcome to the Developer Dashboard</h1>
        </header>
        <nav>
            <a href="add_product.php">Add New Product</a>
            <a href="logout.php">Logout</a>
        </nav>
        <main>
            <h2>Hello, <?php echo htmlspecialchars($user_name); ?>!</h2>
            <section id="productsList">
                <h3>Your Products</h3>
                <div id="productsContainer">
                    <!-- Products will be dynamically loaded here -->
                </div>
            </section>
        </main>
    </body>
</html>
