<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}
require 'db.php';

// Stats
$studentCount = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
$bookCount = $conn->query("SELECT COUNT(*) FROM books")->fetchColumn();
$issuedCount = $conn->query("SELECT COUNT(*) FROM issue_records")->fetchColumn();

// Recent entries
$recentStudents = $conn->query("SELECT id, name, email, course, branch FROM users ORDER BY created_at DESC LIMIT 5")->fetchAll();
$recentBooks = $conn->query("SELECT id, title, author, category, quantity FROM books ORDER BY created_at DESC LIMIT 5")->fetchAll();
$recentIssues = $conn->query("
    SELECT ir.student_name, b.title AS book_title, ir.issue_date, ir.return_date
    FROM issue_records ir
    JOIN books b ON ir.book_id = b.id
    ORDER BY ir.issue_date DESC
    LIMIT 5
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        display: flex;
        background: url('https://images.unsplash.com/photo-1607971813857-7b8f18fd02c2?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') no-repeat center center fixed;
        background-size: cover;
        color: #333;
    }
    .sidebar {
        width: 220px;
        background: rgba(0, 0, 0, 0.85);
        color: #fff;
        padding: 20px;
        height: 240vh;
    }
    .sidebar h2 {
        font-size: 20px;
        margin-bottom: 30px;
        color: rgba(255, 255, 255, 0.95);
    }
    .sidebar a {
        color: #fff;
        display: block;
        margin: 12px 0;
        text-decoration: none;
        padding: 8px 10px;
        border-radius: 5px;
        transition: background 0.3s;
    }
    .sidebar a:hover {
        background: #3c3c3c;
    }
    .main {
        flex: 1;
        padding: 30px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(3px);
        min-height: 100vh;
        overflow-y: auto;
    }
    h1 {
        margin-top: 0;
        font-size: 26px;
    }
    h3 {
        margin-top: 40px;
        color: #333;
    }
    .stats {
        display: flex;
        gap: 20px;
        margin-top: 20px;
        flex-wrap: wrap;
    }
    .stat {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        flex: 1;
        text-align: center;
        min-width: 200px;
    }
    .stat h3 {
        margin: 0;
        font-size: 18px;
        color: #555;
    }
    .stat p {
        font-size: 24px;
        margin: 10px 0 0;
        font-weight: bold;
        color: #222;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
        background: white;
        border-radius: 8px;
        overflow: hidden;
    }
    th, td {
        padding: 12px;
        border: 1px solid #ddd;
        text-align: left;
    }
    th {
        background: #3498db;
        color: white;
    }
    tr:hover {
        background-color: #eaf4fc;
    }
    .overdue {
        color: red;
        font-weight: bold;
    }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Library Management System</h2>
        <a href="dashboard.php">📊 Dashboard</a>
        <a href="manage_students.php">👨‍🎓 Manage Students</a>
        <a href="view_books.php">📚 Manage Books</a>
        <a href="issue_book.php">📖 Issue Book</a>
        <a href="return_book.php">🔁 Return Book</a>
        <a href="view_issued_books.php">📋 View Issued Books</a>
        <a href="defaulter.php">❗Defaulter List</a>
        <a href="logout.php">🚪Logout</a>
    </div>

    <div class="main">
        <h1>Welcome, Admin 👨🏻‍💻</h1>

        <div class="stats">
            <div class="stat">
                <h3>Students</h3>
                <p><?= $studentCount ?></p>
            </div>
            <div class="stat">
                <h3>Books</h3>
                <p><?= $bookCount ?></p>
            </div>
            <div class="stat">
                <h3>Issued Books</h3>
                <p><?= $issuedCount ?></p>
            </div>
        </div>

        <h3>🎓 Recent Students</h3>
        <table>
            <tr>
                <th>ID</th><th>Name</th><th>Email</th><th>Course</th><th>Branch</th>
            </tr>
            <?php foreach ($recentStudents as $student): ?>
                <tr>
                    <td><?= $student['id'] ?></td>
                    <td><?= htmlspecialchars($student['name']) ?></td>
                    <td><?= htmlspecialchars($student['email']) ?></td>
                    <td><?= htmlspecialchars($student['course']) ?></td>
                    <td><?= htmlspecialchars($student['branch']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <h3>📚 Recent Books</h3> 
        <table>
            <tr>
                <th>ID</th><th>Title</th><th>Author</th><th>Category</th><th>Quantity</th>
            </tr>
            <?php foreach ($recentBooks as $book): ?>
                <tr>
                    <td><?= $book['id'] ?></td>
                    <td><?= htmlspecialchars($book['title']) ?></td>
                    <td><?= htmlspecialchars($book['author']) ?></td>
                    <td><?= htmlspecialchars($book['category']) ?></td>
                    <td><?= $book['quantity'] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <h3>📖 Recent Issued Books</h3>
        <table>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Book Title</th>
                    <th>Issue Date</th>
                    <th>Due Date</th>
                    <th>Return Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($recentIssues) > 0): ?>
                    <?php foreach ($recentIssues as $issue): 
                        $dueDate = date('Y-m-d', strtotime($issue['issue_date'] . ' +7 days'));
                        $isOverdue = (is_null($issue['return_date']) || $issue['return_date'] === '') && date('Y-m-d') > $dueDate;
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($issue['student_name']) ?></td>
                            <td><?= htmlspecialchars($issue['book_title']) ?></td>
                            <td><?= htmlspecialchars($issue['issue_date']) ?></td>
                            <td class="<?= $isOverdue ? 'overdue' : '' ?>"><?= $dueDate ?></td>
                            <td><?= htmlspecialchars($issue['return_date'] ?? 'N/A') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No recent issues found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php
$recentRequests = $conn->query("
    SELECT u.name AS student_name, b.title AS book_title, r.request_date, r.status
    FROM book_requests r
    JOIN users u ON r.user_id = u.id
    JOIN books b ON r.book_id = b.id
    ORDER BY r.request_date DESC
    LIMIT 5
")->fetchAll();
?>

<h3>📥 Requested Books</h3>
<table>
    <thead>
        <tr>
            <th>Student</th>
            <th>Book Title</th>
            <th>Request Date</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($recentRequests) > 0): ?>
            <?php foreach ($recentRequests as $req): ?>
                <tr>
                    <td><?= htmlspecialchars($req['student_name']) ?></td>
                    <td><?= htmlspecialchars($req['book_title']) ?></td>
                    <td><?= htmlspecialchars($req['request_date']) ?></td>
                    <td><?= htmlspecialchars($req['status']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="4">No requests found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<p style="padding-top: 20px"><a href="manage_requests.php">📥 Manage Book Requests</a></p>
    </div>
</body>
</html>
