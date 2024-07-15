<?php
session_start();
include 'db.php';

if (!isset($_POST['recipient_id']) || !isset($_POST['message_content'])) {
    header('Location: view_conversations.php');
    exit();
}

$sender_id = $_SESSION['user_id'];
$recipient_id = intval($_POST['recipient_id']);
$message_content = trim($_POST['message_content']);

// Konuşmayı kontrol et veya oluştur
$sql = "SELECT id FROM conversations 
        WHERE (user1_id = ? AND user2_id = ?) 
           OR (user1_id = ? AND user2_id = ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('iiii', $sender_id, $recipient_id, $recipient_id, $sender_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $conversation = $result->fetch_assoc();
    $conversation_id = $conversation['id'];
} else {
    $sql = "INSERT INTO conversations (user1_id, user2_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $sender_id, $recipient_id);
    $stmt->execute();
    $conversation_id = $stmt->insert_id;
}

// Mesajı kaydet
$sql = "INSERT INTO messages (conversation_id, sender_id, content, created_at) VALUES (?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param('iis', $conversation_id, $sender_id, $message_content);
$stmt->execute();

// Konuşma sayfasına yönlendir
header("Location: view_conversation.php?conversation_id=$conversation_id");
exit();
?>
