<?php
require_once 'config.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Fetch user's courses from db
$conn = getDBConnection();
$stmt = $conn->prepare("
    SELECT c.courseId, c.courseName, c.courseCode, c.description, c.instructorName, 
           c.totalHours, e.progress_percentage, e.status 
    FROM enrollment e 
    JOIN courses c ON e.courseId = c.courseId 
    WHERE e.userId = ? 
    ORDER BY e.enrollment_date DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$courses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Courses - Student Dashboard</title>
</head>
<body>
    <div class="header">
        <h1>My Courses</h1>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
    
    <div class="container">
        <aside>
            <nav>
                <a href="dashboard.php">Home</a>
                <a href="courses.php" style="background-color: rgba(255, 255, 255, 0.3);">Courses</a>
                <a href="sessions.php">Sessions</a>
                <a href="attendance.php">Attendance</a>
                <a href="reports.php">Reports</a>
                <a href="profile.php">Profile</a>
            </nav>
        </aside>

        <main>
            <h1>My Enrolled Courses</h1>
            
            <?php if (empty($courses)): ?>
                <div class="course-card">
                    <h3>No Courses Enrolled</h3>
                    <p>You haven't enrolled in any courses yet.</p>
                </div>
            <?php else: ?>
                <?php foreach ($courses as $course): ?>
                    <div class="course-card">
                        <h3><?php echo htmlspecialchars($course['courseName']); ?> 
                            <span class="status-badge status-<?php echo htmlspecialchars($course['status']); ?>">
                                <?php echo ucfirst(htmlspecialchars($course['status'])); ?>
                            </span>
                        </h3>
                        <p><strong>Course Code:</strong> <?php echo htmlspecialchars($course['courseCode']); ?></p>
                        <p><strong>Instructor:</strong> <?php echo htmlspecialchars($course['instructorName']); ?></p>
                        <p><strong>Total Hours:</strong> <?php echo htmlspecialchars($course['totalHours']); ?> hours</p>
                        <?php if ($course['description']): ?>
                            <p><?php echo htmlspecialchars($course['description']); ?></p>
                        <?php endif; ?>
                        
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?php echo htmlspecialchars($course['progress_percentage']); ?>%"></div>
                        </div>
                        <p><strong>Progress:</strong> <?php echo htmlspecialchars($course['progress_percentage']); ?>%</p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>