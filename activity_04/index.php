<?php
require_once 'config.php';

// Check if the user is already logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    // If they are logged in, send them to their specific dashboard
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'faculty') {
        header("Location: faculty_dashboard.php");
        exit();
    } elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'student') {
        header("Location: student_dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Course Management</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="form-container">
        <h2>Welcome</h2>
        <p class="welcome-text">Please select your role to continue:</p>

        <a href="login_student.php" class="btn">I am a Student</a>

        <div class="role-separator">- OR -</div>

        <a href="login_faculty.php" class="btn btn-faculty">I am Faculty</a>
        
        <div class="register-link" style="margin-top: 30px;">
            <p>New here?</p>
            <a href="register_student.php">Register as Student</a> | 
            <a href="register_faculty.php">Register as Faculty</a>
        </div>
    </div>

</body>
</html>