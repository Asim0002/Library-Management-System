<?php
session_start();
require 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Book Requests</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <h2>📥 Manage Book Requests</h2>

  <table border="1" cellpadding="10">
    <tr>
      <th>Student</th>
      <th>Book Title</th>
      <th>Request Date</th>
      <th>Status</th>
      <th>Action</th>
    </tr>

    <?php
    $stmt = $conn->query("
      SELECT r.id, u.name AS student_name, b.title AS book_title, r.request_date, r.status
      FROM book_requests r
      JOIN users u ON r.user_id = u.id
      JOIN books b ON r.book_id = b.id
      ORDER BY r.request_date DESC
    ");

    foreach ($stmt as $row):
      echo "<tr>";
      echo "<td>" . htmlspecialchars($row['student_name']) . "</td>";
      echo "<td>" . htmlspecialchars($row['book_title']) . "</td>";
      echo "<td>" . $row['request_date'] . "</td>";
      echo "<td style='color:" . ($row['status'] === 'pending' ? 'orange' : ($row['status'] === 'approved' ? 'green' : 'red')) . "'>" . ucfirst($row['status']) . "</td>";
      echo "<td>";
      if ($row['status'] === 'pending') {
        echo "<form style='display:inline;' action='handle_request_action.php' method='POST'>
                <input type='hidden' name='request_id' value='{$row['id']}'>
                <input type='hidden' name='action' value='approved'>
                <input type='submit' value='Approve'>
              </form>";
        echo " ";
        echo "<form style='display:inline;' action='handle_request_action.php' method='POST'>
                <input type='hidden' name='request_id' value='{$row['id']}'>
                <input type='hidden' name='action' value='rejected'>
                <input type='submit' value='Reject'>
              </form>";
      } else {
        echo "-";
      }
      echo "</td>";
      echo "</tr>";
    endforeach;
    ?>
  </table>

  <p><a href="dashboard.php">⬅ Back to Dashboard</a></p>
</body>
</html>
