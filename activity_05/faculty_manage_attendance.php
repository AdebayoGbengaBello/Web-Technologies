<?php
require_once 'config.php';
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') { echo json_encode(['success'=>false]); exit; }
    header("Location: login_faculty.php");
    exit();
}

$conn = getDBConnection();
$session_id = $_GET['session_id'] ?? ($_POST['session_id'] ?? null);

if (!$session_id) {
    die("Session ID not provided.");
}

// --- AJAX HANDLER ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $attendance_data = $_POST['attendance'] ?? [];
    
    // Prepare statement for bulk upsert
    $stmt = $conn->prepare("
        INSERT INTO attendance (session_id, student_id, status) 
        VALUES (?, ?, ?) 
        ON DUPLICATE KEY UPDATE status = VALUES(status)
    ");
    
    try {
        $conn->begin_transaction();
        foreach ($attendance_data as $student_id => $status) {
            $stmt->bind_param("iis", $session_id, $student_id, $status);
            $stmt->execute();
        }
        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Attendance updated successfully.']);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
    
    $stmt->close();
    $conn->close();
    exit();
}

$stmt = $conn->prepare("
    SELECT s.session_date, s.session_code, c.course_name, c.course_id 
    FROM sessions s 
    JOIN courses c ON s.course_id = c.course_id 
    WHERE s.session_id = ?
");
$stmt->bind_param("i", $session_id);
$stmt->execute();
$session_info = $stmt->get_result()->fetch_assoc();
$stmt->close();

$query = "
    SELECT s.student_id, s.student_name, a.status 
    FROM enrollment e
    JOIN students s ON e.student_id = s.student_id
    LEFT JOIN attendance a ON e.student_id = a.student_id AND a.session_id = ?
    WHERE e.course_id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $session_id, $session_info['course_id']);
$stmt->execute();
$students = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Attendance</title>
    <link rel="stylesheet" href="style.css">
    <script src="manage_attendance.js" defer></script>
</head>
<body>
    <nav>
        <a href="faculty_sessions.php">Back to Sessions</a>
        <a href="logout.php">Logout</a>
    </nav>
    <div class="container">
        <header>
            <h1>Manage Attendance</h1>
            <h3><?php echo htmlspecialchars($session_info['course_name']); ?></h3>
            <p>Code: <?php echo htmlspecialchars($session_info['session_code']); ?></p>
        </header>
        
        <p id="feedback" style="display:none; font-weight:bold;"></p>

        <form id="attendanceForm">
            <input type="hidden" name="session_id" value="<?php echo htmlspecialchars($session_id); ?>">
            
            <table>
                <thead><tr><th>Student Name</th><th>Status</th></tr></thead>
                <tbody>
                    <?php foreach ($students as $student): 
                        $status = $student['status'] ?? 'Absent';
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($student['student_name']); ?></td>
                        <td>
                            <select name="attendance[<?php echo $student['student_id']; ?>]">
                                <option value="Present" <?php echo ($status === 'Present') ? 'selected' : ''; ?>>Present</option>
                                <option value="Absent" <?php echo ($status === 'Absent') ? 'selected' : ''; ?>>Absent</option>
                            </select>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button type="submit" class="btn" style="margin-top: 15px;">Save Changes</button>
        </form>
    </div>
</body>
</html>