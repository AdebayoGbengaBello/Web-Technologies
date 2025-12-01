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
    SELECT s.session_id, s.course_id, s.course_name, s.session_date
    FROM sessions s
    JOIN courses c ON s.course_id = c.course_id
    WHERE c.faculty_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$sessions = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$stmt = $conn->prepare("
    SELECT c.course_id, c.course_name
    FROM courses c
    WHERE c.faculty_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$courses = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = $_POST['course_id'];
    $session_date = $_POST['session_date'];

    $insert_stmt = $conn->prepare("
        INSERT INTO sessions (course_id, course_name, session_date)
        VALUES (?, (
        SELECT course_name 
        FROM courses WHERE course_id = ?)
        , ?)
    ");
    $insert_stmt->bind_param("iis", $course_id, $course_id, $session_date);
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Sessions</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <a href="faculty_dashboard.php">Dashboard</a>
        <a href="logout.php">Logout</a>
    </nav>
    <div class="container">
        <header>
            <h1>Faculty Sessions</h1>
        </header>

        <section class="sessionSection">
            <h2>Your Sessions</h2>
            <div id="sessionsContainer">
                <?php if (count($sessions) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Course Name</th>
                                <th>Session Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sessions as $session): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($session['course_name']); ?></td>
                                    <td><?php echo htmlspecialchars($session['session_date']); ?></td>
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
                <label for="course_id">Course:</label>
                <select name="course_id" id="course_id" required>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?php echo htmlspecialchars($course['course_id']); ?>">
                            <?php echo htmlspecialchars($course['course_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="session_date">Session Date:</label>
                <input type="date" name="session_date" id="session_date" required>

                <button type="submit">Add Session</button>
            </form>
    </div>
</body>
</html>