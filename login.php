<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT id, password FROM admins WHERE email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $admin['id'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}

// Handle admin forgot password
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset_password'])) {
    $email = $_POST['reset_email'];
    $new_password = $_POST['new_password'];

    $stmt = $conn->prepare("SELECT id FROM admins WHERE email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if ($admin) {
        $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
        $updateStmt = $conn->prepare("UPDATE admins SET password = ? WHERE email = ?");
        $updateStmt->execute([$hashedPassword, $email]);
        $success = "✅ Password reset successful! You can now log in.";
    } else {
        $reset_error = "❌ Email not found in admin records.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; }
        .login-container { width: 300px; margin: 100px auto; padding: 20px; background: #fff; border-radius: 5px; }
        input[type="email"], input[type="password"] { width: 100%; padding: 10px; margin: 5px 0; }
        button { width: 100%; padding: 10px; background: #5cb85c; color: #fff; border: none; }
        .error { color: red; }
        .toggle-link { color: blue; cursor: pointer; text-align: center; display: block; margin-top: 10px; }
        .success { color: green; text-align: center; }
    </style>
    <script>
      function toggleResetForm() {
        const form = document.getElementById('reset-form');
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
      }
    </script>
</head>
<body>
<?php if (isset($_GET['msg']) && $_GET['msg'] == 'logout'): ?>
  <p style="color:green; text-align:center;">✅ Logout Successful</p>
<?php endif; ?>

<div class="login-container">
    <h2>Admin Login</h2>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
    <form method="post">
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit" name="login">Login</button>
    </form>

    <span class="toggle-link" onclick="toggleResetForm()">Forgot Password?</span>

    <form id="reset-form" method="post" style="display:none; margin-top: 20px;">
        <input type="email" name="reset_email" placeholder="Enter your admin email" required><br>
        <input type="password" name="new_password" placeholder="Enter new password" required><br>
        <button type="submit" name="reset_password">Reset Password</button>
        <?php if (isset($reset_error)) echo "<p class='error'>$reset_error</p>"; ?>
    </form>
</div>
</body>
</html>
