<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Manager') {
    header("Location: login.php");
    exit();
}

$manager_id = $_SESSION['user_id'];
$conn = getDBConnection();

$sql_props = "SELECT COUNT(*) as count, SUM(property_revenue) as revenue FROM properties WHERE manager_id = ?";
$stmt = $conn->prepare($sql_props);
$stmt->bind_param("i", $manager_id);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();

$sql_emps = "SELECT COUNT(*) as count FROM employees WHERE manager_id = ?";
$stmt = $conn->prepare($sql_emps);
$stmt->bind_param("i", $manager_id);
$stmt->execute();
$emp_stats = $stmt->get_result()->fetch_assoc();

$sql_logs = "SELECT l.log_description, l.log_date, p.property_name, e.employee_name 
             FROM logs l
             JOIN properties p ON l.property_id = p.property_id
             JOIN employees e ON l.employee_id = e.employee_id
             WHERE p.manager_id = ?
             ORDER BY l.log_date DESC LIMIT 5";
$stmt = $conn->prepare($sql_logs);
$stmt->bind_param("i", $manager_id);
$stmt->execute();
$recent_logs = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manager Dashboard - Rental Flow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-icon { font-size: 2rem; opacity: 0.3; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        
        <nav class="col-md-3 col-lg-2 d-md-block bg-dark sidebar text-white p-3" style="min-height:100vh;">
            <h4 class="text-center my-3">Rental Flow</h4>
            <hr>
            <ul class="nav flex-column">
                <li class="nav-item mb-2">
                    <a class="nav-link text-white active bg-primary" href="manager_dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link text-white" href="manage_properties.php"><?php echo htmlspecialchars($_SESSION['business']) ?> Properties</a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link text-white" href="manage_employees.php"><?php echo htmlspecialchars($_SESSION['business']) ?> Employees</a>
                </li>
                <li class="nav-item mt-5">
                    <a class="nav-link text-danger" href="logout.php">Logout</a>
                </li>
            </ul>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 bg-light min-vh-100">
            
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary">Export Report</button>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card text-white bg-success mb-3 shadow">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title"><?php echo htmlspecialchars($_SESSION['business']) ?> Revenue</h5>
                                <p class="card-text fs-4">$<?php echo number_format($stats['revenue'] ?? 0, 2); ?></p>
                            </div>
                            <span class="fs-1 opacity-25">$</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card text-white bg-primary mb-3 shadow">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title"><?php echo htmlspecialchars($_SESSION['business']) ?> Properties</h5>
                                <p class="card-text fs-4"><?php echo $stats['count']; ?> Units</p>
                            </div>
                            <span class="fs-1 opacity-25">🏠</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card text-white bg-warning mb-3 shadow">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title"><?php echo htmlspecialchars($_SESSION['business']) ?> Employees</h5>
                                <p class="card-text fs-4"><?php echo $emp_stats['count']; ?> Active</p>
                            </div>
                            <span class="fs-1 opacity-25">👤</span>
                        </div>
                    </div>
                </div>
            </div>

            <h3 class="mt-4">Recent Activity Logs</h3>
            <div class="table-responsive bg-white rounded shadow p-3">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Property</th>
                            <th>Employee</th>
                            <th>Event</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $recent_logs->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo date("M d, Y h:i A", strtotime($row['log_date'])); ?></td>
                            <td><?php echo htmlspecialchars($row['property_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['employee_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['log_description']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                        
                        <?php if($recent_logs->num_rows === 0): ?>
                            <tr><td colspan="4" class="text-center text-muted">No activity recorded yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </main>
    </div>
</div>

</body>
</html>