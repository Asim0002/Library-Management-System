<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
  header("Location: login.php");
  exit();
}
include 'db.php';

// Get today's date
$today = date('Y-m-d');

// Query to find overdue books (not returned and return_date < today)
$sql = "SELECT ir.id, ir.student_name, b.title, ir.issue_date, ir.return_date
        FROM issue_records ir
        JOIN books b ON ir.book_id = b.id
        WHERE ir.return_date IS NULL AND DATE(ir.issue_date) < DATE_SUB(:today, INTERVAL 14 DAY)
        ORDER BY ir.issue_date ASC";

$stmt = $conn->prepare($sql);
$stmt->execute([':today' => $today]);
$defaulters = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Defaulters List</title>
  <link rel="stylesheet" href="css/auth.css">
  <style>
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
      color: #c0392b;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th, td {
      padding: 12px;
      text-align: center;
      border-bottom: 1px solid #ddd;
    }

    th {
      background-color: #c0392b;
      color: white;
    }

    tr:hover {
      background-color: #f9f9f9;
    }

    .overdue {
      color: red;
      font-weight: bold;
    }
  </style>
</head>
<body>

  <div class="container">
    <h2>🚨 Defaulters - Overdue Books</h2>
    <table>
      <tr>
        <th>ID</th>
        <th>Student Name</th>
        <th>Book Title</th>
        <th>Issued On</th>
        <th>Status</th>
      </tr>

      <?php
      if (count($defaulters) > 0) {
        foreach ($defaulters as $row) {
          echo "<tr>
            <td>{$row['id']}</td>
            <td>".htmlspecialchars($row['student_name'])."</td>
            <td>".htmlspecialchars($row['title'])."</td>
            <td>{$row['issue_date']}</td>
            <td class='overdue'>❌ Overdue</td>
          </tr>";
        }
      } else {
        echo "<tr><td colspan='5'>No defaulters found.</td></tr>";
      }
      ?>
    </table>
      <a href="dashboard.php" class="back-btn">⬅ Back to Dashboard</a>
  </div>

</body>
</html>
