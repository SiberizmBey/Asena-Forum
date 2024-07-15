<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['comment_id']) || !isset($_POST['post_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$comment_id = $_POST['comment_id'];
$post_id = $_POST['post_id'];

// Kullanıcının zaten beğenip beğenmediğini kontrol et
$sql = "SELECT * FROM comment_likes WHERE user_id = ? AND comment_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $user_id, $comment_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // Beğen
    $sql = "INSERT INTO comment_likes (user_id, comment_id, post_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iii', $user_id, $comment_id, $post_id);
    $stmt->execute();

    // Bildirim oluştur
    $sql = "SELECT comments.user_id AS comment_user_id, comments.content AS comment_content, users.username AS liker_username 
            FROM comments 
            JOIN users ON users.id = ? 
            WHERE comments.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $user_id, $comment_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $comment = $result->fetch_assoc();
        $notification_sql = "INSERT INTO notifications (user_id, post_id, comment_id, comment_content, type, commenter_id, commenter_name) VALUES (?, ?, ?, ?, 'like_comment', ?, ?)";
        $stmt = $conn->prepare($notification_sql);
        $stmt->bind_param('iiisis', $comment['comment_user_id'], $post_id, $comment_id, $comment['comment_content'], $user_id, $comment['liker_username']);
        $stmt->execute();
    }

} else {
    // Beğeniyi geri al
    $sql = "DELETE FROM comment_likes WHERE user_id = ? AND comment_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $user_id, $comment_id);
    $stmt->execute();
}

header("Location: post.php?id=$post_id");
exit();
?>
