<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login_user.php");
  exit();
}
$userName = $_SESSION['user_name']; // Assuming name is stored on login
?>

<!DOCTYPE html>
<html>
<head>
  <title>User Dashboard</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f7f7f7;
      margin: 0;
      padding: 0;
    }
    .container {
      width: 90%;
      max-width: 600px;
      margin: 60px auto;
      background-color: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      text-align: center;
    }
    h2 {
      color: #2c3e50;
    }
    .link-btn {
      display: block;
      margin: 15px auto;
      padding: 12px 20px;
      width: 80%;
      background-color: #3498db;
      color: white;
      text-decoration: none;
      border-radius: 6px;
      font-size: 16px;
      font-weight: bold;
    }
    .link-btn:hover {
      background-color: #2c80b4;
    }
  </style>
</head>
<body>

<div class="container">
  <h2>Welcome, <?= htmlspecialchars($userName) ?>! 👋</h2>

  <a href="view_books_user.php" class="link-btn">📚 View Available Books</a>
  <a href="my_issued_books.php" class="link-btn">📋 View My Issued Books</a>
  <a href="login_user.php" class="link-btn" style="background-color: #e74c3c;">🚪 Logout</a>
</div>

</body>
</html>
