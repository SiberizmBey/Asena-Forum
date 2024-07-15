<?php
include 'check_login.php';
include '../db.php';

// Yorum silme işlemi
if (isset($_GET['delete'])) {
    $comment_id = $_GET['delete'];
    $sql = "DELETE FROM comments WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $comment_id);
    if ($stmt->execute()) {
        echo "Yorum başarıyla silindi.";
    } else {
        echo "Yorum silinirken bir hata oluştu.";
    }
}

// Yorumları listele
$sql = "SELECT comments.*, users.username, posts.title FROM comments 
        JOIN users ON comments.user_id = users.id 
        JOIN posts ON comments.post_id = posts.id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yorum Yönetimi</title>
</head>
<body>
    <h1>Yorum Yönetimi</h1>
    <table border="1">
        <tr>
            <th>Gönderi Başlığı</th>
            <th>Yorum</th>
            <th>Yazan</th>
            <th>İşlemler</th>
        </tr>
        <?php while ($comment = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $comment['title']; ?></td>
            <td><?php echo $comment['content']; ?></td>
            <td><?php echo $comment['username']; ?></td>
            <td>
                <a href="edit_comment.php?id=<?php echo $comment['id']; ?>">Düzenle</a>
                <a href="manage_comments.php?delete=<?php echo $comment['id']; ?>" onclick="return confirm('Bu yorumu silmek istediğinize emin misiniz?');">Sil</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
