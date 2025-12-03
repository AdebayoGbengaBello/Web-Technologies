<?php
require_once 'config.php';
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login_faculty.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

$conn = getDBConnection();

$stmt = $conn->prepare("
    SELECT s.session_id, s.course_id, s.course_name, s.session_date, s.session_code
    FROM sessions s
    JOIN courses c ON s.course_id = c.course_id
    WHERE c.faculty_id = ?
    ORDER BY s.session_date DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$sessions = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();


$stmt = $conn->prepare("SELECT course_id, course_name FROM courses WHERE faculty_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$courses = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = $_POST['course_id'];
    $session_date = $_POST['session_date'];
    $session_code = $_POST['session_code'];
    
    
    $stmt = $conn->prepare("SELECT course_name FROM courses WHERE course_id = ?");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $c_row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $insert_stmt = $conn->prepare("
        INSERT INTO sessions (course_id, course_name, session_date, session_code)
        VALUES (?, ?, ?, ?)
    ");
    $insert_stmt->bind_param("issi", $course_id, $c_row['course_name'], $session_date, $session_code);
    $insert_stmt->execute();
    $insert_stmt->close();

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Faculty Sessions</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <a href="faculty_dashboard.php">Dashboard</a>
        <a href="logout.php">Logout</a>
    </nav>
    <div class="container">
        <header><h1>Faculty Sessions</h1></header>

        <section class="sessionSection">
            <h2>Your Sessions</h2>
            <div id="sessionsContainer">
                <?php if (count($sessions) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Course Name</th>
                                <th>Session Date</th>
                                <th>Session Code</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sessions as $session): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($session['course_name']); ?></td>
                                    <td><?php echo htmlspecialchars($session['session_date']); ?></td>
                                    <td><?php echo htmlspecialchars($session['session_code']); ?></td>
                                    <td>
                                        <a href="faculty_manage_attendance.php?session_id=<?php echo $session['session_id']; ?>">
                                            Manage Attendance
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No sessions found.</p>
                <?php endif; ?>
            </div>
        </section>

        <section class="addSessionSection">
            <h2>Add New Session</h2>
            <form action="faculty_sessions.php" method="POST">
                <div class="form-group">
                    <label for="course_id">Course:</label>
                    <select name="course_id" id="course_id" required>
                        <?php foreach ($courses as $course): ?>
                            <option value="<?php echo htmlspecialchars($course['course_id']); ?>">
                                <?php echo htmlspecialchars($course['course_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="session_date">Session Date:</label>
                    <input type="date" name="session_date" id="session_date" required>
                </div>
                <div class="form-group">
                    <label for="session_code">Session Code:</label>
                    <input type="number" name="session_code" id="session_code" required>
                </div>
                <button type="submit" class="btn">Add Session</button>
            </form>
        </section>
    </div>
</body>
</html>