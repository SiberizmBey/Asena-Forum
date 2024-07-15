<?php
session_start();
include '../db.php';

// Kullanıcının admin veya moderator olduğunu kontrol et
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'Admin' && $_SESSION['role'] != 'Moderator')) {
    header("Location: ../login.php");
    exit();
}

// Yorum ID'sini al
$comment_id = $_GET['id'];

// Yorumu getir
$sql = "SELECT * FROM comments WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $comment_id);
$stmt->execute();
$result = $stmt->get_result();
$comment = $result->fetch_assoc();

// Yorumu güncelle
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $content = $_POST['content'];
    
    $sql = "UPDATE comments SET content = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $content, $comment_id);
    if ($stmt->execute()) {
        header("Location: manage_comments.php");
        exit();
    } else {
        echo "Yorum güncellenirken bir hata oluştu.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yorum Düzenle</title>
</head>
<body>
    <h1>Yorum Düzenle</h1>
    <form action="edit_comment.php?id=<?php echo $comment_id; ?>" method="post">
        <label for="content">İçerik:</label><br>
        <textarea id="content" name="content" required><?php echo $comment['content']; ?></textarea><br><br>
        
        <input type="submit" value="Güncelle">
    </form>
</body>
</html>
