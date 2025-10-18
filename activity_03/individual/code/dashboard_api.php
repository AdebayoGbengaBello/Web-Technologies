<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(["error" => "Not authenticated"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$conn = getDBConnection();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $response = [];
        
        $stmt = $conn->prepare("
            SELECT COUNT(*) as course_count, 
                   AVG(e.progress_percentage) as avg_progress,
                   SUM(c.totalHours) as total_hours
            FROM enrollment e 
            JOIN courses c ON e.courseId = c.courseId 
            WHERE e.userId = ? AND e.status = 'Enrolled'
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $summary = $stmt->get_result()->fetch_assoc();
        
        $response['summary'] = [
            'courseCount' => $summary['course_count'] ?? 0,
            'averageProgress' => round($summary['avg_progress'] ?? 0),
            'totalHours' => $summary['total_hours'] ?? 0
        ];
        
        $stmt = $conn->prepare("
            SELECT s.sessionId, s.sessionTitle, s.sessionDate, s.sessionTime, s.sessionType, s.notes, c.courseName 
            FROM sessions s 
            JOIN courses c ON s.courseId = c.courseId 
            JOIN enrollment e ON c.courseId = e.courseId 
            WHERE e.userId = ? AND s.sessionDate >= CURDATE() 
            ORDER BY s.sessionDate, s.sessionTime 
            LIMIT 5
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $response['sessions'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        echo json_encode($response);
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
        break;
}

$conn->close();
?>