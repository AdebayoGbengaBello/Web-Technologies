<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['role'] === 'Employee') {
    $conn = getDBConnection();
    
    $prop_id = $_POST['property_id'];
    $tenant_id = $_POST['tenant_id'];
    
    $name = trim($_POST['tenant_name']);
    $email = trim($_POST['tenant_email']);
    $phone = trim($_POST['tenant_phone']);

    if (empty($name)) {
        die("Tenant name is required.");
    }

    if (!empty($tenant_id)) {
        $sql = "UPDATE tenants SET tenant_name=?, tenant_email=?, tenant_phone=? WHERE tenant_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $name, $email, $phone, $tenant_id);
        $stmt->execute();
    } else {
        $sql = "INSERT INTO tenants (tenant_name, tenant_email, tenant_phone) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $name, $email, $phone);
        $stmt->execute();
        
        $new_id = $conn->insert_id;
        
        $link_sql = "UPDATE properties SET tenant_id = ? WHERE property_id = ?";
        $link_stmt = $conn->prepare($link_sql);
        $link_stmt->bind_param("ii", $new_id, $prop_id);
        $link_stmt->execute();
    }

    header("Location: employee_property_details.php?id=$prop_id");
    exit();
}
?>