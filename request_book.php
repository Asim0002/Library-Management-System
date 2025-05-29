<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['book_id'])) {
    header('Location: view_books_user.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$book_id = $_POST['book_id'];
$request_date = date('Y-m-d');

// Prevent duplicate requests
$check = $conn->prepare("SELECT * FROM book_requests WHERE user_id = ? AND book_id = ? AND status = 'pending'");
$check->execute([$user_id, $book_id]);

if ($check->rowCount() == 0) {
    $stmt = $conn->prepare("INSERT INTO book_requests (user_id, book_id, request_date, status) VALUES (?, ?, ?, 'pending')");
    $stmt->execute([$user_id, $book_id, $request_date]);
    $_SESSION['message'] = "Book request submitted successfully.";
} else {
    $_SESSION['message'] = "You already requested this book.";
}

header('Location: view_books_user.php');
exit();
