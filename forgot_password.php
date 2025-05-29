<?php
include 'db.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = $_POST['email'];
  $type = $_POST['type'];
  $newPassword = $_POST['new_password'];
  $confirmPassword = $_POST['confirm_password'];

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $message = "❌ Invalid email format.";
  } elseif ($newPassword !== $confirmPassword) {
    $message = "❌ Passwords do not match.";
  } else {
    $table = $type === 'admin' ? 'admins' : 'users';

    // Check if user/admin exists
    $stmt = $conn->prepare("SELECT id FROM $table WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
      $hashed = password_hash($newPassword, PASSWORD_DEFAULT);

      $update = $conn->prepare("UPDATE $table SET password = ? WHERE email = ?");
      $update->execute([$hashed, $email]);

      $message = "✅ Password successfully reset!";
    } else {
      $message = "❌ No account found with that email.";
    }
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Forgot Password</title>
  <style>
    body { font-family: Arial; background: #f4f4f4; }
    .form-box { background: #fff; padding: 20px; margin: 50px auto; width: 350px; border-radius: 8px; }
    input, select, button { width: 100%; padding: 10px; margin: 10px 0; }
    p { text-align: center; color: red; }
  </style>
</head>
<body>
  <div class="form-box">
    <h2>🔁 Forgot Password</h2>
    <?php if ($message): ?><p><?= $message ?></p><?php endif; ?>
    <form method="post">
      <input type="email" name="email" placeholder="Enter your email" required>
      <select name="type" required>
        <option value="">-- Select Role --</option>
        <option value="user">User</option>
        <option value="admin">Admin</option>
      </select>
      <input type="password" name="new_password" placeholder="New Password" required>
      <input type="password" name="confirm_password" placeholder="Confirm Password" required>
      <button type="submit">Reset Password</button>
    </form>
    <p><a href="login.php">← Admin Login</a> | <a href="login_user.php">User Login</a></p>
  </div>
</body>
</html>
