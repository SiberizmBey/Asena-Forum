<?php
session_start();
include '../db.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'Admin' && $_SESSION['role'] != 'Moderator')) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    
    $sql = "INSERT INTO categories (name) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $name);
    if ($stmt->execute()) {
        header("Location: admin_dashboard.php");
    } else {
        echo "Kategori eklerken hata oluştu.";
    }
}
?>
