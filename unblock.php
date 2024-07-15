<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    die('Lütfen giriş yapın.');
}

$blocker_id = $_SESSION['user_id'];
$blocked_id = isset($_POST['blocked_id']) ? intval($_POST['blocked_id']) : 0;

if ($blocked_id) {
    // Kullanıcıyı engelden çıkar
    $sql = "DELETE FROM blocks WHERE blocker_id = ? AND blocked_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $blocker_id, $blocked_id);
    $stmt->execute();
    header('Location: profile.php?id=' . $blocked_id);
} else {
    die('Geçersiz kullanıcı.');
}
?>
