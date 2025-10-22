<?php
header("Content-Type: application/json");

$servername = "localhost";
$username = "root";
$password = ""; // Default for XAMPP
$dbname = "demo.db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit();
}

$sql = "SELECT id, name, email, course FROM students";
$result = $conn->query($sql);

$students = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
}

$conn->close();
echo json_encode($students);
?>
