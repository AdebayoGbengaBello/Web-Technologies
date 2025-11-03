<?php
require_once 'config.php';

$error = '';
$success = '';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // --- BUG FIX: Redirect to correct login page ---
    header("Location: login_developer.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Pre-fill form variables (for repopulation on error)
$product_name = '';
$price = 0.0;
$description = '';
$link = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = trim($_POST['product_name'] ?? '');
    $price = $_POST['price'] ?? 0.0;
    $description = $_POST['description'] ?? '';
    $link = $_POST['link'] ?? '';
    
    $errors = [];
    if (empty($product_name) || empty($description) || empty($link)) {
        $errors[] = "Product Name, Description, and Link are required.";
    }
    if (!filter_var($link, FILTER_VALIDATE_URL)) {
        $errors[] = "Invalid URL format.";
    }
    if (!is_numeric($price) || $price < 0) {
        $errors[] = "Price must be a positive number.";
    }
    
    if (empty($errors)) {
        $conn = getDBConnection();
        $sql = "INSERT INTO Products (developer_id, product_name, description, price, linked_url) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issds", $user_id, $product_name, $description, $price, $link);
        
        if ($stmt->execute()) {
            // --- USABILITY FIX: Redirect to dashboard on success ---
            header("Location: dashboard.php?success=product_added");
            exit();
        } else {
            $error = "Failed to add product. Database error.";
        }
        $stmt->close();
        $conn->close();
    } else {
        // --- BUG FIX: Show all validation errors ---
        $error = implode("<br>", $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Product</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="logout.php">Logout</a>
    </nav>
    <div class="form-container">
        <h2>Add New Product</h2>
        
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
    
        <?php if (!empty($success)): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="add_product.php">
            <div class="form-group">
                <label for="product_name">Product Name:</label>
                <input 
                    type="text" 
                    id="product_name" 
                    name="product_name"
                    value="<?php echo htmlspecialchars($product_name); ?>" 
                    aria-required="true" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea 
                    id="description" 
                    name="description" 
                    aria-required="true" required><?php echo htmlspecialchars($description); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="price">Price:</label>
                <input type="number" 
                    id="price" 
                    name="price"
                    value="<?php echo htmlspecialchars($price); ?>"
                    min="0" 
                    step="0.01"     
                    aria-required="true" required>
            </div>
            
            <div class="form-group">
                <label for="link">Link:</label>
                <input type="url" 
                    id="link" 
                    name="link" 
                    value="<?php echo htmlspecialchars($link); ?>"
                    placeholder="https://example.com/product"
                    aria-required="true" required>
            </div>
            
            <button type="submit" class="btn">Add Product</button>
        </form>
    </div>
</body>
</html>