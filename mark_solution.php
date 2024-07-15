<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// POST yerine GET kullanımı
$comment_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$comment_id) {
    echo "Hata: comment_id değeri eksik.";
    exit();
}

$user_id = $_SESSION['user_id'];

// Kullanıcının yazıya sahip olup olmadığını kontrol et
$sql = "SELECT posts.user_id AS post_user_id, posts.id AS post_id 
        FROM posts 
        JOIN comments ON posts.id = comments.post_id 
        WHERE comments.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $comment_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows === 0) {
    echo "Hata: " . $conn->error;
    exit();
}

$post = $result->fetch_assoc();

if ($post['post_user_id'] == $user_id) {
    $sql = "UPDATE comments SET is_solution = 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $comment_id);

    if ($stmt->execute()) {
        // Bildirim oluştur
        $sql = "SELECT comments.user_id AS comment_user_id, comments.content AS comment_content, posts.title AS post_title 
                FROM comments 
                JOIN posts ON comments.post_id = posts.id 
                WHERE comments.id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $comment_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if (!$result || $result->num_rows === 0) {
            echo "Hata: " . $conn->error;
            exit();
        }

        $comment = $result->fetch_assoc();

        // Yorumu ekleyen kullanıcının adını al
        $sql = "SELECT username FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $comment['comment_user_id']);
        $stmt->execute();
        $result = $stmt->get_result();

        if (!$result || $result->num_rows === 0) {
            echo "Hata: " . $conn->error;
            exit();
        }

        $commenter = $result->fetch_assoc();

        $notification_sql = "INSERT INTO notifications (user_id, post_id, post_title, type, comment_content, commenter_id, commenter_name) VALUES (?, ?, ?, 'solution', ?, ?, ?)";
        $stmt = $conn->prepare($notification_sql);
        $stmt->bind_param('iissis', $comment['comment_user_id'], $post['post_id'], $comment['post_title'], $comment['comment_content'], $comment['comment_user_id'], $commenter['username']);

        if ($stmt->execute()) {
            header("Location: " . $_SERVER['HTTP_REFERER']);
        } else {
            echo "Hata: " . $conn->error;
        }

    } else {
        echo "Hata: " . $sql . "<br>" . $conn->error;
    }

} else {
    echo "Bu yorumu çözüm olarak işaretleme yetkiniz yok.";
}
?>
