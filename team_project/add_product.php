<?php
require_once 'config.php';

$error = '';
$success = '';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = trim($_POST['product_name'] ?? '');
    $price = $_POST['price'] ?? 0.0;
    $description = $_POST['description'] ?? '';
    $link = $_POST['link'] ?? '';
    $errors = [];
    if (empty($product_name) || empty($description) || empty($link)) {
        $errors[] = "All fields are required.";
    }
    if (!filter_var($link, FILTER_VALIDATE_URL)) {
        $errors[] = "Invalid URL format.";
    } 
    
    if (empty($errors)) {
        $conn = getDBConnection();
        $sql = "INSERT INTO Products (developer_id, product_name, description, price, linked_url) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issds", $user_id, $product_name, $description, $price, $link);
        if ($stmt->execute()) {
            $success = "Product added successfully!";
        } else {
            $error = "Failed to add product.";
        }
        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Developer Register</title>
    <body>
        <nav>
            <a href="dashboard.php">Back to Dashboard</a>
            <a href="logout.php">Logout</a>
        </nav>
        <main>
            <?php if (!empty($error)): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
        
            <?php if (!empty($success)): ?>
                <div class="success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            <form method="POST" action="add_product.php">
                <label for="product_name">Product Name:</label>
                <input 
                    type="text" 
                    id="product_name" 
                    name="product_name" 
                    aria-required="true" required>

                <label for="description">Description:</label>
                <textarea 
                    id="description" 
                    name="description" 
                    aria-required="true" required></textarea>

                <label for="price">Price:</label>
                <input type="number" 
                    id="price" 
                    name="price"
                    min="0" 
                    step="0.01"     
                    aria-required="true" required>

                <label for="link">Link:</label>
                <input type="url" 
                    id="link" 
                    name="link" 
                    aria-required="true" required>
                <button type="submit">Add Product</button>
            </form>
        </main>
    </body>
</html>