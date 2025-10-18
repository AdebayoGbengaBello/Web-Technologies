
<?php
//  database config
require_once 'config.php';

// php variables det get 
$email = '';
$password = '';
$error = '';
$success = '';

// Check if form was submitted  JSON 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Get and sanitize input
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validation(CLaude Not ness since html handles, but just in cae bypassed)
    if (empty($email)) {
        $error = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } elseif (empty($password)) {
        $error = "Password is required";
    } else {
        //  database connection
        $conn = getDBConnection();
        
        // Prepare SQL statement(prevents sqlnject)
        $sql = "SELECT userId, firstName, lastName, email, password_hash 
                FROM users 
                WHERE email = ? 
                LIMIT 1";
        
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            // Bind email parameter
            $stmt->bind_param("s", $email);
            
            // Execute query
            $stmt->execute();
            
            // Get result
            $result = $stmt->get_result();
            
            // Check if user exists
            if ($result->num_rows === 1) {
                // Fetch user data
                $user = $result->fetch_assoc();
                
                // Verify password against hash (ish it could be on plintext)
                // password_verify() compares plain text password with hashed password
                if (password_verify($password, $user['password_hash'])) {
                    //  if s correct Login successful
                    
                    // Regenerate session ID to prevent session fixation(???)
                    session_regenerate_id(true);
                    
                    // Store user information in session
                    $_SESSION['user_id'] = $user['userId'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_name'] = $user['firstName'] . ' ' . $user['lastName'];
                    $_SESSION['logged_in'] = true;
                    $_SESSION['login_time'] = time();
                    
                    // Redirect to dashboard
                    header("Location: dashboard.php");
                    exit();
                    
                } else {
                    // Password is incorrect
                    $error = "Invalid email or password";
                }
            } else {
                // User not found
                $error = "Invalid email or password";
            }
            
            $stmt->close();
        } else {
            $error = "Database error. Please try again later.";
        }
        
        $conn->close();
    }
}
?>
<!-- HTML and CSS for the login page -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <title>Login - Student Dashboard</title>
</head>
<body>
    <div class="login-container">
        <h2>Student Login</h2>
        
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="<?php echo htmlspecialchars($email); ?>"
                    required
                    placeholder="your.email@student.edu">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required
                    placeholder="Enter your password">
            </div>
            
            <button type="submit" class="btn">Login</button>
        </form>
        
        <div class="register-link">
            Don't have an account? <a href="register.php">Register here</a>
        </div>
    </div>
</body>
</html>
