<?php
session_start();
include 'db.php';

if (!isset($_GET['user_id'])) {
    header('Location: view_conversations.php');
    exit();
}

$user1_id = $_SESSION['user_id'];
$user2_id = $_GET['user_id'];

// Mevcut bir konuşma var mı kontrol et
$sql = "SELECT id FROM conversations 
        WHERE (user1_id = ? AND user2_id = ?) 
           OR (user1_id = ? AND user2_id = ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('iiii', $user1_id, $user2_id, $user2_id, $user1_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Mevcut konuşmayı al
    $conversation = $result->fetch_assoc();
    $conversation_id = $conversation['id'];
} else {
    // Yeni bir konuşma başlat
    $sql = "INSERT INTO conversations (user1_id, user2_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $user1_id, $user2_id);
    $stmt->execute();
    $conversation_id = $stmt->insert_id;
}

// Konuşma sayfasına yönlendir
header("Location: view_conversation.php?conversation_id=$conversation_id");
exit();
?>
