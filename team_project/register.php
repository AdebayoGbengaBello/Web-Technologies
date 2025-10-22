<?php
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company_name = trim($_POST['company_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    $errors = [];
    if (empty($company_name) || empty($email) || empty($phone) || empty($password)) {
        $errors[] = "All fields are required.";
    }
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
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
        
        $check_sql = "SELECT developer_id FROM Developers WHERE email = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $error = "Email already registered.";
        } else {
            $sql = "INSERT INTO Developers (company_name, email, phone, password) 
                    VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $company_name, $email, $phone, $password);
            
            if ($stmt->execute()) {
                $success = "Registration successful! You can now login.";
                $company_name = $email = $phone = '';
            } else {
                $error = "Registration failed. Please try again.";
            }
            $stmt->close();
        }
        $check_stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Developer Register</title>
    <body>
        <main>
            <?php if (!empty($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
            <form method="POST" action="register.php">
                <label for="company_name">Company Name:</label>
                <input 
                    type="text" 
                    id="company_name" 
                    name="company_name" 
                    aria-required="true" required>

                <label for="email">Email:</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    aria-required="true" required>

                <label for="phone">Phone:</label>
                <input type="text" 
                    id="phone" 
                    name="phone" 
                    aria-required="true" required>

                <label for="password">Password:</label>
                <input type="password" 
                    id="password" 
                    name="password" 
                    pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" 
                    title="Must contain at least one  number and one uppercase and lowercase letter, and at least 8 or more characters" 
                    aria-required="true" required>

                <label for="confirm_password">Confirm Password:</label>
                <input type="password" 
                    id="confirm_password" 
                    name="confirm_password" 
                    aria-required="true" required>
                <button type="submit">Register</button>
            </form>
        </main>
    </body>
</html>