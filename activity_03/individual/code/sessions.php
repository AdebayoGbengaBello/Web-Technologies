<?php
require_once 'config.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];


$conn = getDBConnection();
$stmt = $conn->prepare("
    SELECT s.sessionTitle, s.sessionDate, s.sessionTime, s.location, s.sessionType, s.notes, c.courseName
    FROM sessions s
    JOIN courses c ON s.courseId = c.courseId
    JOIN enrollment e ON c.courseId = e.courseId
    WHERE e.userId = ? AND s.sessionDate >= CURDATE()
    ORDER BY s.sessionDate, s.sessionTime
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$sessions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Sessions - Student Dashboard</title>
    <link rel="stylesheet" href="sessions.css">
</head>
<body>
    <div class="header">
        <h1>My Sessions</h1>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="container">
        <aside>
            <nav>
                <a href="dashboard.php">Home</a>
                <a href="courses.php">Courses</a>
                <a href="sessions.php" style="background-color: rgba(255, 255, 255, 0.3);">Sessions</a>
                <a href="attendance.php">Attendance</a>
                <a href="reports.php">Reports</a>
                <a href="profile.php">Profile</a>
            </nav>
        </aside>

        <main>
            <h1>Upcoming Sessions</h1>
            
            <?php if (empty($sessions)): ?>
                <div class="session-card">
                    <h3>No Upcoming Sessions</h3>
                    <p>You have no scheduled sessions at this time.</p>
                </div>
            <?php else: ?>
                <?php foreach ($sessions as $session): ?>
                    <div class="session-card">
                        <h3>
                            <?php echo htmlspecialchars($session['sessionTitle']); ?>
                            <span class="session-type-badge type-<?php echo $session['sessionType']; ?>">
                                <?php echo htmlspecialchars($session['sessionType']); ?>
                            </span>
                        </h3>
                        <p><strong>Course:</strong> <?php echo htmlspecialchars($session['courseName']); ?></p>
                        <p><strong>Date:</strong> <?php echo date("F j, Y", strtotime($session['sessionDate'])); ?> at <?php echo date("g:i A", strtotime($session['sessionTime'])); ?></p>
                        <?php if ($session['location']): ?>
                            <p><strong>Location:</strong> <?php echo htmlspecialchars($session['location']); ?></p>
                        <?php endif; ?>
                        <?php if ($session['notes']): ?>
                            <div class="session-notes">
                                <strong>Note:</strong> <?php echo htmlspecialchars($session['notes']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>