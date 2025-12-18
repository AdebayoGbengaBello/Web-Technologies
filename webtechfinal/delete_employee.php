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

$manager_id = $_SESSION['user_id'];
$emp_id = $_GET['id'];
$conn = getDBConnection();

$check_stmt = $conn->prepare("SELECT employee_id FROM employees WHERE employee_id = ? AND manager_id = ?");
$check_stmt->bind_param("ii", $emp_id, $manager_id);
$check_stmt->execute();
if ($check_stmt->get_result()->num_rows === 0) {
    die("Access Denied: You do not manage this employee.");
}

$prop_sql = "UPDATE properties SET employee_id = NULL WHERE employee_id = ?";
$prop_stmt = $conn->prepare($prop_sql);
$prop_stmt->bind_param("i", $emp_id);
$prop_stmt->execute();

$deactivate_sql = "UPDATE employees SET manager_id = NULL, employee_password = NULL WHERE employee_id = ?";
$deactivate_stmt = $conn->prepare($deactivate_sql);
$deactivate_stmt->bind_param("i", $emp_id);

if ($deactivate_stmt->execute()) {
    header("Location: manage_employees.php?msg=deactivated");
} else {
    echo "Error deactivating employee: " . $conn->error;
}

$conn->close();
?>