<?php
require_once 'config.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    <script src="dashboard.js"></script>
    
</head>
<body>
    <div class="header">
        <h1>Welcome, <?php echo htmlspecialchars($user_name); ?>!</h1>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="container">
        <aside>
            <nav>
                <a href="dashboard.php" style="background-color: rgba(255, 255, 255, 0.3);">Home</a>
                <a href="courses.php">Courses</a>
                <a href="sessions.php">Sessions</a>
                <a href="attendance.php">Attendance</a>
                <a href="reports.php">Reports</a>
                <a href="profile.php">Profile</a>
            </nav>
        </aside>

        <main>
            <h1>Dashboard</h1>

            <div class="card-grid">
                <div class="card">
                    <h2>Courses</h2>
                    <p id="courseCount">Loading...</p>
                </div>
                <div class="card">
                    <h2>Avg. Progress</h2>
                    <p id="averageProgress">Loading...</p>
                </div>
                <div class="card">
                    <h2>Total Hours</h2>
                    <p id="totalHours">Loading...</p>
                </div>
            </div>

            <section id="sessions">
                <h2>Upcoming Sessions</h2>
                <div id="sessionList">
                    <p>Loading sessions...</p>
                </div>
            </section>
        </main>
    </div>
</body>
</html>