<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])) {
  header("Location: login_user.php");
  exit();
}
include 'db.php';
$userName = $_SESSION['user_name'];

$stmt = $conn->prepare("
  SELECT books.title, books.author, books.category, ir.issue_date, ir.return_date
  FROM issue_records ir
  JOIN books ON ir.book_id = books.id
  WHERE ir.student_name = ?
  ORDER BY ir.issue_date DESC
");
$stmt->execute([$userName]);
$records = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Issued Books</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <h2>📖 My Issued Books</h2>

  <?php if (count($records) === 0): ?>
    <p>No books issued yet.</p>
  <?php else: ?>
    <table border="1" cellpadding="10" cellspacing="0">
      <tr>
        <th>#</th>
        <th>Title</th>
        <th>Author</th>
        <th>Category</th>
        <th>Issue Date</th>
        <th>Due Date</th>
      </tr>
      <?php foreach ($records as $i => $row): ?>
        <tr>
          <td><?= $i + 1 ?></td>
          <td><?= htmlspecialchars($row['title']) ?></td>
          <td><?= htmlspecialchars($row['author']) ?></td>
          <td><?= htmlspecialchars($row['category']) ?></td>
          <td><?= htmlspecialchars($row['issue_date']) ?></td>
          <td>
            <?= $row['return_date'] 
            ? htmlspecialchars($row['return_date']) 
            : date('Y-m-d', strtotime($row['issue_date'] . ' +14 days')) ?>
            </td>

        </tr>
      <?php endforeach; ?>
    </table>
  <?php endif; ?>

  <p><a href="user_dashboard.php">⬅ Back to Dashboard</a></p>
</body>
</html>
