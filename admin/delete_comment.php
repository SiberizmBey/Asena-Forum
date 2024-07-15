<?php
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$comment_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Kullanıcının yoruma sahip olup olmadığını kontrol et
$sql = "SELECT * FROM comments WHERE id = $comment_id AND user_id = $user_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $sql = "DELETE FROM comments WHERE id = $comment_id";
    if ($conn->query($sql) === TRUE) {
        header("Location: " . $_SERVER['HTTP_REFERER']);
    } else {
        echo "Hata: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Bu yorumu silme yetkiniz yok.";
}
?>
