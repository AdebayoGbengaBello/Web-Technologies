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
        if (isset($_GET['enrollmentId'])) {
            $stmt = $conn->prepare("SELECT e.*, u.firstName, u.lastName, c.courseName 
                                   FROM enrollment e 
                                   JOIN users u ON e.userId = u.userId 
                                   JOIN courses c ON e.courseId = c.courseId 
                                   WHERE e.enrollmentId = ?");
            $stmt->bind_param("i", $_GET['enrollmentId']);
        } elseif (isset($_GET['userId'])) {
            $stmt = $conn->prepare("SELECT e.*, c.courseName, c.courseCode, c.instructorName 
                                   FROM enrollment e 
                                   JOIN courses c ON e.courseId = c.courseId 
                                   WHERE e.userId = ?");
            $stmt->bind_param("i", $_GET['userId']);
        } elseif (isset($_GET['courseId'])) {
            $stmt = $conn->prepare("SELECT e.*, u.firstName, u.lastName, u.email 
                                   FROM enrollment e 
                                   JOIN users u ON e.userId = u.userId 
                                   WHERE e.courseId = ?");
            $stmt->bind_param("i", $_GET['courseId']);
        } else {
            $stmt = $conn->prepare("SELECT e.*, u.firstName, u.lastName, c.courseName 
                                   FROM enrollment e 
                                   JOIN users u ON e.userId = u.userId 
                                   JOIN courses c ON e.courseId = c.courseId 
                                   ORDER BY e.enrollment_date DESC");
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
        if (!isset($data['userId'], $data['courseId'])) {
            http_response_code(400);
            echo json_encode(["error" => "Missing required parameters: userId and courseId"]);
            exit;
        }
        
        $progress_percentage = $data['progress_percentage'] ?? 0;
        $status = $data['status'] ?? 'Enrolled';
        
        $stmt = $conn->prepare("INSERT INTO enrollment (userId, courseId, progress_percentage, status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $data['userId'], $data['courseId'], $progress_percentage, $status);
        
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "enrollmentId" => $conn->insert_id]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Enrollment failed: " . $stmt->error]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['enrollmentId'])) {
            http_response_code(400);
            echo json_encode(["error" => "Missing enrollment ID"]);
            exit;
        }
        
        $updates = [];
        $params = [];
        $types = "";
        
        if (isset($data['progress_percentage'])) {
            $updates[] = "progress_percentage = ?";
            $params[] = $data['progress_percentage'];
            $types .= "i";
        }
        if (isset($data['status'])) {
            $updates[] = "status = ?";
            $params[] = $data['status'];
            $types .= "s";
        }
        
        if (empty($updates)) {
            http_response_code(400);
            echo json_encode(["error" => "No fields to update"]);
            exit;
        }
        
        $params[] = $data['enrollmentId'];
        $types .= "i";
        
        $sql = "UPDATE enrollment SET " . implode(", ", $updates) . " WHERE enrollmentId = ?";
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
        if (!isset($data['enrollmentId'])) {
            http_response_code(400);
            echo json_encode(["error" => "Missing enrollment ID"]);
            exit;
        }
        $stmt = $conn->prepare("DELETE FROM enrollment WHERE enrollmentId = ?");
        $stmt->bind_param("i", $data['enrollmentId']);
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