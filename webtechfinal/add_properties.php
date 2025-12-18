<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Manager') {
    header("Location: login.php");
    exit();
}

$errors = [];
$success = "";
$manager_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $p_name = trim($_POST['property_name']);
    $p_desc = trim($_POST['property_description']);
    $p_rent = trim($_POST['property_rent']);
    
    if (empty($p_name)) $errors['name'] = "Property name is required.";
    if (empty($p_rent) || !is_numeric($p_rent)) $errors['rent'] = "Rent must be a valid number.";
    
    $final_image_name = "default.webp";
    
    if (isset($_FILES['property_image']) && $_FILES['property_image']['error'] === 0) {
        
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $filename = $_FILES['property_image']['name'];
        $filesize = $_FILES['property_image']['size'];
        $file_tmp = $_FILES['property_image']['tmp_name'];
        
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (!in_array($ext, $allowed)) {
            $errors['image'] = "Only JPG, PNG, WEBP, and GIF files are allowed.";
        } elseif ($filesize > 5 * 1024 * 1024) {
            $errors['image'] = "File size must be less than 5MB.";
        } else {
            $final_image_name = "property_" . uniqid() . "." . $ext;
            $destination = "uploads/" . $final_image_name;
            
            if (!file_exists('uploads')) {
                mkdir('uploads', 0777, true);
            }
            
            if (!move_uploaded_file($file_tmp, $destination)) {
                $errors['image'] = "Failed to upload image to server.";
            }
        }
    }

    if (empty($errors)) {
        $conn = getDBConnection();
        
        $sql = "INSERT INTO properties (property_name, property_description, property_rent, property_image, manager_id) 
                VALUES (?, ?, ?, ?, ?)";
                
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdsi", $p_name, $p_desc, $p_rent, $final_image_name, $manager_id);
        
        if ($stmt->execute()) {
            $success = "Property added successfully! Redirecting...";
            header("refresh:2;url=manage_properties.php");
        } else {
            $errors['db'] = "Database Error: " . $conn->error;
        }
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Property - Rental Flow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="add_property.js" defer></script>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="manager_dashboard.php">Rental Flow</a>
    <div class="d-flex">
        <a href="manage_properties.php" class="btn btn-outline-light btn-sm">Cancel</a>
    </div>
  </div>
</nav>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Add New Property</h4>
                </div>
                <div class="card-body p-4">

                    <?php if($success): ?>
                        <div class="alert alert-success">
                            <?php echo $success; ?>
                        </div>
                    <?php endif; ?>

                    <?php if(isset($errors['db'])): ?>
                        <div class="alert alert-danger">
                            <?php echo $errors['db']; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data" novalidate>
                        
                        <div class="mb-3">
                            <label for="p_name" class="form-label">Property Name</label>
                            <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" 
                                   id="p_name" name="property_name" 
                                   value="<?php echo isset($_POST['property_name']) ? htmlspecialchars($_POST['property_name']) : ''; ?>" required>
                            <div class="invalid-feedback"><?php echo $errors['name'] ?? ''; ?></div>
                        </div>

                        <div class="mb-3">
                            <label for="p_desc" class="form-label">Description</label>
                            <textarea class="form-control" id="p_desc" name="property_description" rows="3"><?php echo isset($_POST['property_description']) ? htmlspecialchars($_POST['property_description']) : ''; ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="p_rent" class="form-label">Monthly Rent ($)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control <?php echo isset($errors['rent']) ? 'is-invalid' : ''; ?>" 
                                       id="p_rent" name="property_rent" 
                                       value="<?php echo isset($_POST['property_rent']) ? htmlspecialchars($_POST['property_rent']) : ''; ?>" required>
                                <div class="invalid-feedback"><?php echo $errors['rent'] ?? ''; ?></div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="p_image" class="form-label">Property Image</label>
                            <input type="file" class="form-control <?php echo isset($errors['image']) ? 'is-invalid' : ''; ?>" 
                                   id="p_image" name="property_image" accept="image/*">
                            <div class="form-text">Accepted formats: JPG, PNG, WEBP. Max size: 5MB.</div>
                            <div class="invalid-feedback"><?php echo $errors['image'] ?? ''; ?></div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">Save Property</button>
                            <a href="manage_properties.php" class="btn btn-outline-secondary">Back to List</a>
                        </div>

                    </form>
                    </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>