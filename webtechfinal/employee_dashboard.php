<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Employee') {
    header("Location: login.php");
    exit();
}

$emp_id = $_SESSION['user_id'];
$conn = getDBConnection();

$sql = "SELECT p.*, t.tenant_name 
        FROM properties p 
        LEFT JOIN tenants t ON p.tenant_id = t.tenant_id 
        WHERE p.employee_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $emp_id);
$stmt->execute();
$properties = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Dashboard - Rental Flow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .prop-card-img { height: 180px; object-fit: cover; }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
  <div class="container">
    <a class="navbar-brand" href="#">Rental Flow | Employee Portal</a>
    <div class="d-flex text-white align-items-center">
        <span class="me-3">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
        <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container">
    <h3 class="mb-4">My Assigned Properties</h3>

    <div class="row">
        <?php if ($properties->num_rows > 0): ?>
            <?php while($row = $properties->fetch_assoc()): ?>
                <?php 
                    $status_text = $row['tenant_id'] ? 'Occupied' : 'Vacant';
                    $status_color = $row['tenant_id'] ? 'bg-success' : 'bg-warning text-dark';
                    $img = !empty($row['property_image']) ? "uploads/".$row['property_image'] : "uploads/default.jpg";
                ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <img src="<?php echo htmlspecialchars($img); ?>" class="prop-card-img card-img-top">
                        <div class="position-absolute top-0 end-0 m-2 badge <?php echo $status_color; ?>">
                            <?php echo $status_text; ?>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['property_name']); ?></h5>
                            <p class="small text-muted mb-2">
                                <?php echo htmlspecialchars(substr($row['property_description'], 0, 60)) . '...'; ?>
                            </p>
                            
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">Tenant:</span>
                                <span class="fw-bold"><?php echo htmlspecialchars($row['tenant_name'] ?? 'None'); ?></span>
                            </div>

                            <div class="d-grid">
                                <a href="employee_property_details.php?id=<?php echo $row['property_id']; ?>" class="btn btn-primary">Manage Unit</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <div class="alert alert-info">
                    <h4>No Assignments Yet</h4>
                    <p>Your manager has not assigned any properties to you yet.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>