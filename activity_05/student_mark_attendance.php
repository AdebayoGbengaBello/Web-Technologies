<?php
require_once 'config.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit();
    }
    header("Location: login_faculty.php");
    exit();
}

$user_id = $_SESSION['user_id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $code = trim($_POST['session_code'] ?? '');
    
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("
        SELECT s.session_id, s.course_id, c.course_name 
        FROM sessions s
        JOIN enrollment e ON s.course_id = e.course_id
        JOIN courses c ON s.course_id = c.course_id
        WHERE s.session_code = ? AND e.student_id = ?
    ");
    $stmt->bind_param("ii", $code, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $session = $result->fetch_assoc();
        $session_id = $session['session_id'];
        
        $insert_stmt = $conn->prepare("
            INSERT IGNORE INTO attendance (session_id, student_id, status) 
            VALUES (?, ?, 'Present')
        ");
        $insert_stmt->bind_param("ii", $session_id, $user_id);
        
        if ($insert_stmt->execute()) {
            if ($insert_stmt->affected_rows > 0) {
                echo json_encode(['success' => true, 'message' => "Success! Marked present for " . $session['course_name']]);
            } else {
                echo json_encode(['success' => false, 'message' => "You have already marked attendance for this session."]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => "Database error."]);
        }
        $insert_stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => "Invalid Code or not enrolled."]);
    }
    $stmt->close();
    $conn->close();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mark Attendance</title>
    <link rel="stylesheet" href="style.css">
    <script src="mark_attendance.js" defer></script>
</head>
<body>
    <nav>
        <a href="student_dashboard.php">Dashboard</a>
        <a href="logout.php">Logout</a>
    </nav>
    <div class="container">
        <header><h1>Mark Attendance</h1></header>
        
        <p id="feedback" style="display:none; font-weight:bold;"></p>

        <form id="markAttendanceForm">
            <div class="form-group">
                <label for="session_code">Enter Session Code:</label>
                <input type="number" name="session_code" id="session_code" required placeholder="e.g. 12345">
            </div>
            <button type="submit" class="btn">Submit Code</button>
        </form>
    </div>
</body>
</html>