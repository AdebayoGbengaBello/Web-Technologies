<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Manager') {
    header("Location: login.php");
    exit();
}

$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['employee_name']);
    $email = trim($_POST['employee_email']);
    $phone = trim($_POST['employee_phone']);
    $password = $_POST['employee_password'];
    $manager_id = $_SESSION['user_id'];

    if (empty($name)) $errors['name'] = "Name is required.";
    if (empty($email)) $errors['email'] = "Email is required.";
    if (empty($password)) $errors['password'] = "Password is required.";

    if (empty($errors)) {
        $conn = getDBConnection();
        $check = $conn->prepare("SELECT employee_id FROM employees WHERE employee_email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $errors['email'] = "Email already exists.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO employees (employee_name, employee_email, employee_phone, employee_password, manager_id) VALUES (?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", $name, $email, $phone, $hashed, $manager_id);
            
            if ($stmt->execute()) {
                header("Location: manage_employees.php");
                exit();
            } else {
                $errors['db'] = "Database Error: " . $conn->error;
            }
        }
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Employee - Rental Flow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="add_employee.js" defer></script>
</head>
<body class="bg-light">
<div class="container mt-5" style="max-width: 600px;">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4>Add New Employee</h4>
        </div>
        <div class="card-body">
            
            <?php if(isset($errors['db'])): ?>
                <div class="alert alert-danger"><?php echo $errors['db']; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="employee_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="employee_email" class="form-control" required>
                    <div class="text-danger small"><?php echo $errors['email'] ?? ''; ?></div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="employee_phone" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Default Password</label>
                    <input type="password" name="employee_password" class="form-control" required>
                    <div class="form-text">The employee can change this later.</div>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success">Create Employee</button>
                    <a href="manage_employees.php" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>