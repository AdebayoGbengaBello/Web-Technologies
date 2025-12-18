<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['role'] === 'Employee') {
    $conn = getDBConnection();
    
    $prop_id = $_POST['property_id'];
    $tenant_id = !empty($_POST['tenant_id']) ? $_POST['tenant_id'] : NULL;
    $emp_id = $_SESSION['user_id'];
    $desc = trim($_POST['log_description']);

    if (!empty($desc)) {
        $sql = "INSERT INTO logs (log_description, property_id, employee_id, tenant_id) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("siii", $desc, $prop_id, $emp_id, $tenant_id);
        $stmt->execute();
    }

    header("Location: employee_property_details.php?id=$prop_id");
    exit();
}
?>