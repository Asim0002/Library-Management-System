<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
  header("Location: login.php");
  exit();
}
include 'db.php'; // Make sure db.php uses PDO

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $title = trim($_POST['title']);
  $author = trim($_POST['author']);
  $category = trim($_POST['category']);
  $quantity = intval($_POST['quantity']);

  try {
    $stmt = $conn->prepare("INSERT INTO books (title, author, category, quantity) VALUES (:title, :author, :category, :quantity)");
    $stmt->execute([
      ':title' => $title,
      ':author' => $author,
      ':category' => $category,
      ':quantity' => $quantity
    ]);
    header("Location: view_books.php?msg=added");
    exit();
  } catch (PDOException $e) {
    $error = "❌ Failed to add book: " . $e->getMessage();
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Book</title>
  <link rel="stylesheet" href="css/auth.css">
</head>
<body>
  <div class="auth-form">
    <h2>📚 Add New Book</h2>

    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <form method="post">
      <input type="text" name="title" placeholder="Book Title" required><br>
      <input type="text" name="author" placeholder="Author" required><br>

      <select name="category" required>
        <option value="">-- Select Category --</option>
        <option value="Fiction">Fiction</option>
        <option value="Non-Fiction">Non-Fiction</option>
        <option value="Science">Science</option>
        <option value="Technology">Technology</option>
        <option value="Self-Help">Self-Help</option>
        <option value="Biography">Biography</option>
        <option value="History">History</option>
      </select><br>

      <input type="number" name="quantity" placeholder="Quantity" min="1" required><br>
      <button type="submit">➕ Add Book</button>
    </form>

    <p style="text-align:center;"><a href="view_books.php">← Back to Book List</a></p>
  </div>
</body>
</html>
