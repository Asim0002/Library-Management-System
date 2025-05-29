<?php
session_start();
include 'db.php';

$success = $error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = trim($_POST['name']);
  $email = trim($_POST['email']);
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $course = trim($_POST['course']);
  $branch = trim($_POST['branch']);

  $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
  $stmt->execute([$email]);

  if ($stmt->rowCount() > 0) {
    $error = "⚠️ Email already exists.";
  } else {
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, course, branch) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$name, $email, $password, $course, $branch])) {
      $success = "✅ Account created. Redirecting to login...";
      header("refresh:2; url=login_user.php");
    } else {
      $error = "❌ Failed to create account.";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Signup</title>
  <link rel="stylesheet" href="css/auth.css">
</head>
<body>
  <div class="auth-form">
    <h2>🆕 User Signup</h2>

    <?php if ($success): ?>
      <p style="color:green;"><?= $success ?></p>
    <?php endif; ?>
    <?php if ($error): ?>
      <p style="color:red;"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST">
      <input type="text" name="name" placeholder="Name" required><br>
      <input type="email" name="email" placeholder="Email" required><br>
      <input type="password" name="password" placeholder="Password" required><br>
      <input type="text" name="course" placeholder="Course" required><br>
      <input type="text" name="branch" placeholder="Branch" required><br>
      <button type="submit">📝 Signup</button>
    </form>

    <p style="text-align:center;"><a href="login_user.php">🔐 Already have an account? Login</a></p>
  </div>
</body>
</html>
