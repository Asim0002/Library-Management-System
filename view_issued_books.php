<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
  header("Location: login.php");
  exit();
}
include 'db.php';

// Handle filters
$studentFilter = isset($_GET['student_name']) ? trim($_GET['student_name']) : '';
$dateFilter = isset($_GET['issue_date']) ? trim($_GET['issue_date']) : '';

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$sql = "FROM issue_records ir 
        JOIN books b ON ir.book_id = b.id 
        WHERE 1 ";
$params = [];

if (!empty($studentFilter)) {
  $sql .= "AND ir.student_name LIKE :student_name ";
  $params[':student_name'] = "%" . $studentFilter . "%";
}

if (!empty($dateFilter)) {
  $sql .= "AND DATE(ir.issue_date) = :issue_date ";
  $params[':issue_date'] = $dateFilter;
}

// Total count
$countStmt = $conn->prepare("SELECT COUNT(*) $sql");
$countStmt->execute($params);
$totalRecords = $countStmt->fetchColumn();
$totalPages = ceil($totalRecords / $limit);

// Fetch records
$sqlQuery = "SELECT ir.id, ir.student_name, ir.issue_date, ir.return_date, b.title " . $sql . " 
             ORDER BY ir.issue_date DESC, ir.id DESC 
             LIMIT :limit OFFSET :offset";
$stmt = $conn->prepare($sqlQuery);

foreach ($params as $key => $val) {
  $stmt->bindValue($key, $val);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$issuedBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Issued Books</title>
  <link rel="stylesheet" href="css/auth.css">
  <style>
    body {
      background-color: #f4f6f9;
      font-family: Arial, sans-serif;
    }

    .container {
      width: 90%;
      margin: 50px auto;
      background: white;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      color: #2c3e50;
      margin-bottom: 20px;
    }

    .filter-form {
      display: flex;
      justify-content: center;
      gap: 10px;
      margin-bottom: 20px;
      flex-wrap: wrap;
    }

    .filter-form input[type="text"],
    .filter-form input[type="date"] {
      padding: 8px;
      border-radius: 6px;
      border: 1px solid #ccc;
      width: 200px;
    }

    .filter-form button {
      padding: 8px 16px;
      background-color: #2980b9;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: bold;
    }

    .filter-form button:hover {
      background-color: #1c5980;
    }

    .filter-form .reset-btn {
      background-color: #e74c3c;
    }

    .filter-form .reset-btn:hover {
      background-color: #c0392b;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    th, td {
      padding: 12px;
      text-align: center;
      border-bottom: 1px solid #ddd;
    }

    th {
      background-color: #2c3e50;
      color: white;
    }

    tr:hover {
      background-color: #f9f9f9;
    }

    .return-btn {
      display: inline-block;
      padding: 6px 12px;
      background-color: #2980b9;
      color: white;
      text-decoration: none;
      border-radius: 5px;
      font-weight: bold;
    }

    .return-btn:hover {
      background-color: #1c5980;
    }

    .pagination {
      text-align: center;
      margin-top: 20px;
    }

    .pagination a {
      padding: 8px 14px;
      margin: 2px;
      border: 1px solid #ccc;
      text-decoration: none;
      color: #2c3e50;
      border-radius: 4px;
    }

    .pagination a.active {
      background-color: #2980b9;
      color: white;
      font-weight: bold;
    }

    .pagination a:hover {
      background-color: #ddd;
    }

    .top-bar {
      text-align: left;
      margin-bottom: 10px;
    }

    .top-bar a {
      text-decoration: none;
      padding: 6px 12px;
      background-color: #27ae60;
      color: white;
      border-radius: 5px;
      font-weight: bold;
    }

    .top-bar a:hover {
      background-color: #1e8449;
    }
  </style>
</head>
<body>

  <div class="container">
    <h2>📋 Issued Books List</h2>

    <!-- Back Button -->
    <div class="top-bar">
      <a href="dashboard.php">⬅️ Back to Dashboard</a>
    </div>

    <!-- Filter Form -->
    <form method="GET" class="filter-form">
      <input type="text" name="student_name" placeholder="Search by student name" value="<?= htmlspecialchars($studentFilter) ?>">
      <input type="date" name="issue_date" value="<?= htmlspecialchars($dateFilter) ?>">
      <button type="submit">🔍 Filter</button>
      <a href="view_issued_books.php" style="text-decoration:none;">
        <button type="button" class="reset-btn">🔄 Reset</button>
      </a>
    </form>

    <!-- Issued Books Table -->
    <table>
      <tr>
        <th>ID</th>
        <th>Student Name</th>
        <th>Book Title</th>
        <th>Issued On</th>
        <th>Returned On</th>
        <th>Actions</th>
      </tr>
      <?php
      if (count($issuedBooks) > 0) {
        foreach ($issuedBooks as $row) {
          echo "<tr>
            <td>{$row['id']}</td>
            <td>" . htmlspecialchars($row['student_name']) . "</td>
            <td>" . htmlspecialchars($row['title']) . "</td>
            <td>{$row['issue_date']}</td>
            <td>" . ($row['return_date'] ? $row['return_date'] : "Not Returned") . "</td>
            <td>";
          if (!$row['return_date']) {
            echo "<a href='return_book.php?id={$row['id']}' class='return-btn' onclick=\"return confirm('Mark this book as returned?');\">Return</a>";
          } else {
            echo "✅ Returned";
          }
          echo "</td></tr>";
        }
      } else {
        echo "<tr><td colspan='6'>No books found for selected filters.</td></tr>";
      }
      ?>
    </table>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
      <div class="pagination">
        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
          <a class="<?= ($p == $page) ? 'active' : '' ?>" href="?page=<?= $p ?>&student_name=<?= urlencode($studentFilter) ?>&issue_date=<?= urlencode($dateFilter) ?>"><?= $p ?></a>
        <?php endfor; ?>
      </div>
    <?php endif; ?>
  </div>

</body>
</html>
