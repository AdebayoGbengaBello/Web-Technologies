<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Manager') {
    header("Location: login.php");
    exit();
}

$manager_id = $_SESSION['user_id'];
$conn = getDBConnection();

$sql = "SELECT p.*, e.employee_name, t.tenant_name 
        FROM properties p
        LEFT JOIN employees e ON p.employee_id = e.employee_id
        LEFT JOIN tenants t ON p.tenant_id = t.tenant_id
        WHERE p.manager_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $manager_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($_SESSION['business']) ?> Properties - Rental Flow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="properties.js" defer></script>
    <style>
        .property-img {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }
    </style>
</head>
<body class="bg-light">

<div class="container-fluid">
    <div class="row">
        
        <nav class="col-md-3 col-lg-2 d-md-block bg-dark sidebar text-white p-3 min-vh-100">
            <h4 class="text-center my-3">Rental Flow</h4>
            <hr>
            <ul class="nav flex-column">
                <li class="nav-item mb-2">
                    <a class="nav-link text-white" href="manager_dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link text-white active bg-primary" href="manage_properties.php"><?php echo htmlspecialchars($_SESSION['business']) ?>  Properties</a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link text-white" href="manage_employees.php"><?php echo htmlspecialchars($_SESSION['business']) ?>  Employees</a>
                </li>
                <li class="nav-item mt-5">
                    <a class="nav-link text-danger" href="logout.php">Logout</a>
                </li>
            </ul>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><?php echo htmlspecialchars($_SESSION['business']) ?> Properties</h2>
                <a href="add_properties.php" class="btn btn-primary">
                    + Add New Property
                </a>
            </div>

            <div class="row mb-4">
                <div class="col-md-5">
                    <input type="text" id="searchInput" class="form-control" placeholder="Search properties by name, tenant, or agent...">
                </div>
            </div>

            <div class="row">
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <?php 
                            $status_class = $row['tenant_id'] ? 'bg-success' : 'bg-warning text-dark';
                            $status_text = $row['tenant_id'] ? 'Occupied' : 'Vacant';
                            $image_path = "uploads/" . $row['property_image'];
                        ?>
                        <div class="col-md-6 col-lg-4 mb-4"> <div class="card shadow-sm h-100">
                                <img src="<?php echo htmlspecialchars($image_path); ?>" class="card-img-top property-img" alt="Property Image">
                                <div class="position-absolute top-0 end-0 m-2 badge <?php echo $status_class; ?>">
                                    <?php echo $status_text; ?>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($row['property_name']); ?></h5>
                                    <p class="card-text text-muted small">
                                        <?php echo htmlspecialchars(substr($row['property_description'], 0, 80)) . '...'; ?>
                                    </p>
                                    <ul class="list-group list-group-flush mb-3">
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span>Rent:</span>
                                            <strong>$<?php echo number_format($row['property_rent'], 2); ?></strong>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span>Tenant:</span>
                                            <span><?php echo $row['tenant_name'] ?? 'None'; ?></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span>Employee:</span>
                                            <span class="text-primary"><?php echo $row['employee_name'] ?? 'Unassigned'; ?></span>
                                        </li>
                                    </ul>
                                    <div class="d-grid gap-2">
                                        <a href="view_property.php?id=<?php echo $row['property_id']; ?>" class="btn btn-outline-primary">View Details</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <h3 class="text-muted">No properties found.</h3>
                        <p>Click the button above to add your first rental unit.</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
        
    </div> 
</div> 
</body>
</html>