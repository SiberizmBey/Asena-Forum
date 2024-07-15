<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$comment_id = $_GET['id'];

// Yorumu ve ilgili gönderiyi al
$sql = "SELECT * FROM comments WHERE id = $comment_id";
$comment_result = $conn->query($sql);
$comment = $comment_result->fetch_assoc();

if (!$comment) {
    echo "Yorum bulunamadı.";
    exit();
}

$sql = "SELECT * FROM posts WHERE id = " . $comment['post_id'];
$post_result = $conn->query($sql);
$post = $post_result->fetch_assoc();

if (!$post) {
    echo "Gönderi bulunamadı.";
    exit();
}

// Kullanıcı gönderinin sahibi mi kontrol et
if ($_SESSION['user_id'] != $post['user_id']) {
    echo "Bu işlemi yapma yetkiniz yok.";
    exit();
}

// Yorumu çözüm işaretinden çıkar
$sql = "UPDATE comments SET is_solution = 0 WHERE id = $comment_id";
if ($conn->query($sql) === TRUE) {
    header('Location: post.php?id=' . $comment['post_id']);
} else {
    echo "Hata: " . $conn->error;
}
?>
