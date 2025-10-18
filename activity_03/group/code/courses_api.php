<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dash_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['userId'])) {
            $stmt = $conn->prepare("SELECT userId, firstName, lastName, email, date_registered, profile_picture FROM users WHERE userId = ?");
            $stmt->bind_param("i", $_GET['userId']);
        } else {
            $stmt = $conn->prepare("SELECT userId, firstName, lastName, email, date_registered, profile_picture FROM users ORDER BY date_registered DESC");
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        echo json_encode($rows);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['firstName'], $data['lastName'], $data['email'], $data['password'])) {
            http_response_code(400);
            echo json_encode(["error" => "Missing required parameters"]);
            exit;
        }
        
        $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
        $profile_picture = $data['profile_picture'] ?? null;
        
        $stmt = $conn->prepare("INSERT INTO users (firstName, lastName, email, password_hash, profile_picture) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $data['firstName'], $data['lastName'], $data['email'], $password_hash, $profile_picture);
        
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "userId" => $conn->insert_id]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "User creation failed: " . $stmt->error]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['userId'])) {
            http_response_code(400);
            echo json_encode(["error" => "Missing user ID"]);
            exit;
        }
        
        $updates = [];
        $params = [];
        $types = "";
        
        if (isset($data['firstName'])) {
            $updates[] = "firstName = ?";
            $params[] = $data['firstName'];
            $types .= "s";
        }
        if (isset($data['lastName'])) {
            $updates[] = "lastName = ?";
            $params[] = $data['lastName'];
            $types .= "s";
        }
        if (isset($data['email'])) {
            $updates[] = "email = ?";
            $params[] = $data['email'];
            $types .= "s";
        }
        if (isset($data['password'])) {
            $updates[] = "password_hash = ?";
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
            $types .= "s";
        }
        if (isset($data['profile_picture'])) {
            $updates[] = "profile_picture = ?";
            $params[] = $data['profile_picture'];
            $types .= "s";
        }
        
        if (empty($updates)) {
            http_response_code(400);
            echo json_encode(["error" => "No fields to update"]);
            exit;
        }
        
        $params[] = $data['userId'];
        $types .= "i";
        
        $sql = "UPDATE users SET " . implode(", ", $updates) . " WHERE userId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        
        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Update failed: " . $stmt->error]);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['userId'])) {
            http_response_code(400);
            echo json_encode(["error" => "Missing user ID"]);
            exit;
        }
        $stmt = $conn->prepare("DELETE FROM users WHERE userId = ?");
        $stmt->bind_param("i", $data['userId']);
        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Delete failed: " . $stmt->error]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
        break;
}

$conn->close();
?>