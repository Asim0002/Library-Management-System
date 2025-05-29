<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $course = $_POST['course'];
    $branch = $_POST['branch'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name, email, course, branch, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $email, $course, $branch, $password]);

    header("Location: manage_students.php?added=1");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Student</title>
    <style>
        body { font-family: Arial; background-color: #f9f9f9; }
        .form-container {
            width: 400px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 { text-align: center; margin-bottom: 20px; }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #2ecc71;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover { background-color: #27ae60; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Add New Student</h2>
        <form method="POST">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="text" name="course" placeholder="Course" required>
            <input type="text" name="branch" placeholder="Branch" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Add Student</button>
        </form>
    </div>
</body>
</html>
