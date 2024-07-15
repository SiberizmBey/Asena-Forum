<?php
session_start();
include '../db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $new_role = $_POST['role'];

    // SQL injection'dan korunmak için prepared statements kullanın
    $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->bind_param('si', $new_role, $user_id);

    if ($stmt->execute()) {
        // Başarı mesajı ekleyebilir veya admin paneline yönlendirebilirsiniz
        header("Location: admin_dashboard.php");
        exit();
    } else {
        echo "Hata: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
