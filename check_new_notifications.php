<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['hasNewNotifications' => false]);
    exit();
}

$user_id = $_SESSION['user_id'];

// Yeni bildirimleri kontrol eden sorgu
$sql = "SELECT COUNT(*) AS new_notifications
        FROM notifications
        WHERE user_id = ?
        AND seen = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$hasNewNotifications = $row['new_notifications'] > 0;

echo json_encode(['hasNewNotifications' => $hasNewNotifications]);
?>
