<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = $_POST["name"];
  $email = $_POST["email"];
  $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

  $stmt = $conn->prepare("INSERT INTO admins (name, email, password) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $name, $email, $password);
  
  if ($stmt->execute()) {
    echo "<script>alert('Signup successful. You can now login.'); window.location.href='login.php';</script>";
  } else {
    echo "Error: " . $stmt->error;
  }

  $stmt->close();
  $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Signup</title>
  <link rel="stylesheet" href="css/auth.css">
</head>
<body>
  <div class="auth-form">
    <h2>📝 Create Admin Account</h2>
    <form method="post" action="">
      <input type="text" name="name" placeholder="Full Name" required><br>
      <input type="email" name="email" placeholder="Email" required><br>
      <input type="password" name="password" placeholder="Password" required><br>
      <button type="submit">Create Account</button>
    </form>
    <p style="text-align:center;"><a href="login.php">Already have an account? Login</a></p>
  </div>
</body>
</html>
