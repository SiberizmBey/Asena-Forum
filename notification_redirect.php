<?php
include 'db.php';

if (isset($_GET['notification_id'])) {
    $notification_id = $_GET['notification_id'];

    // Bildirimi okundu olarak işaretle
    $sql = "UPDATE notifications SET is_read = 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $notification_id);
    $stmt->execute();

    // İlgili posta veya yoruma yönlendir
    $sql = "SELECT * FROM notifications WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $notification_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $notification = $result->fetch_assoc();

    if ($notification['type'] == 'comment') {
        header("Location: post.php?id=" . $notification['post_id']);
    } elseif ($notification['type'] == 'reply') {
        header("Location: comment.php?id=" . $notification['comment_id']);
    } elseif ($notification['type'] == 'solution') {
        header("Location: post.php?id=" . $notification['post_id']);
    }
    exit();
}
?>
