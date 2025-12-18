<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Manager' || !isset($_GET['id'])) {
    header("Location: login.php");
    exit();
}

$conn = getDBConnection();
$sql = "DELETE FROM properties WHERE property_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_GET['id']);

if ($stmt->execute()) {
    header("Location: manage_properties.php?msg=deleted");
} else {
    echo "Error deleting record: " . $conn->error;
}
?>