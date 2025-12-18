<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Manager') {
    header("Location: login.php");
    exit();
}

$manager_id = $_SESSION['user_id'];
$conn = getDBConnection();

$sql = "SELECT e.*, GROUP_CONCAT(p.property_name SEPARATOR ', ') as assigned_properties
        FROM employees e
        LEFT JOIN properties p ON e.employee_id = p.employee_id
        WHERE e.manager_id = ?
        GROUP BY e.employee_id";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $manager_id);
$stmt->execute();
$employees = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Employees - Rental Flow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="manage_employees.js" defer></script>
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
                    <a class="nav-link text-white" href="manage_properties.php"><?php echo htmlspecialchars($_SESSION['business']) ?> Properties</a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link text-white active bg-primary" href="manage_employees.php"><?php echo htmlspecialchars($_SESSION['business']) ?> Employees</a>
                </li>
                <li class="nav-item mt-5">
                    <a class="nav-link text-danger" href="logout.php">Logout</a>
                </li>
            </ul>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><?php echo htmlspecialchars($_SESSION['business']) ?> Employees</h2>
                <a href="add_employee.php" class="btn btn-primary">
                    + Add New Employee
                </a>
            </div>

            <div class="row mb-4">
                <div class="col-md-5">
                    <input type="text" id="searchInput" class="form-control" placeholder="Search employees by name...">
                </div>
            </div>

            <div class="row">
                <?php if ($employees->num_rows > 0): ?>
                    <?php while($row = $employees->fetch_assoc()): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h5 class="card-title fw-bold text-primary">
                                            <?php echo htmlspecialchars($row['employee_name']); ?>
                                        </h5>
                                        <span class="badge bg-secondary">Agent</span>
                                    </div>
                                    
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item px-0">
                                            <small class="text-muted d-block">Phone</small>
                                            <?php echo htmlspecialchars($row['employee_phone']); ?>
                                        </li>
                                        <li class="list-group-item px-0">
                                            <small class="text-muted d-block">Email</small>
                                            <a href="mailto:<?php echo htmlspecialchars($row['employee_email']); ?>" class="text-decoration-none">
                                                <?php echo htmlspecialchars($row['employee_email']); ?>
                                            </a>
                                        </li>
                                        <li class="list-group-item px-0">
                                            <small class="text-muted d-block">Assigned Properties</small>
                                            <?php if (!empty($row['assigned_properties'])): ?>
                                                <span class="text-dark">
                                                    <?php echo htmlspecialchars($row['assigned_properties']); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted fst-italic">No properties assigned</span>
                                            <?php endif; ?>
                                        </li>
                                    </ul>

                                    <div class="mt-3 d-grid">
                                        <a href="edit_employee.php?id=<?php echo $row['employee_id']; ?>" class="btn btn-outline-dark btn-sm">Manage Assignment</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <h3 class="text-muted">No employees found.</h3>
                        <p>Click the button above to add your first employee.</p>
                    </div>
                <?php endif; ?>
            </div>
            
        </main>
    </div>
</div>

</body>
</html>