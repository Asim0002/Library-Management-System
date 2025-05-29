<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
  header("Location: login.php");
  exit();
}
include 'db.php';

$book_id_param = $_GET['book_id'] ?? '';
$success = $error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $book_id = $_POST['book_id'];
  $new_quantity = intval($_POST['quantity']);

  $stmt = $conn->prepare("UPDATE books SET quantity = ? WHERE id = ?");
  if ($stmt->execute([$new_quantity, $book_id])) {
    $success = "✅ Book quantity updated successfully!";
  } else {
    $error = "❌ Failed to update quantity.";
  }
}

// Fetch all books
$stmt = $conn->query("SELECT id, title, quantity FROM books");
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Update Book Quantity</title>
  <link rel="stylesheet" href="css/auth.css">
  <style>
    select, input, button {
      margin-top: 10px;
      width: 100%;
      padding: 10px;
    }
    .message {
      text-align: center;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <div class="auth-form">
    <h2>🔄 Update Book Quantity</h2>

    <?php if ($success) echo "<p class='message' style='color:green;'>$success</p>"; ?>
    <?php if ($error) echo "<p class='message' style='color:red;'>$error</p>"; ?>

    <form method="post">
      <select name="book_id" required>
        <option value="">-- Select Book --</option>
        <?php foreach ($books as $book): ?>
          <option value="<?= $book['id'] ?>" <?= ($book['id'] == $book_id_param) ? 'selected' : '' ?>>
            <?= htmlspecialchars($book['title']) ?> (Current: <?= $book['quantity'] ?>)
          </option>
        <?php endforeach; ?>
      </select>

      <input type="number" name="quantity" placeholder="Enter New Quantity" min="0" required>

      <button type="submit">✅ Update Quantity</button>
    </form>

    <p style="text-align:center;"><a href="view_books.php">← Back to Manage Books</a></p>
  </div>
</body>
</html>
