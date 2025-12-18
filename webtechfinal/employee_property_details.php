<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Employee' || !isset($_GET['id'])) {
    header("Location: login.php");
    exit();
}

$emp_id = $_SESSION['user_id'];
$p_id = $_GET['id'];
$conn = getDBConnection();

$check_sql = "SELECT p.*, t.* FROM properties p 
              LEFT JOIN tenants t ON p.tenant_id = t.tenant_id 
              WHERE p.property_id = ? AND p.employee_id = ?";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param("ii", $p_id, $emp_id);
$stmt->execute();
$property = $stmt->get_result()->fetch_assoc();

if (!$property) {
    die("Access Denied: You are not assigned to this property.");
}

$log_sql = "SELECT * FROM logs WHERE property_id = ? ORDER BY log_date DESC";
$log_stmt = $conn->prepare($log_sql);
$log_stmt->bind_param("i", $p_id);
$log_stmt->execute();
$logs = $log_stmt->get_result();

$pay_sql = "SELECT * FROM payments WHERE property_id = ? ORDER BY payment_date DESC LIMIT 5";
$pay_stmt = $conn->prepare($pay_sql);
$pay_stmt->bind_param("i", $p_id);
$pay_stmt->execute();
$payments = $pay_stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage <?php echo htmlspecialchars($property['property_name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4 mb-5">
    <a href="employee_dashboard.php" class="btn btn-outline-secondary mb-3">&larr; Back to Dashboard</a>

    <div class="card shadow-sm mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0"><?php echo htmlspecialchars($property['property_name']); ?></h2>
                <span class="text-muted">Rent: $<?php echo number_format($property['property_rent'], 2); ?></span>
            </div>
            <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#paymentModal">
                + Record Payment
            </button>
            <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#logModal">
                + New Log Entry
            </button>
        </div>
    </div>

    <div class="card mt-4 shadow-sm">
        <div class="card-header bg-white text-success fw-bold">Recent Payments</div>
        <ul class="list-group list-group-flush">
            <?php while($pay = $payments->fetch_assoc()): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>
                        <strong>$<?php echo number_format($pay['payment_amount'], 2); ?></strong> 
                        <span class="text-muted ms-2">- <?php echo htmlspecialchars($pay['payment_description']); ?></span>
                    </span>
                    <small class="text-muted"><?php echo date("M d, Y", strtotime($pay['payment_date'])); ?></small>
                </li>
            <?php endwhile; ?>
            <?php if($payments->num_rows === 0): ?>
                <li class="list-group-item text-center text-muted py-4">No Payments found. Start creating one!</li>
            <?php endif; ?>
        </ul>
    </div>

    <div class="row">
        
        <div class="col-md-5">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-dark text-white">
                    Tenant Information
                </div>
                <div class="card-body">
                    <form action="process_tenant.php" method="POST">
                        <input type="hidden" name="property_id" value="<?php echo $p_id; ?>">
                        <input type="hidden" name="tenant_id" value="<?php echo $property['tenant_id'] ?? ''; ?>">

                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="tenant_name" class="form-control" 
                                   value="<?php echo htmlspecialchars($property['tenant_name'] ?? ''); ?>" placeholder="Vacant">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="tenant_email" class="form-control" 
                                   value="<?php echo htmlspecialchars($property['tenant_email'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="tenant_phone" class="form-control" 
                                   value="<?php echo htmlspecialchars($property['tenant_phone'] ?? ''); ?>">
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Update Tenant Details</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <strong>Activity History</strong>
                </div>
                <ul class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
                    <?php while($log = $logs->fetch_assoc()): ?>
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <small class="text-muted"><?php echo date("M d, h:i A", strtotime($log['log_date'])); ?></small>
                                <span class="badge bg-secondary">Log #<?php echo $log['log_id']; ?></span>
                            </div>
                            <p class="mb-1 mt-1"><?php echo htmlspecialchars($log['log_description']); ?></p>
                        </li>
                    <?php endwhile; ?>
                    
                    <?php if($logs->num_rows === 0): ?>
                        <li class="list-group-item text-center text-muted py-4">No logs found. Start creating one!</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="logModal" tabindex="-1">
  <div class="modal-dialog">
    <form action="process_log.php" method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Create New Log</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="property_id" value="<?php echo $p_id; ?>">
        <input type="hidden" name="tenant_id" value="<?php echo $property['tenant_id'] ?? ''; ?>">
        
        <div class="mb-3">
            <label class="form-label">Event Description</label>
            <textarea name="log_description" class="form-control" rows="4" placeholder="e.g. Tenant reported leaking faucet..." required></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-success">Save Log</button>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="paymentModal" tabindex="-1">
  <div class="modal-dialog">
    <form action="process_payment.php" method="POST" class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title">Record Rent Payment</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="property_id" value="<?php echo $p_id; ?>">
        <input type="hidden" name="tenant_id" value="<?php echo $property['tenant_id'] ?? ''; ?>">
        
        <div class="mb-3">
            <label class="form-label">Payment Amount ($)</label>
            <input type="number" step="0.01" name="payment_amount" class="form-control" 
                   value="<?php echo $property['property_rent']; ?>" required>
            <div class="form-text">Default is set to monthly rent.</div>
        </div>

        <div class="mb-3">
            <label class="form-label">Description / Note</label>
            <input type="text" name="payment_description" class="form-control" 
                   placeholder="e.g. January Rent + Late Fee" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-success">Process Payment</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>