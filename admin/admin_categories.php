<?php
session_start();
include '../db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Admin') {
    header("Location: index.php");
    exit();
}

// Kategori ekleme
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $name = $_POST['name'];
    $sql = "INSERT INTO categories (name) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $name);
    $stmt->execute();
}

// Kategori silme
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $sql = "DELETE FROM categories WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

// Kategorileri alma
$sql = "SELECT * FROM categories";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kategori Yönetimi</title>
</head>
<body>
    <h1>Kategori Yönetimi</h1>
    <form action="admin_categories.php" method="post">
        <label for="name">Kategori Adı:</label>
        <input type="text" id="name" name="name" required>
        <input type="submit" name="add_category" value="Kategori Ekle">
    </form>
    
    <h2>Mevcut Kategoriler</h2>
    <ul>
        <?php while ($category = $result->fetch_assoc()): ?>
            <li>
                <?php echo $category['name']; ?>
                <a href="admin_categories.php?delete_id=<?php echo $category['id']; ?>" onclick="return confirm('Bu kategoriyi silmek istediğinize emin misiniz?');">Sil</a>
            </li>
        <?php endwhile; ?>
    </ul>
</body>
</html>
