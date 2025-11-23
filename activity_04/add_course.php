<?php
require_once 'config.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login_faculty.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_name = trim($_POST['course_name'] ?? '');

    if (!empty($course_name)) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("INSERT INTO courses (faculty_id, course_name) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $course_name);
        
        if ($stmt->execute()) {
            header("Location: faculty_dashboard.php");
            exit();
        } else {
            $error = "Error adding course. Please try again.";
        }

        $stmt->close();
        $conn->close();
    } else {
        $error = "Course name cannot be empty.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <script src="faculty_dashboard.js" defer></script>
</head>
<body>
    <nav>
        <a href="faculty_dashboard.php">Dashboard</a>
        <a href="logout.php">Logout</a>
    </nav>
    <h2>Add New Course</h2>
    <form id="addCourseForm" method="POST" action="add_course.php">
        <div class="form-group">
            <label for="course_name">Course Name:</label>
            <input 
                type="text" 
                id="course_name" 
                name="course_name" 
                aria-required="true" required>
        </div>
        <button type="submit" class="btn">Add Course</button>
    </form>
</body>
</html>