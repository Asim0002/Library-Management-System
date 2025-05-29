<?php
session_start();

$isAdmin = isset($_SESSION['admin_id']);
$isUser = isset($_SESSION['user_id']);

session_unset();
session_destroy();

if ($isAdmin) {
  header("Location: login.php?msg=logout");
} elseif ($isUser) {
  header("Location: login_user.php?msg=logout");
} else {
  header("Location: index.php?msg=logout");
}
exit();
