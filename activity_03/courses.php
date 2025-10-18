<?php
require_once 'config.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Fetch u's courses from db
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
    <style>
        /* Same styles as home.php */
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; display: flex; justify-content: space-between; align-items: center; }
        .logout-btn { background: white; color: #667eea; padding: 10px 20px; border: none; border-radius: 5px; text-decoration: none; }
        .container { display: flex; }
        aside { width: 200px; background-color: #5050d8; min-height: 100vh; padding: 20px; }
        nav { display: flex; flex-direction: column; gap: 10px; }
        nav a { color: white; text-decoration: none; padding: 10px; border-radius: 5px; transition: background-color 0.3s; }
        nav a:hover { background-color: rgba(255, 255, 255, 0.2); }
        main { flex: 1; padding: 30px; }
        .course-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; border-left: 4px solid #5050d8; }
        .progress-bar { width: 100%; height: 10px; background-color: #e0e0e0; border-radius: 5px; margin: 10px 0; overflow: hidden; }
        .progress-fill { height: 100%; background-color: #5050d8; transition: width 0.5s; }
        .status-badge { display: inline-block; padding: 4px 8px; border-radius: 12px; font-size: 12px; margin-left: 10px; }
        .status-enrolled { background-color: #e3f2fd; color: #1976d2; }
        .status-completed { background-color: #e8f5e8; color: #2e7d32; }
    </style>
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
                            <span class="status-badge status-<?php echo $course['status']; ?>">
                                <?php echo ucfirst($course['status']); ?>
                            </span>
                        </h3>
                        <p><strong>Course Code:</strong> <?php echo htmlspecialchars($course['courseCode']); ?></p>
                        <p><strong>Instructor:</strong> <?php echo htmlspecialchars($course['instructorName']); ?></p>
                        <p><strong>Total Hours:</strong> <?php echo $course['totalHours']; ?> hours</p>
                        <?php if ($course['description']): ?>
                            <p><?php echo htmlspecialchars($course['description']); ?></p>
                        <?php endif; ?>
                        
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?php echo $course['progress_percentage']; ?>%"></div>
                        </div>
                        <p><strong>Progress:</strong> <?php echo $course['progress_percentage']; ?>%</p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>