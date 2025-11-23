<?php
require_once 'config.php';

$error = '';
$success = '';
$student_name = '';
$email = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_name = trim($_POST['student_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    $errors = [];
    if (empty($student_name) || empty($email) || empty($password)) {
        $errors[] = "All fields are required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    } 
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $password)) {
        $errors[] = "Password must be at least 8 characters and include an uppercase letter, a lowercase letter, and a number.";
    }

    if (empty($errors)) {
        $conn = getDBConnection();
        
        $check_sql = "SELECT student_id FROM students WHERE email = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $error = "Email already registered.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            
            $sql = "INSERT INTO students (student_name, email, password) 
                    VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $student_name, $email, $hashed_password);
            
            if ($stmt->execute()) {
                $success = "Registration successful! You can now <a href='login_student.php'>login here</a>.";
                $student_name = $email = '';
            } else {
                $error = "Registration failed. Please try again.";
            }
            $stmt->close();
        }
        $check_stmt->close();
        $conn->close();
    } else {
        $error = implode("<br>", $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="form-container">
        <h2>Student Registration</h2>
        
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo $error;?></div>
        <?php endif; ?>
    
        <?php if (!empty($success)): ?>
            <div class="success"><?php echo $success;?></div>
        <?php endif; ?>
        
        <form method="POST" action="register_student.php" novalidate>
            <div class="form-group">
                <label for="student_name">Name:</label>
                <input 
                    type="text" 
                    id="student_name" 
                    name="student_name" 
                    value="<?php echo htmlspecialchars($student_name); ?>"
                    aria-required="true" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="<?php echo htmlspecialchars($email); ?>"
                    aria-required="true" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" 
                    id="password" 
                    name="password" 
                    pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" 
                    title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" 
                    aria-required="true" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" 
                    id="confirm_password" 
                    name="confirm_password" 
                    aria-required="true" required>
            </div>
            
            <button type="submit" class="btn">Register</button>
        </form>
         <div class="register-link">
            Already have an account? <a href="login_student.php">Login here</a>
        </div>
    </div>
</body>
</html>