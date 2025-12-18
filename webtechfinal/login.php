<?php
require_once 'config.php';

$email= "";
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']); 
    
    if (empty($email)) {
        $errors['email'] = "Email is required";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    } 
    if (empty($password)) {
        $errors['password'] = "Password is required";
    }
    if (empty($errors)) {
        $conn = getDBConnection();
        
        $check_sql = "SELECT manager_id, manager_name, business_name, manager_email, manager_password FROM managers WHERE manager_email = ? LIMIT 1";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result=$stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['manager_password'])) {
                session_regenerate_id(true);  
                $_SESSION['user_id'] = $user['manager_id'];
                $_SESSION['user_name'] = $user['manager_name'];
                $_SESSION['business'] = $user['business_name'];
                $_SESSION['logged_in'] = true;
                $_SESSION['role'] = 'Manager';     
                header("Location: manager_dashboard.php");
                exit();
            }else{
                $errors['login'] = "Invalid email or password.";
            }
        } else {
            $check_sql = "SELECT employee_id, employee_name, employee_email, employee_password, manager_id FROM employees WHERE employee_email = ? LIMIT 1";
            $stmt = $conn->prepare($check_sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result=$stmt->get_result();
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                if (password_verify($password, $user['employee_password'])) {
                    session_regenerate_id(true);  
                    $_SESSION['user_id'] = $user['employee_id'];
                    $_SESSION['user_name'] = $user['employee_name'];
                    $_SESSION['manager_id'] = $user['manager_id'];
                    $_SESSION['logged_in'] = true;
                    $_SESSION['role'] = 'Employee';     
                    header("Location: employee_dashboard.php");
                    exit();
                }else{
                    $errors['login'] = "Invalid email or password.";
                }
            }else{
                $errors['login'] = "Invalid email or password.";
            }
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
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="login.js" defer></script>
</head>
<body class="bg-dark d-flex align-items-center min-vh-100">
    <div class= "container">
        <h2 class= "text-center text-white"> Welcome to Rental Flow!</h2>
    </div>
    <div class="container">
        <div class="mx-auto p-4 shadow rounded bg-white" style="max-width:500px;">
            <h3 class="text-center mb-4">Login to Rental Flow</h3>
            
            <?php if(isset($errors['login'])): ?>
                <div class="alert alert-danger text-center"><?php echo $errors['login']; ?></div>
            <?php endif; ?>

            <form method="POST" id="login" novalidate>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" 
                           class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                           id="email" 
                           name="email" 
                           value="<?php echo htmlspecialchars($email); ?>" 
                           required>
                    <div class="invalid-feedback"><?php echo $errors['email'] ?? ''; ?></div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" 
                           class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" 
                           id="password" 
                           name="password" 
                           required>
                    <div class="invalid-feedback"><?php echo $errors['password'] ?? ''; ?></div>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">Login</button>
                    <hr>
                    <p class="text-center text-muted small mb-0">Manage your own business?</p>
                    <a href="manager_registration.php" class="btn btn-outline-secondary">Register as Manager</a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>