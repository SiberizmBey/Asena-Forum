<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$comment_id = $_GET['id'];

// Yorumu al
$sql = "SELECT * FROM comments WHERE id = $comment_id";
$comment_result = $conn->query($sql);
$comment = $comment_result->fetch_assoc();

if (!$comment) {
    echo "Yorum bulunamadı.";
    exit();
}

// Kullanıcı yorumu sahibi mi kontrol et
if ($_SESSION['user_id'] != $comment['user_id']) {
    echo "Bu yorumu silme yetkiniz yok.";
    exit();
}

// Yorumu sil
$sql = "DELETE FROM comments WHERE id = $comment_id";
if ($conn->query($sql) === TRUE) {
    header('Location: post.php?id=' . $comment['post_id']);
} else {
    echo "Hata: " . $conn->error;
}
?>
