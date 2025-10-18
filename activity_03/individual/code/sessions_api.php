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
        if (isset($_GET['sessionId'])) {
            $stmt = $conn->prepare("SELECT s.*, c.courseName, c.courseCode
                                   FROM sessions s
                                   JOIN courses c ON s.courseId = c.courseId
                                   WHERE s.sessionId = ?");
            $stmt->bind_param("i", $_GET['sessionId']);
        } else {
             $stmt = $conn->prepare("SELECT s.sessionId, s.sessionTitle, s.sessionDate, s.sessionTime, s.sessionType, s.notes, c.courseName
                                   FROM sessions s
                                   JOIN courses c ON s.courseId = c.courseId
                                   ORDER BY s.sessionDate, s.sessionTime");
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        echo json_encode($rows);
        break;
    default:
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
        break;
}

$conn->close();
?>