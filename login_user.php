<?php
session_start();
include 'db.php';

$showResetForm = isset($_GET['reset']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['reset_password'])) {
        // Handle password reset for users
        $email = trim($_POST['reset_email']);
        $newPassword = trim($_POST['new_password']);

        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update->execute([$hashedPassword, $user['id']]);
            $success = "✅ Password reset successfully.";
        } else {
            $error = "❌ No user found with this email.";
        }
    } else {
        // Handle user login
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];

            $stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
            $stmt->execute([$user['id']]);
            $user_data = $stmt->fetch();
            $_SESSION['user_name'] = $user_data['name'];

            header("Location: user_dashboard.php");
            exit();
        } else {
            $error = "❌ Invalid email or password!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Login</title>
  <link rel="stylesheet" href="css/auth.css">
</head>
<body>
<?php if (isset($_GET['msg']) && $_GET['msg'] == 'logout'): ?>
  <p style="color:green; text-align:center;">✅ Logout Successful</p>
<?php endif; ?>

<div class="auth-form">
    <?php if ($showResetForm): ?>
        <h2>🔑 Reset Password (User)</h2>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <?php if (isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
        <form method="post">
            <input type="email" name="reset_email" placeholder="Your Email" required><br>
            <input type="password" name="new_password" placeholder="New Password" required><br>
            <button type="submit" name="reset_password">Reset Password</button>
        </form>
        <p style="text-align:center;"><a href="login_user.php">← Back to Login</a></p>
    <?php else: ?>
        <h2>🔐 User Login</h2>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <form method="post">
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit">Login</button>
        </form>
        <p style="text-align:center;"><a href="register_user.php">Don't have an account? Register</a></p>
        <p style="text-align:center;"><a href="?reset=1">Forgot Password?</a></p>
    <?php endif; ?>
</div>
</body>
</html>
