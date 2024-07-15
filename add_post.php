<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Kategorileri alma
$sql = "SELECT * FROM categories";
$result = $conn->query($sql);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $category_id = $_POST['category_id'];
    $user_id = $_SESSION['user_id'];

    if (empty($category_id)) {
        echo "Lütfen bir kategori seçin.";
    } else {
        $sql = "INSERT INTO posts (title, content, category_id, user_id) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssii", $title, $content, $category_id, $user_id);
        $stmt->execute();

        // Kullanıcının gönderi sayısını güncelle
        $sql = "UPDATE users SET post_count = post_count + 1 WHERE id = $user_id";
        $conn->query($sql);

        // Kullanıcının yeni seviyesini hesapla
        function calculate_level($post_count) {
            if ($post_count >= 300) {
                return 'Uzman 10';
            } elseif ($post_count < 50) {
                return 'Acemi ' . ceil($post_count / 10);
            } elseif ($post_count < 250) {
                return 'Bilir Kişi ' . ceil(($post_count - 50) / 10);
            } else {
                return 'Uzman ' . ceil(($post_count - 250) / 10);
            }
        }

        // Kullanıcının yeni gönderi sayısını al
        $sql = "SELECT post_count FROM users WHERE id = $user_id";
        $result = $conn->query($sql);
        $user = $result->fetch_assoc();
        $new_level = calculate_level($user['post_count']);

        // Kullanıcının seviyesini güncelle
        $sql = "UPDATE users SET level = '$new_level' WHERE id = $user_id";
        $conn->query($sql);

        header("Location: index.php");
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yeni Gönderi</title>
</head>
<body>
<h1>Yeni Gönderi</h1>
<form action="add_post.php" method="post">
    <label for="title">Başlık:</label>
    <input type="text" id="title" name="title" required><br><br>

    <label for="content">İçerik:</label><br>
    <textarea id="content" name="content" required></textarea><br><br>

    <label for="category">Kategori:</label>
    <select id="category" name="category_id" required>
        <option value="">Kategori Seçin</option>
        <?php while ($category = $result->fetch_assoc()): ?>
            <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
        <?php endwhile; ?>
    </select><br><br>

    <input type="submit" value="Gönderi Ekle">
</form>
</body>
</html>
