<?php
session_start();
include '../db.php';

// Kullanıcının admin olduğunu kontrol et
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    header("Location: ../login.php");
    exit();
}

// Kullanıcı ID'sini al
$user_id = $_GET['id'];

// Kullanıcıyı getir
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Kullanıcıyı güncelle
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $role = $_POST['role'];
    
    $sql = "UPDATE users SET username = ?, role = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $username, $role, $user_id);
    if ($stmt->execute()) {
        header("Location: manage_users.php");
        exit();
    } else {
        echo "Kullanıcı güncellenirken bir hata oluştu.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kullanıcı Düzenle</title>
</head>
<body>
    <h1>Kullanıcı Düzenle</h1>
    <form action="edit_user.php?id=<?php echo $user_id; ?>" method="post">
        <label for="username">Kullanıcı Adı:</label><br>
        <input type="text" id="username" name="username" value="<?php echo $user['username']; ?>" required><br><br>
        
        <label for="role">Rol:</label><br>
        <select id="role" name="role" required>
            <option value="admin" <?php if ($user['role'] == 'admin') echo 'selected'; ?>>Admin</option>
            <option value="moderator" <?php if ($user['role'] == 'moderator') echo 'selected'; ?>>Moderator</option>
            <option value="member" <?php if ($user['role'] == 'member') echo 'selected'; ?>>Üye</option>
        </select><br><br>
        
        <input type="submit" value="Güncelle">
    </form>
</body>
</html>
