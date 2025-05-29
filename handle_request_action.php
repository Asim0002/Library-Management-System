<?php
session_start();
require 'db.php';

if (!isset($_SESSION['admin_id']) || !isset($_POST['request_id']) || !isset($_POST['action'])) {
    header("Location: dashboard.php");
    exit();
}

$requestId = $_POST['request_id'];
$action = $_POST['action'];

// Validate action
if (!in_array($action, ['approved', 'rejected'])) {
    header("Location: manage_requests.php");
    exit();
}

// Update request status
$stmt = $conn->prepare("UPDATE book_requests SET status = ? WHERE id = ?");
$stmt->execute([$action, $requestId]);

// If approved, insert into issue_records
if ($action === 'approved') {
    // Get user_id and book_id from request
    $stmt = $conn->prepare("SELECT user_id, book_id FROM book_requests WHERE id = ?");
    $stmt->execute([$requestId]);
    $request = $stmt->fetch();

    if ($request) {
        $user_id = $request['user_id'];
        $book_id = $request['book_id'];
        $issue_date = date("Y-m-d");

        // Insert into issue_records
        $stmt = $conn->prepare("INSERT INTO issue_records (student_name, book_id, issue_date, status) VALUES ((SELECT name FROM users WHERE id = ?), ?, ?, 'issued')");
        $stmt->execute([$user_id, $book_id, $issue_date]);

        // Optional: Mark book status as issued in books table
        $conn->prepare("UPDATE books SET status = 'issued' WHERE id = ?")->execute([$book_id]);
    }
}

header("Location: manage_requests.php");
exit();
