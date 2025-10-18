<?php
require_once 'config.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

$conn = getDBConnection();
$stmt = $conn->prepare("
    SELECT a.status, a.checkInTime, s.sessionTitle, s.sessionDate, s.sessionTime, s.sessionType, c.courseName
    FROM attendance a
    JOIN sessions s ON a.sessionId = s.sessionId
    JOIN courses c ON s.courseId = c.courseId
    WHERE a.userId = ?
    ORDER BY s.sessionDate DESC, s.sessionTime DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$attendance_records = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Attendance - Student Dashboard</title>
    <link rel="stylesheet" href="attendance.css">
    
</head>
<body>
    <div class="header">
        <h1>My Attendance</h1>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="container">
        <aside>
            <nav>
                <a href="dashboard.php">Home</a>
                <a href="courses.php">Courses</a>
                <a href="sessions.php">Sessions</a>
                <a href="attendance.php" style="background-color: rgba(255, 255, 255, 0.3);">Attendance</a>
                <a href="reports.php">Reports</a>
                <a href="profile.php">Profile</a>
            </nav>
        </aside>

        <main>
            <h1>Attendance History</h1>
            <div class="issue-link">
                <p>Having an issue with your attendance? <a href="attendance_issue.php">Report it here.</a></p>
            </div>

            <?php if (empty($attendance_records)): ?>
                <div class="attendance-card">
                    <h3>No Attendance Records Found</h3>
                    <p>Your attendance has not been recorded for any session yet.</p>
                </div>
            <?php else: ?>
                <?php foreach ($attendance_records as $record): ?>
                    <div class="attendance-card">
                        <h3><?php echo htmlspecialchars($record['courseName']); ?> - <?php echo htmlspecialchars($record['sessionTitle']); ?>
                            <span class="status-badge status-<?php echo $record['status']; ?>">
                                <?php echo htmlspecialchars($record['status']); ?>
                            </span>
                        </h3>
                        <p><strong>Date:</strong> <?php echo date("F j, Y", strtotime($record['sessionDate'])); ?> at <?php echo date("g:i A", strtotime($record['sessionTime'])); ?></p>
                        <p><strong>Session Type:</strong> <?php echo htmlspecialchars($record['sessionType']); ?></p>
                        <?php if($record['checkInTime']): ?>
                            <p><strong>Checked-in:</strong> <?php echo date("g:i:s A", strtotime($record['checkInTime'])); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>