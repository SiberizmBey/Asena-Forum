<?php
session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['Admin', 'Moderator'])) {
    header("Location: admin_login.php");
    exit();
}
?>
