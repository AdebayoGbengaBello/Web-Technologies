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

// Check if user is logged in
session_start();
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
        // Get dashboard summary data
        $response = [];
        
        // Get user's enrolled courses count
        $stmt = $conn->prepare("
            SELECT COUNT(*) as course_count, 
                   AVG(progress_percentage) as avg_progress,
                   SUM(c.totalHours) as total_hours
            FROM enrollment e 
            JOIN courses c ON e.courseId = c.courseId 
            WHERE e.userId = ? AND e.status = 'Enrolled'
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $summary = $result->fetch_assoc();
        
        $response['summary'] = [
            'courseCount' => $summary['course_count'] ?? 0,
            'averageProgress' => round($summary['avg_progress'] ?? 0),
            'totalHours' => $summary['total_hours'] ?? 0
        ];
        
        // Get enrolled courses with progress
        $stmt = $conn->prepare("
            SELECT c.courseId, c.courseName, c.instructorName, e.progress_percentage 
            FROM enrollment e 
            JOIN courses c ON e.courseId = c.courseId 
            WHERE e.userId = ? AND e.status = 'Enrolled'
            ORDER BY e.enrollment_date DESC
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $courses = [];
        while ($row = $result->fetch_assoc()) {
            $courses[] = $row;
        }
        $response['courses'] = $courses;
        
        // Get upcoming sessions
        $stmt = $conn->prepare("
            SELECT s.sessionId, s.sessionTitle, s.sessionDate, s.sessionTime, c.courseName 
            FROM sessions s 
            JOIN courses c ON s.courseId = c.courseId 
            JOIN enrollment e ON c.courseId = e.courseId 
            WHERE e.userId = ? AND s.sessionDate >= CURDATE() 
            ORDER BY s.sessionDate, s.sessionTime 
            LIMIT 5
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $sessions = [];
        while ($row = $result->fetch_assoc()) {
            $sessions[] = $row;
        }
        $response['sessions'] = $sessions;
        
        echo json_encode($response);
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
        break;
}

$conn->close();
?>