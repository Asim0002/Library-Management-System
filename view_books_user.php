<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login_user.php");
  exit();
}
include 'db.php';
$userId = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Available Books</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <h2>📚 Available Books</h2>

  <table border="1" cellpadding="10" cellspacing="0">
    <tr>
      <th>#</th>
      <th>Title</th>
      <th>Author</th>
      <th>Category</th>
      <th>Status</th>
      <th>Action</th>
    </tr>

    <?php
    $stmt = $conn->query("SELECT * FROM books ORDER BY created_at DESC");
    $count = 1;
    $hasBooks = false;

    while ($row = $stmt->fetch()) {
      $hasBooks = true;
      echo "<tr>";
      echo "<td>" . $count++ . "</td>";
      echo "<td>" . htmlspecialchars($row['title']) . "</td>";
      echo "<td>" . htmlspecialchars($row['author']) . "</td>";
      echo "<td>" . htmlspecialchars($row['category']) . "</td>";
      echo "<td>" . ($row['status'] ?? 'Available') . "</td>";
      echo "<td><form action='request_book.php' method='POST' onsubmit=\"return confirm('Request this book?');\">
              <input type='hidden' name='book_id' value='{$row['id']}'>
              <input type='submit' value='Request Book'>
            </form></td>";
      echo "</tr>";
    }

    if (!$hasBooks) {
      echo "<tr><td colspan='6' style='text-align:center;'>No books found!</td></tr>";
    }
    ?>
  </table>

  <p><a href="user_dashboard.php">⬅ Back to Dashboard</a></p>
</body>
</html>
