<?php
require_once 'config.php';
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login_faculty.php");
    exit();
}

$student_id = $_SESSION['user_id'];
$conn = getDBConnection();


$stats_query = "
    SELECT 
        c.course_name,
        COUNT(DISTINCT s.session_id) as total_sessions,
        COUNT(DISTINCT a.session_id) as attended_sessions
    FROM enrollment e
    JOIN courses c ON e.course_id = c.course_id
    LEFT JOIN sessions s ON c.course_id = s.course_id
    LEFT JOIN attendance a ON s.session_id = a.session_id AND a.student_id = ?
    WHERE e.student_id = ?
    GROUP BY c.course_id, c.course_name
";

$stmt = $conn->prepare($stats_query);
$stmt->bind_param("ii", $student_id, $student_id);
$stmt->execute();
$overview = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();


$log_query = "
    SELECT c.course_name, s.session_date, a.status
    FROM attendance a
    JOIN sessions s ON a.session_id = s.session_id
    JOIN courses c ON s.course_id = c.course_id
    WHERE a.student_id = ?
    ORDER BY s.session_date DESC
";
$stmt = $conn->prepare($log_query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$logs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Reports</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <a href="student_dashboard.php">Dashboard</a>
        <a href="logout.php">Logout</a>
    </nav>
    <div class="container">
        <header><h1>Your Attendance Reports</h1></header>

        <section>
            <h2>Overall Attendance</h2>
            <table>
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Sessions Held</th>
                        <th>Attended</th>
                        <th>Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($overview as $row): 
                        $percent = ($row['total_sessions'] > 0) 
                            ? round(($row['attended_sessions'] / $row['total_sessions']) * 100, 2) 
                            : 0;
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['course_name']); ?></td>
                        <td><?php echo $row['total_sessions']; ?></td>
                        <td><?php echo $row['attended_sessions']; ?></td>
                        <td><?php echo $percent; ?>%</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section>
            <h2>Attendance History</h2>
            <table>
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                        <tr><td colspan="3">No attendance records found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($log['course_name']); ?></td>
                            <td><?php echo htmlspecialchars($log['session_date']); ?></td>
                            <td><?php echo htmlspecialchars($log['status']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </div>
</body>
</html>