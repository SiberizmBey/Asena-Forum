<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'db.php';

$user_id = 1;  // Örnek kullanıcı ID'si
$post_id = 1;  // Örnek post ID'si
$comment_id = 1;  // Örnek yorum ID'si
$content = "Örnek yorum içerik";  // Örnek yorum içeriği
$post_owner_id = 2;  // Örnek post sahibi ID'si
$post_title = "Örnek post başlığı";  // Örnek post başlığı
$commenter_name = "Örnek kullanıcı adı";  // Örnek kullanıcı adı

// Post sahibine bildirim gönder
$notification_sql = "INSERT INTO notifications (user_id, post_id, comment_id, comment_content, commenter_id, commenter_name, post_title, type) VALUES (?, ?, ?, ?, ?, ?, ?, 'comment')";
$stmt = $conn->prepare($notification_sql);
if (!$stmt) {
    die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
}
$stmt->bind_param("iiisiis", $post_owner_id, $post_id, $comment_id, $content, $user_id, $commenter_name, $post_title);
if (!$stmt->execute()) {
    die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
}
echo "Bildirim başarıyla eklendi.";
$stmt->close();
?>
