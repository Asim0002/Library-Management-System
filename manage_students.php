<?php
session_start();
require 'db.php';

$students = $conn->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Students</title>
    <style>
        body { font-family: Arial; background-color: #f4f4f4; margin: 0; padding: 0; }
        .container { padding: 30px; }
        h2 { text-align: center; margin-bottom: 20px; }
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .back-btn {
            padding: 10px 15px;
            background-color: #34495e;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th { background-color: #3498db; color: white; }
        tr:hover { background-color: #f1f1f1; }

        .action-buttons {
            display: flex;
            gap: 10px;
        }
        .action-buttons a {
            padding: 5px 10px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .action-buttons a.delete {
            background: #e74c3c;
        }

        .message {
            text-align: center;
            padding: 10px;
            font-weight: bold;
            border-radius: 5px;
            margin-bottom: 15px;
            width: 100%;
        }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="container">
        <div class="top-bar">
            <h2>Manage Students</h2>
            <a href="dashboard.php" class="back-btn">🔙 Back to Dashboard</a>
        </div>

        <?php if (isset($_GET['updated']) || isset($_GET['added']) || isset($_GET['deleted'])): ?>
            <div id="message" class="message <?= isset($_GET['deleted']) ? 'error' : 'success' ?>">
                <?= isset($_GET['updated']) ? 'Student updated successfully!' : (isset($_GET['added']) ? 'Student added successfully!' : 'Student deleted successfully!') ?>
            </div>
        <?php endif; ?>

        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Course</th>
                <th>Branch</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($students as $student): ?>
            <tr>
                <td><?= $student['id'] ?></td>
                <td><?= htmlspecialchars($student['name']) ?></td>
                <td><?= htmlspecialchars($student['email']) ?></td>
                <td><?= htmlspecialchars($student['course']) ?></td>
                <td><?= htmlspecialchars($student['branch']) ?></td>
                <td>
                    <div class="action-buttons">
                        <a href="edit_student.php?id=<?= $student['id'] ?>">Edit</a>
                        <a href="delete_student.php?id=<?= $student['id'] ?>" class="delete" onclick="return confirm('Are you sure you want to delete this student?');">Delete</a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <script>
        setTimeout(() => {
            const message = document.getElementById('message');
            if (message) message.style.display = 'none';
        }, 2000);
    </script>
</body>
</html>
