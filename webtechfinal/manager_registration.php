<?php
require_once 'config.php';

$manager_name = $business_name = $manager_email = $manager_phone = "";
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $manager_name = trim($_POST['manager_name']);
    $business_name = trim($_POST['business_name']);
    $manager_email = trim($_POST['manager_email']);
    $manager_password = trim($_POST['manager_password']); 
    $confirm_password = trim($_POST['confirmPassword']);
    $manager_phone = trim($_POST['manager_phone']);

    if (empty($manager_name)) $errors['name'] = "Name is required.";
    if (empty($business_name)) $errors['business'] = "Business name is required.";
    
    if (!preg_match('/^[0-9]{10}$/', $manager_phone)) {
        $errors['phone'] = "Phone must be exactly 10 digits.";
    }

    if (!filter_var($manager_email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    }

    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $manager_password)) {
        $errors['password'] = "Password must be 8+ chars with Uppercase, Lowercase, and Number.";
    }

    if ($manager_password !== $confirm_password) {
        $errors['confirm'] = "Passwords do not match.";
    }

    if (empty($errors)) {
        $conn = getDBConnection();
        
        $check_sql = "SELECT manager_id FROM managers WHERE manager_email = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("s", $manager_email);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows > 0) {
            $errors['email'] = "This email is already registered.";
        } else {
            $hashed_password = password_hash($manager_password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO managers (manager_name, business_name, manager_email, manager_password, manager_phone) VALUES (?,?,?,?,?)";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $manager_name, $business_name, $manager_email, $hashed_password, $manager_phone);
            
            if ($stmt->execute()) {
                header("Location: login.php?status=success");
                exit();
            } else {
                $errors['db'] = "Database error: " . $conn->error;
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
    <title>Manager Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="manager_registration.js" defer></script>
</head>
<body class="bg-dark">
    <div class="mx-auto mt-5 p-4 shadow rounded bg-white" style="max-width:600px;">
      <h3 class="text-center mb-3">Manager Registration</h3>
      
      <?php if(isset($errors['db'])): ?>
          <div class="alert alert-danger"><?php echo $errors['db']; ?></div>
      <?php endif; ?>

      <form method="POST" id="managerRegistration" novalidate>
        
        <div class="mb-3">
          <label for="managerName" class="form-label">Name</label>
          <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" 
                 id="managerName" name="manager_name" 
                 value="<?php echo htmlspecialchars($manager_name); ?>" required>
          <div class="invalid-feedback"><?php echo $errors['name'] ?? 'Please enter your name.'; ?></div>
        </div>

        <div class="mb-3">
          <label for="businessName" class="form-label">Business Name</label>
          <input type="text" class="form-control <?php echo isset($errors['business']) ? 'is-invalid' : ''; ?>" 
                 id="businessName" name="business_name" 
                 value="<?php echo htmlspecialchars($business_name); ?>" required>
          <div class="invalid-feedback"><?php echo $errors['business'] ?? 'Please enter your business name.'; ?></div>
        </div>

        <div class="mb-3">
          <label for="managerEmail" class="form-label">Email Address</label>
          <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                 id="managerEmail" name="manager_email" 
                 value="<?php echo htmlspecialchars($manager_email); ?>" required>
          <div class="invalid-feedback"><?php echo $errors['email'] ?? 'Please enter a valid email address.'; ?></div>
        </div>

        <div class="mb-3">
          <label for="managerPhone" class="form-label">Phone Number</label>
          <input type="tel" class="form-control <?php echo isset($errors['phone']) ? 'is-invalid' : ''; ?>" 
                 id="managerPhone" name="manager_phone" 
                 value="<?php echo htmlspecialchars($manager_phone); ?>" required>
          <div class="invalid-feedback"><?php echo $errors['phone'] ?? 'Please enter a 10-digit phone number.'; ?></div>
        </div>

        <div class="mb-3">
          <label for="managerPassword" class="form-label">Password</label>
          <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" 
                 id="managerPassword" name="manager_password" required>
          <div class="invalid-feedback"><?php echo $errors['password'] ?? 'Password must include uppercase, lowercase, and number.'; ?></div>
        </div>
        
        <div class="mb-3">
            <label for="confirmPassword" class="form-label">Confirm Password</label>
            <input type="password" class="form-control <?php echo isset($errors['confirm']) ? 'is-invalid' : ''; ?>" 
                   id="confirmPassword" name="confirmPassword" required>
            <div class="invalid-feedback"><?php echo $errors['confirm'] ?? 'Passwords do not match.'; ?></div>
        </div>

        <div class="d-grid gap-2">
          <button type="submit" class="btn btn-primary">Register</button>
          <a href="login.php" class="btn btn-outline-secondary">Already have an account? Login</a>
        </div>
      </form>
    </div>
</body>
</html>