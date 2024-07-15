<?php
session_start();
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = ? AND role IN ('Admin', 'Moderator')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        header("Location: admin_dashboard.php");
    } else {
        echo "Geçersiz kullanıcı adı veya şifre.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Admin Girişi</title>
</head>
<body>
    <h1>Admin Girişi</h1>
    <form action="admin_login.php" method="post">
        <label for="username">Kullanıcı Adı:</label>
        <input type="text" id="username" name="username" required><br><br>
        
        <label for="password">Şifre:</label>
        <input type="password" id="password" name="password" required><br><br>
        
        <input type="submit" value="Giriş Yap">
    </form>
</body>
</html>
