<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
  header("Location: login.php");
  exit();
}
include 'db.php';

// Handle return form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['issue_id'])) {
  $issue_id = $_POST['issue_id'];

  $stmt = $conn->prepare("SELECT book_id FROM issue_records WHERE id = ?");
  $stmt->execute([$issue_id]);
  $book_id = $stmt->fetchColumn();

  if ($book_id) {
    $return_date = date('Y-m-d');
    $stmt = $conn->prepare("UPDATE issue_records SET return_date = ? WHERE id = ?");
    $stmt->execute([$return_date, $issue_id]);

    $stmt = $conn->prepare("UPDATE books SET quantity = quantity + 1 WHERE id = ?");
    $stmt->execute([$book_id]);

    header("Location: return_book.php?success=1");
    exit();
  } else {
    $error = "❌ Invalid issue ID.";
  }
}

$success = isset($_GET['success']) ? "✅ Book returned successfully!" : null;

// Pagination logic for return history
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$totalRows = $conn->query("SELECT COUNT(*) FROM issue_records WHERE return_date IS NOT NULL")->fetchColumn();
$totalPages = ceil($totalRows / $limit);

$returned = $conn->query("SELECT ir.id, ir.student_name, b.title, ir.issue_date, ir.return_date 
  FROM issue_records ir 
  JOIN books b ON ir.book_id = b.id 
  WHERE ir.return_date IS NOT NULL 
  ORDER BY ir.return_date DESC 
  LIMIT $limit OFFSET $offset")->fetchAll(PDO::FETCH_ASSOC);

$unreturned = $conn->query("SELECT ir.id, ir.student_name, b.title 
  FROM issue_records ir 
  JOIN books b ON ir.book_id = b.id 
  WHERE ir.return_date IS NULL 
  ORDER BY ir.issue_date DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Return Books</title>
  <link rel="stylesheet" href="css/auth.css">
  <style>
    .container {
      width: 90%;
      margin: 40px auto;
      padding: 20px;
      background: white;
      border-radius: 12px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
    h2 { text-align: center; color: #2c3e50; }
    .message {
      text-align: center;
      font-weight: bold;
      margin: 10px 0;
      padding: 10px;
      border-radius: 8px;
    }
    .success { background: #d4edda; color: #155724; }
    .error { background: #f8d7da; color: #721c24; }
    select, button, input[type="text"] {
      width: 100%;
      padding: 10px;
      margin: 10px 0;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 30px;
    }
    th, td {
      padding: 10px;
      text-align: center;
      border-bottom: 1px solid #ccc;
    }
    th {
      background: #2c3e50;
      color: white;
    }
    .pagination {
      text-align: center;
      margin-top: 20px;
    }
    .pagination a {
      padding: 8px 14px;
      margin: 0 4px;
      background: #eee;
      text-decoration: none;
      color: #2c3e50;
      border-radius: 6px;
    }
    .pagination a.active {
      background: #2c3e50;
      color: white;
    }
    .search-bar {
      margin: 20px 0;
    }
  </style>
</head>
<body>

<div class="container">
  <h2>📥 Return Book</h2>

  <?php if (isset($success)) echo "<p class='message success' id='alert'>$success</p>"; ?>
  <?php if (isset($error)) echo "<p class='message error' id='alert'>$error</p>"; ?>

  <form method="POST">
    <select name="issue_id" required>
      <option value="">-- Select Book to Return --</option>
      <?php foreach ($unreturned as $record): ?>
        <option value="<?= $record['id'] ?>">
          <?= htmlspecialchars($record['student_name']) ?> - <?= htmlspecialchars($record['title']) ?>
        </option>
      <?php endforeach; ?>
    </select>
    <button type="submit">✅ Mark as Returned</button>
  </form>

  <h2>📜 Return History</h2>

  <input type="text" id="search" class="search-bar" placeholder="🔍 Search by student or book title...">

  <table id="historyTable">
    <thead>
      <tr>
        <th>ID</th>
        <th>Student</th>
        <th>Book</th>
        <th>Issued On</th>
        <th>Returned On</th>
      </tr>
    </thead>
    <tbody>
    <?php if ($returned): ?>
      <?php foreach ($returned as $row): ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= htmlspecialchars($row['student_name']) ?></td>
          <td><?= htmlspecialchars($row['title']) ?></td>
          <td><?= $row['issue_date'] ?></td>
          <td><?= $row['return_date'] ?></td>
        </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr><td colspan="5">No return history available.</td></tr>
    <?php endif; ?>
    </tbody>
  </table>

  <div class="pagination">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
      <a href="?page=<?= $i ?>" class="<?= ($i == $page) ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
  </div>

  <a href="dashboard.php" class="back-btn">⬅ Back to Dashboard</a>
</div>

<script>
// Auto-hide alert
setTimeout(() => {
  const alert = document.getElementById("alert");
  if (alert) alert.style.display = "none";
}, 3000);

// Search filter
const searchInput = document.getElementById("search");
searchInput.addEventListener("keyup", function () {
  const filter = searchInput.value.toLowerCase();
  const rows = document.querySelectorAll("#historyTable tbody tr");

  rows.forEach(row => {
    const student = row.cells[1].textContent.toLowerCase();
    const book = row.cells[2].textContent.toLowerCase();
    row.style.display = student.includes(filter) || book.includes(filter) ? "" : "none";
  });
});
</script>

</body>
</html>
