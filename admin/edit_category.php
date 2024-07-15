<?php
session_start();
include '../db.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'Admin' && $_SESSION['role'] != 'Moderator')) {
    header("Location: admin_login.php");
    exit();
}

$category_id = $_GET['id'];

$sql = "SELECT * FROM categories WHERE id = $category_id";
$result = $conn->query($sql);
$category = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    
    $sql = "UPDATE categories SET name = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $name, $category_id);
    if ($stmt->execute()) {
        header("Location: admin_dashboard.php");
    } else {
        echo "Kategori güncellerken hata oluştu.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kategori Düzenle</title>
</head>
<body>
    <h1>Kategori Düzenle</h1>
    <form action="edit_category.php?id=<?php echo $category_id; ?>" method="post">
        <label for="name">Kategori Adı:</label>
        <input type="text" id="name" name="name" value="<?php echo $category['name']; ?>" required><br><br>
        <input type="submit" value="Kategori Güncelle">
    </form>
</body>
</html>
