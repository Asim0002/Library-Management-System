<?php
include 'db.php';
session_start();
if (!isset($_SESSION['admin_id'])) {
  header("Location: login.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Books</title>
  <link rel="stylesheet" href="css/auth.css">
  <style>
    body {
      background-color: #f5f7fa;
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
      text-align: center;
      margin-bottom: 20px;
    }

    .filter-form select, .filter-form button {
      padding: 8px 12px;
      font-size: 16px;
      margin: 0 5px;
      border-radius: 6px;
      border: 1px solid #ccc;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }

    th, td {
      padding: 12px 15px;
      text-align: center;
      border-bottom: 1px solid #ddd;
    }

    th {
      background-color: #2c3e50;
      color: white;
    }

    tr:hover {
      background-color: #f1f1f1;
    }

    .actions a {
      padding: 6px 12px;
      margin: 0 4px;
      text-decoration: none;
      border-radius: 5px;
      color: white;
      font-size: 14px;
    }

    .actions .edit-btn {
      background-color: #3498db;
    }

    .actions .edit-btn:hover {
      background-color: #2c80b4;
    }

    .actions .delete-btn {
      background-color: #e74c3c;
    }

    .actions .delete-btn:hover {
      background-color: #c0392b;
    }

    .actions .qty-btn {
      background-color: #f39c12;
    }

    .actions .qty-btn:hover {
      background-color: #d68910;
    }

    .top-btn {
      display: inline-block;
      margin-bottom: 20px;
      padding: 10px 15px;
      background-color: #27ae60;
      color: white;
      border-radius: 6px;
      text-decoration: none;
    }

    .top-btn:hover {
      background-color: #1e8449;
    }

    .back-btn {
      display: inline-block;
      margin-top: 30px;
      padding: 10px 20px;
      background-color: #34495e;
      color: white;
      border-radius: 6px;
      text-decoration: none;
    }

    .back-btn:hover {
      background-color: #2c3e50;
    }
  </style>
</head>
<body>

<div class="container">
  <h2>📚 Library Books List</h2>
  <a class="top-btn" href="add_new_book.php">➕ Add New Book</a>

  <form method="GET" class="filter-form">
    <label for="category">Filter by Category:</label>
    <select name="category" id="category">
      <option value="">All</option>
      <?php
        $cat_stmt = $conn->query("SELECT DISTINCT category FROM books WHERE category IS NOT NULL AND category != ''");
        while ($cat = $cat_stmt->fetch(PDO::FETCH_ASSOC)) {
          $selected = ($_GET['category'] ?? '') === $cat['category'] ? 'selected' : '';
          echo "<option value='".htmlspecialchars($cat['category'])."' $selected>".htmlspecialchars($cat['category'])."</option>";
        }
      ?>
    </select>
    <button type="submit">Filter</button>
  </form>

  <table>
    <tr>
      <th>ID</th>
      <th>Title</th>
      <th>Author</th>
      <th>Category</th>
      <th>Quantity</th>
      <th>Added On</th>
      <th>Actions</th>
    </tr>

    <?php
      $category = $_GET['category'] ?? '';
      if (!empty($category)) {
        $stmt = $conn->prepare("SELECT * FROM books WHERE category = ? ORDER BY added_on DESC");
        $stmt->execute([$category]);
      } else {
        $stmt = $conn->query("SELECT * FROM books ORDER BY added_on DESC");
      }

      $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
      if (count($books) > 0) {
        foreach ($books as $row) {
          echo "<tr>
            <td>{$row['id']}</td>
            <td>".htmlspecialchars($row['title'])."</td>
            <td>".htmlspecialchars($row['author'])."</td>
            <td>".htmlspecialchars($row['category'])."</td>
            <td>{$row['quantity']}</td>
            <td>{$row['added_on']}</td>
            <td class='actions'>
              <a href='edit_book.php?id={$row['id']}' class='edit-btn'>Edit</a>
              <a href='delete_book.php?id={$row['id']}' class='delete-btn' onclick=\"return confirm('Are you sure you want to delete this book?');\">Delete</a>
              <a href='update_quantity.php?book_id={$row['id']}' class='qty-btn'>Update Quantity</a>
            </td>
          </tr>";
        }
      } else {
        echo "<tr><td colspan='7'>No books found.</td></tr>";
      }
    ?>
  </table>

  <a href="dashboard.php" class="back-btn">⬅ Back to Dashboard</a>
</div>

</body>
</html>
