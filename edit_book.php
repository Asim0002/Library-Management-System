<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
  header("Location: login.php");
  exit();
}
include 'db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
  echo "Invalid book ID.";
  exit;
}

// Fetch book details
$stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
$stmt->execute([$id]);
$book = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$book) {
  echo "Book not found.";
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = $_POST['title'];
  $author = $_POST['author'];
  $category = $_POST['category'];
  $quantity = $_POST['quantity'];

  $stmt = $conn->prepare("UPDATE books SET title = ?, author = ?, category = ?, quantity = ? WHERE id = ?");
  $success = $stmt->execute([$title, $author, $category, $quantity, $id]);

  if ($success) {
    header("Location: view_books.php");
    exit;
  } else {
    $error = "Failed to update book.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Book</title>
  <link rel="stylesheet" href="css/auth.css">
</head>
<body>
  <div class="auth-form">
    <h2>✏️ Edit Book</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="post">
      <input type="text" name="title" value="<?= htmlspecialchars($book['title']) ?>" required>
      <input type="text" name="author" value="<?= htmlspecialchars($book['author']) ?>" required>
      <input type="text" name="category" value="<?= htmlspecialchars($book['category']) ?>">
      <input type="number" name="quantity" value="<?= htmlspecialchars($book['quantity']) ?>" min="0" required>
      <button type="submit">✅ Update</button>
    </form>
    <p style="text-align:center;"><a href="view_books.php">← Back</a></p>
  </div>
</body>
</html>
