<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Manager') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: manage_employees.php");
    exit();
}

$conn = getDBConnection();
$manager_id = $_SESSION['user_id'];
$emp_id = $_GET['id'];
$errors = [];
$success = "";

$stmt = $conn->prepare("SELECT * FROM employees WHERE employee_id = ? AND manager_id = ?");
$stmt->bind_param("ii", $emp_id, $manager_id);
$stmt->execute();
$employee = $stmt->get_result()->fetch_assoc();

if (!$employee) {
    die("Employee not found or access denied.");
}

$prop_sql = "SELECT p.property_id, p.property_name, p.employee_id, e.employee_name 
             FROM properties p 
             LEFT JOIN employees e ON p.employee_id = e.employee_id 
             WHERE p.manager_id = ?";
$prop_stmt = $conn->prepare($prop_sql);
$prop_stmt->bind_param("i", $manager_id);
$prop_stmt->execute();
$properties_result = $prop_stmt->get_result();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $name = trim($_POST['employee_name']);
    $email = trim($_POST['employee_email']);
    $phone = trim($_POST['employee_phone']);
    $assignments = $_POST['assigned_properties'] ?? []; 

    if (empty($name)) $errors['name'] = "Name is required.";
    if (empty($email)) $errors['email'] = "Email is required.";


    $check = $conn->prepare("SELECT employee_id FROM employees WHERE employee_email = ? AND employee_id != ?");
    $check->bind_param("si", $email, $emp_id);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        $errors['email'] = "Email is already taken by another employee.";
    }

    if (empty($errors)) {
        $update_sql = "UPDATE employees SET employee_name=?, employee_email=?, employee_phone=? WHERE employee_id=?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sssi", $name, $email, $phone, $emp_id);
        $update_stmt->execute();
        
        $clear_sql = "UPDATE properties SET employee_id = NULL WHERE employee_id = ?";
        $clear_stmt = $conn->prepare($clear_sql);
        $clear_stmt->bind_param("i", $emp_id);
        $clear_stmt->execute();

        if (!empty($assignments)) {
            $types = str_repeat('i', count($assignments));
            $placeholders = implode(',', array_fill(0, count($assignments), '?'));

            $assign_sql = "UPDATE properties SET employee_id = ? WHERE property_id IN ($placeholders)";
            $assign_stmt = $conn->prepare($assign_sql);
            
            $params = array_merge([$emp_id], $assignments);
            $assign_stmt->bind_param("i" . $types, ...$params);
            $assign_stmt->execute();
        }

        $success = "Employee updated successfully!";
        header("Refresh:0");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Employee - Rental Flow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            
            <a href="manage_employees.php" class="btn btn-outline-secondary mb-3">&larr; Back to Employees</a>

            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Edit Employee</h4>
                    <a href="delete_employee.php?id=<?php echo $emp_id; ?>" 
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('Are you sure? This will remove the employee and unassign all their properties.');">
                       Delete Employee
                    </a>
                </div>
                <div class="card-body p-4">

                    <?php if($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>

                    <form method="POST" id="editEmployeeForm" novalidate>
                        
                        <h5 class="mb-3 text-muted border-bottom pb-2">Personal Information</h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" 
                                       name="employee_name" id="empName"
                                       value="<?php echo htmlspecialchars($employee['employee_name']); ?>" required>
                                <div class="invalid-feedback"><?php echo $errors['name'] ?? ''; ?></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="text" class="form-control" name="employee_phone" 
                                       value="<?php echo htmlspecialchars($employee['employee_phone']); ?>">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                                   name="employee_email" id="empEmail"
                                   value="<?php echo htmlspecialchars($employee['employee_email']); ?>" required>
                            <div class="invalid-feedback"><?php echo $errors['email'] ?? ''; ?></div>
                        </div>

                        <h5 class="mb-3 text-muted border-bottom pb-2">Assign Properties</h5>
                        <div class="alert alert-info small">
                            Check the properties you want this employee to manage. 
                            <br><strong>Note:</strong> Selecting a property already assigned to someone else will reassign it to this employee.
                        </div>

                        <div class="list-group mb-4 shadow-sm" style="max-height: 300px; overflow-y: auto;">
                            <?php 
                            $properties_result->data_seek(0); 
                            
                            if ($properties_result->num_rows > 0): 
                                while($prop = $properties_result->fetch_assoc()): 
                                    $is_mine = ($prop['employee_id'] == $emp_id);
                                    $is_taken = ($prop['employee_id'] && !$is_mine);
                            ?>
                                <label class="list-group-item d-flex gap-3 align-items-center <?php echo $is_mine ? 'bg-primary-subtle' : ''; ?>">
                                    <input class="form-check-input flex-shrink-0" type="checkbox" 
                                           name="assigned_properties[]" 
                                           value="<?php echo $prop['property_id']; ?>"
                                           <?php echo $is_mine ? 'checked' : ''; ?>>
                                    
                                    <span class="d-flex w-100 justify-content-between">
                                        <span><?php echo htmlspecialchars($prop['property_name']); ?></span>
                                        
                                        <small class="text-muted">
                                            <?php 
                                            if ($is_mine) echo '<span class="text-primary fw-bold">Current Agent</span>';
                                            elseif ($is_taken) echo 'Assigned to: ' . htmlspecialchars($prop['employee_name']); 
                                            else echo 'Unassigned';
                                            ?>
                                        </small>
                                    </span>
                                </label>
                            <?php endwhile; ?>
                            <?php else: ?>
                                <div class="list-group-item text-center text-muted">You have no properties to assign.</div>
                            <?php endif; ?>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">Save Changes</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


</body>
</html>