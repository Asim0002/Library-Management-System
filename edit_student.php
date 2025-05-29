<?php
session_start();
require 'db.php';

if (!isset($_GET['id'])) {
    header('Location: manage_students.php');
    exit();
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch();

if (!$student) {
    echo "Student not found.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $course = trim($_POST['course']);
    $branch = trim($_POST['branch']);

    $updateStmt = $conn->prepare("UPDATE users SET name = ?, email = ?, course = ?, branch = ? WHERE id = ?");
    $updateStmt->execute([$name, $email, $course, $branch, $id]);

    header('Location: manage_students.php?updated=1');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Student</title>
    <style>
        body { font-family: Arial; background-color: #eef2f3; }
        .form-container {
            max-width: 400px;
            margin: 80px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            position: relative;
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
        }
        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
        }
        button:hover {
            background: #2980b9;
        }
        .back-btn {
            position: absolute;
            top: -45px;
            left: 0;
            padding: 8px 12px;
            background-color: #34495e;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <a href="dashboard.php" class="back-btn">🔙 Back to Dashboard</a>
        <h2>Edit Student</h2>
        <form method="post">
            <input type="text" name="name" value="<?= htmlspecialchars($student['name']) ?>" required>
            <input type="email" name="email" value="<?= htmlspecialchars($student['email']) ?>" required>
            <input type="text" name="course" value="<?= htmlspecialchars($student['course']) ?>" placeholder="Course" required>
            <input type="text" name="branch" value="<?= htmlspecialchars($student['branch']) ?>" placeholder="Branch" required>
            <button type="submit">Update Student</button>
        </form>
    </div>
</body>
</html>
