<?php
include 'check_login.php';
include '../db.php';

// Gönderi silme işlemi
if (isset($_GET['delete'])) {
    $post_id = $_GET['delete'];
    $sql = "DELETE FROM posts WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $post_id);
    if ($stmt->execute()) {
        echo "Gönderi başarıyla silindi.";
    } else {
        echo "Gönderi silinirken bir hata oluştu.";
    }
}

// Gönderileri listele
$sql = "SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Gönderi Yönetimi</title>
</head>
<body>
    <h1>Gönderi Yönetimi</h1>
    <table border="1">
        <tr>
            <th>Başlık</th>
            <th>İçerik</th>
            <th>Yazar</th>
            <th>İşlemler</th>
        </tr>
        <?php while ($post = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $post['title']; ?></td>
            <td><?php echo $post['content']; ?></td>
            <td><?php echo $post['username']; ?></td>
            <td>
                <a href="edit_post.php?id=<?php echo $post['id']; ?>">Düzenle</a>
                <a href="manage_posts.php?delete=<?php echo $post['id']; ?>" onclick="return confirm('Bu gönderiyi silmek istediğinize emin misiniz?');">Sil</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
