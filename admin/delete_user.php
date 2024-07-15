<?php
include '../db.php';
session_start();

// Admin kontrolü
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Admin') {
    header("Location: login.php");
    exit();
}

$user_id = $_GET['id'];
$sql = "DELETE FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    header("Location: admin_panel.php");
} else {
    echo "Kullanıcı silinirken bir hata oluştu.";
}
?>
