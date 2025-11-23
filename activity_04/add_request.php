<?php
require_once 'config.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login_faculty.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = trim($_POST['course_id'] ?? '');
    if (empty($course_id)) {
        $error = "Course selection is required.";
    } else {
        $conn = getDBConnection();
        $sql = "INSERT INTO requests (student_id, course_id, approved) 
                VALUES (?, ?, false)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $user_id, $course_id);
        
        if ($stmt->execute()) {
            $success = "Request to join the course has been submitted successfully.";
        } else {
            $error = "Failed to submit request. Database error.";
        }
        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request to Join a Course</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <a href="student_dashboard.php">Student Dashboard</a>
        <a href="logout.php">Logout</a>
    </nav>
    <div class="container">
        <header>
            <h1>Request to Join a Course</h1>
        </header>
        <main>
            <form method="POST" action="add_request.php">
                <div class="form-group">
                    <label for="course_id">Select Course:</label>
                    <select id="course_id" name="course_id" required>
                        <?php
                        $conn = getDBConnection();
                        $stmt = $conn->prepare("SELECT course_id, course_name FROM courses");
                        $stmt->execute();
                        $result = $stmt->get_result();
                        while ($row = $result->fetch_assoc()) {
                            echo '<option value="' . htmlspecialchars($row['course_id']) . '">' . htmlspecialchars($row['course_name']) . '</option>';
                        }
                        $stmt->close();
                        $conn->close();
                        ?>
                    </select>
                </div>
                <button type="submit" class="btn">Request</button>
            </form>
        </main>
    </div>
</body>
</html>