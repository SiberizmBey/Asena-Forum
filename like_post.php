<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['post_id'])) {
    header("Location: login.php");
    exit();
}

$type = 'like_test';  // Farklı bir değer deneyelim
$user_id = $_SESSION['user_id'];
$post_id = $_POST['post_id'];

// Kullanıcının zaten beğenip beğenmediğini kontrol et
$sql = "SELECT * FROM likes WHERE user_id = ? AND post_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $user_id, $post_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // Beğen
    $sql = "INSERT INTO likes (user_id, post_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $user_id, $post_id);
    $stmt->execute();

    // Bildirim oluştur
    $sql = "SELECT posts.user_id AS post_user_id, posts.title AS post_title, users.username AS liker_username 
            FROM posts 
            JOIN users ON users.id = ? 
            WHERE posts.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $user_id, $post_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $post = $result->fetch_assoc();
        $notification_sql = "INSERT INTO notifications (user_id, post_id, post_title, type, commenter_id, commenter_name) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($notification_sql);
        $type = 'like';  // Bildirim türünü burada belirtiyoruz
        $stmt->bind_param('iissis', $post['post_user_id'], $post_id, $post['post_title'], $type, $user_id, $post['liker_username']);
        $stmt->execute();
    }

} else {
    // Beğeniyi geri al
    $sql = "DELETE FROM likes WHERE user_id = ? AND post_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $user_id, $post_id);
    $stmt->execute();
}

header("Location: post.php?id=$post_id");
exit();
?>