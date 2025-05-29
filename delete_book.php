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

$stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
if ($stmt->execute([$id])) {
  header("Location: view_books.php");
  exit;
} else {
  echo "❌ Error deleting book.";
}
?>
