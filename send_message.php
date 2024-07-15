<?php
session_start();
include 'db.php';

$conversation_id = $_POST['conversation_id'];
$sender_id = $_SESSION['user_id'];
$content = $_POST['content'];

$sql = "INSERT INTO messages (conversation_id, sender_id, content, timestamp) VALUES (?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param('iis', $conversation_id, $sender_id, $content);
$stmt->execute();

header("Location: view_conversation.php?conversation_id=$conversation_id");
exit();
?>
