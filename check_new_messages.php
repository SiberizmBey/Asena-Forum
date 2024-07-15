<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['hasNewMessages' => false]);
    exit();
}

$user_id = $_SESSION['user_id'];

// Yeni mesajları kontrol eden sorgu
$sql = "SELECT COUNT(*) AS new_messages
        FROM messages m
        JOIN conversations c ON m.conversation_id = c.id
        WHERE (c.user1_id = ? OR c.user2_id = ?)
        AND m.sender_id != ?
        AND m.is_read = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param('iii', $user_id, $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$hasNewMessages = $row['new_messages'] > 0;

echo json_encode(['hasNewMessages' => $hasNewMessages]);
?>
