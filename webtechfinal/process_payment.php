<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['role'] === 'Employee') {
    $conn = getDBConnection();
    
    $prop_id = $_POST['property_id'];
    $tenant_id = !empty($_POST['tenant_id']) ? $_POST['tenant_id'] : NULL;
    $emp_id = $_SESSION['user_id'];
    $amount = trim($_POST['payment_amount']);
    $desc = trim($_POST['payment_description']);

    if (empty($amount) || !is_numeric($amount) || $amount <= 0) {
        die("Invalid Amount");
    }

    $sql = "INSERT INTO payments (payment_description, payment_amount, property_id, employee_id, tenant_id) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdiii", $desc, $amount, $prop_id, $emp_id, $tenant_id);
    
    if ($stmt->execute()) {
        $update_sql = "UPDATE properties SET property_revenue = property_revenue + ? WHERE property_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("di", $amount, $prop_id);
        $update_stmt->execute();
        header("Location: employee_property_details.php?id=$prop_id&msg=payment_saved");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>