<?php
session_start();
include '../db.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'Admin' && $_SESSION['role'] != 'Moderator')) {
    header("Location: admin_login.php");
    exit();
}

$category_id = $_GET['id'];

$sql = "DELETE FROM categories WHERE id = $category_id";
if ($conn->query($sql)) {
    header("Location: admin_dashboard.php");
} else {
    echo "Kategori silinirken hata oluştu.";
}
?>
