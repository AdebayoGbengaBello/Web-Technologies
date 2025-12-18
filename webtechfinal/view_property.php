<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: manage_properties.php");
    exit();
}

$p_id = $_GET['id'];
$conn = getDBConnection();

$sql = "SELECT p.*, e.employee_name, t.tenant_name, t.tenant_email 
        FROM properties p
        LEFT JOIN employees e ON p.employee_id = e.employee_id
        LEFT JOIN tenants t ON p.tenant_id = t.tenant_id
        WHERE p.property_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $p_id);
$stmt->execute();
$property = $stmt->get_result()->fetch_assoc();

$log_sql = "SELECT l.*, e.employee_name 
            FROM logs l 
            LEFT JOIN employees e ON l.employee_id = e.employee_id 
            WHERE l.property_id = ? 
            ORDER BY l.log_date DESC";
$log_stmt = $conn->prepare($log_sql);
$log_stmt->bind_param("i", $p_id);
$log_stmt->execute();
$logs = $log_stmt->get_result();


$pay_sql = "SELECT p.*, e.employee_name 
            FROM payments p 
            LEFT JOIN employees e ON p.employee_id = e.employee_id 
            WHERE p.property_id = ? 
            ORDER BY p.payment_date DESC";
$pay_stmt = $conn->prepare($pay_sql);
$pay_stmt->bind_param("i", $p_id);
$pay_stmt->execute();
$payments = $pay_stmt->get_result();

if (!$property) die("Property not found.");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($property['property_name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">
    <a href="manage_properties.php" class="btn btn-outline-secondary mb-3">&larr; Back to List</a>
    
    <div class="row">
        <div class="col-md-5">
            <div class="card shadow-sm mb-4">
                <img src="uploads/<?php echo htmlspecialchars($property['property_image']); ?>" class="card-img-top" style="height: 250px; object-fit: cover;">
                <div class="card-body">
                    <h3 class="card-title"><?php echo htmlspecialchars($property['property_name']); ?></h3>
                    <h4 class="text-success">$<?php echo number_format($property['property_rent'], 2); ?> / mo</h4>
                    <p class="card-text mt-3"><?php echo nl2br(htmlspecialchars($property['property_description'])); ?></p>
                    <hr>
                    
                    <h5>Current Status</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>Agent:</strong>
                            <span><?php echo $property['employee_name'] ?? '<span class="text-danger">Unassigned</span>'; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>Tenant:</strong>
                            <span><?php echo $property['tenant_name'] ?? '<span class="text-warning">Vacant</span>'; ?></span>
                        </li>
                    </ul>
                    
                    <div class="mt-3">
                         <a href="delete_property.php?id=<?php echo $p_id; ?>" class="btn btn-danger w-100" onclick="return confirm('Are you sure? This cannot be undone.');">Delete Property</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Payment History</h5>
                    <span class="badge bg-white text-success">
                        <?php echo $payments->num_rows; ?> Transactions
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-hover mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Description</th>
                                    <th>Recorded By</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($pay = $payments->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo date("M d, Y", strtotime($pay['payment_date'])); ?></td>
                                    <td class="fw-bold text-success">$<?php echo number_format($pay['payment_amount'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($pay['payment_description']); ?></td>
                                    <td><small class="text-muted"><?php echo htmlspecialchars($pay['employee_name']); ?></small></td>
                                </tr>
                                <?php endwhile; ?>

                                <?php if($payments->num_rows === 0): ?>
                                    <tr><td colspan="4" class="text-center p-3 text-muted">No payments recorded yet.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Activity Logs</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Agent</th>
                            <th>Event Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($log = $logs->fetch_assoc()): ?>
                        <tr>
                            <td style="width: 25%;"><?php echo date("M d, Y", strtotime($log['log_date'])); ?></td>
                            <td style="width: 25%;"><?php echo htmlspecialchars($log['employee_name']); ?></td>
                            <td><?php echo htmlspecialchars($log['log_description']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                        
                        <?php if($logs->num_rows === 0): ?>
                            <tr><td colspan="3" class="text-center p-4 text-muted">No logs recorded yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>