<?php
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$post_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Kullanıcının yazıya sahip olup olmadığını kontrol et
$sql = "SELECT * FROM posts WHERE id = $post_id AND user_id = $user_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Yazıyı ve ilgili yorumları sil
    $sql = "DELETE FROM comments WHERE post_id = $post_id";
    $conn->query($sql);
    
    $sql = "DELETE FROM posts WHERE id = $post_id";
    if ($conn->query($sql) === TRUE) {
        header("Location: index.php");
    } else {
        echo "Hata: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Bu yazıyı silme yetkiniz yok.";
}
?>
