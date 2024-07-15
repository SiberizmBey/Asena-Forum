<?php
session_start();
include '../db.php';

// Kullanıcının admin veya moderator olduğunu kontrol et
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'Admin' && $_SESSION['role'] != 'Moderator')) {
    header("Location: ../login.php");
    exit();
}

// Gönderi ID'sini al
$post_id = $_GET['id'];

// Gönderiyi getir
$sql = "SELECT * FROM posts WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

// Gönderiyi güncelle
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $content = $_POST['content'];
    
    $sql = "UPDATE posts SET title = ?, content = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $title, $content, $post_id);
    if ($stmt->execute()) {
        header("Location: manage_posts.php");
        exit();
    } else {
        echo "Gönderi güncellenirken bir hata oluştu.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Gönderi Düzenle</title>
</head>
<body>
    <h1>Gönderi Düzenle</h1>
    <form action="edit_post.php?id=<?php echo $post_id; ?>" method="post">
        <label for="title">Başlık:</label><br>
        <input type="text" id="title" name="title" value="<?php echo $post['title']; ?>" required><br><br>
        
        <label for="content">İçerik:</label><br>
        <textarea id="content" name="content" required><?php echo $post['content']; ?></textarea><br><br>
        
        <input type="submit" value="Güncelle">
    </form>
</body>
</html>
