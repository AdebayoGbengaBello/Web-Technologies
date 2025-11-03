<?php
require_once 'config.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login_developer.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

$success = '';
if (isset($_GET['success']) && $_GET['success'] === 'product_added') {
    $success = "Product added successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Developer Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <script src="dashboard.js" defer></script>
</head>
<body>
    <nav>
        <a href="add_product.php">Add New Product</a>
        <a href="logout.php">Logout</a>
    </nav>
    <div class="container">
        <header>
            <h1>Welcome, <?php echo htmlspecialchars($user_name); ?>!</h1>
        </header>
        <main>
            <?php if (!empty($success)): ?>
                <div class="success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            <section id="productsSection">
                <h2>Your Products</h2>
                <div id="productsContainer">
                    <p>Loading your products...</p>
                    </div>
            </section>
        </main>
    </div>
</body>
</html>