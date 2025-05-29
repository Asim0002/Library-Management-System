<?php
include 'db.php';

// Add Category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
  $name = trim($_POST['name']);
  if (!empty($name)) {
    $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
    $stmt->bind_param("s", $name);
    if ($stmt->execute()) {
      $success = "✅ Category added!";
    } else {
      $error = "❌ Category already exists.";
    }
    $stmt->close();
  } else {
    $error = "❗ Please enter a category name.";
  }
}

// Delete Category
if (isset($_GET['delete'])) {
  $id = $_GET['delete'];
  $conn->query("DELETE FROM categories WHERE id = $id");
  header("Location: categories.php");
  exit();
}

// Fetch All Categories
$categories = $conn->query("SELECT * FROM categories ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Categories</title>
  <link rel="stylesheet" href="css/auth.css">
  <style>
    body {
      background-color: #f5f7fa;
      font-family: Arial, sans-serif;
    }
    .container {
      width: 500px;
      margin: 50px auto;
      background: white;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      color: #2c3e50;
      margin-bottom: 20px;
    }
    form {
      display: flex;
      justify-content: space-between;
      gap: 10px;
      margin-bottom: 20px;
    }
    input[type="text"] {
      flex: 1;
      padding: 8px;
    }
    button {
      padding: 8px 15px;
      background-color: #2980b9;
      color: white;
      border: none;
      border-radius: 5px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    th, td {
      text-align: left;
      padding: 10px;
      border-bottom: 1px solid #ccc;
    }
    .delete-btn {
      color: white;
      background-color: #c0392b;
      padding: 4px 10px;
      border-radius: 5px;
      text-decoration: none;
    }
  </style>
</head>
<body>

  <div class="container">
    <h2>📂 Manage Book Categories</h2>

    <?php if (isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <form method="post">
      <input type="text" name="name" placeholder="Enter category name" required>
      <button type="submit">Add</button>
    </form>

    <table>
      <tr>
        <th>Category</th>
        <th>Action</th>
      </tr>
      <?php while ($row = $categories->fetch_assoc()) { ?>
        <tr>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><a class="delete-btn" href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this category?')">Delete</a></td>
        </tr>
      <?php } ?>
    </table>
  </div>

</body>
</html>
