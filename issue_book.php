<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
  header("Location: login.php");
  exit();
}
include 'db.php';

// Fetch all books with quantity > 0
$books_stmt = $conn->query("SELECT id, title FROM books WHERE quantity > 0");
$books = $books_stmt->fetchAll();

// Fetch all students from users table
$students_stmt = $conn->query("SELECT id, name FROM users ORDER BY name");
$students = $students_stmt->fetchAll();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $student_id = $_POST['student_id'];
  $book_id = $_POST['book_id'];
  $issue_date = date('Y-m-d');

  // Get student name using ID
  $get_name_stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
  $get_name_stmt->execute([$student_id]);
  $student = $get_name_stmt->fetch();

  if ($student) {
    $student_name = $student['name'];

    $stmt = $conn->prepare("INSERT INTO issue_records (student_name, book_id, issue_date) VALUES (?, ?, ?)");
    if ($stmt->execute([$student_name, $book_id, $issue_date])) {
      $update_stmt = $conn->prepare("UPDATE books SET quantity = quantity - 1 WHERE id = ?");
      $update_stmt->execute([$book_id]);

      $success = "✅ Book issued to <strong>$student_name</strong> successfully!";
    } else {
      $error = "❌ Failed to issue book.";
    }
  } else {
    $error = "❌ Invalid student selected.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Issue Book</title>
  <link rel="stylesheet" href="css/auth.css">
  <style>
    select, input, button {
      margin-top: 10px;
      width: 100%;
      padding: 10px;
      font-size: 16px;
    }
    .message {
      text-align: center;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <div class="auth-form">
    <h2>📕 Issue Book</h2>

    <?php if (isset($success)) echo "<p class='message' style='color:green;'>$success</p>"; ?>
    <?php if (isset($error)) echo "<p class='message' style='color:red;'>$error</p>"; ?>

    <form method="post">
      <?php if (count($students) > 0): ?>
        <select name="student_id" required>
          <option value="">-- Select Student --</option>
          <?php foreach ($students as $row): ?>
            <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
          <?php endforeach; ?>
        </select>
      <?php else: ?>
        <p style="color:red;">❌ No students found. Please add students first.</p>
      <?php endif; ?>

      <?php if (count($books) > 0): ?>
        <select name="book_id" required>
          <option value="">-- Select Book --</option>
          <?php foreach ($books as $row): ?>
            <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['title']) ?></option>
          <?php endforeach; ?>
        </select>
        <button type="submit">📤 Issue Book</button>
      <?php else: ?>
        <p style="color:red;">❌ No books available to issue (quantity is 0).</p>
      <?php endif; ?>
    </form>

    <p style="text-align:center;"><a href="dashboard.php">← Back to Dashboard</a></p>
  </div>
</body>
</html>
