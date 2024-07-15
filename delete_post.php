<?php
session_start();
include 'db.php';

// Hata ayıklama için hata raporlamayı aç
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Kullanıcı giriş yapmamışsa login sayfasına yönlendir
if (!isset($_SESSION['user_id'])) {
    die('Giriş yapılmamış.');
}

$post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($post_id === 0) {
    die('Geçersiz yazı ID.');
}

// Yazıyı veritabanından al
$sql = "SELECT * FROM posts WHERE id = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die('Prepare Error (Post Select): ' . $conn->error);
}
$stmt->bind_param('i', $post_id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

if (!$post) {
    die('Yazı bulunamadı.');
}

// Kullanıcı yazının sahibi mi kontrol et
if ($_SESSION['user_id'] != $post['user_id']) {
    die('Bu yazıyı silme yetkiniz yok.');
}

// Yazıya ait yorumları sil
$sql = "DELETE FROM comments WHERE post_id = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die('Prepare Error (Comment Delete): ' . $conn->error);
}
$stmt->bind_param('i', $post_id);
if ($stmt->execute() === false) {
    die('Comment Delete Error: ' . $stmt->error);
}

// Yazıyı sil
$sql = "DELETE FROM posts WHERE id = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die('Prepare Error (Post Delete): ' . $conn->error);
}
$stmt->bind_param('i', $post_id);
if ($stmt->execute()) {
    echo 'Yazı ve yorumlar başarıyla silindi.';
    header('Location: index.php');
    exit();
} else {
    die('Post Delete Error: ' . $stmt->error);
}
?>
