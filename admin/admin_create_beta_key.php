<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../db.php';

// Yalnızca admin kullanıcılarının bu sayfaya erişimine izin ver
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $beta_key = $_POST['beta_key'];
    $user_id = $_POST['user_id'] ? intval($_POST['user_id']) : NULL;

    $sql = "INSERT INTO beta_keys (beta_key, user_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $beta_key, $user_id);
    if ($stmt->execute()) {
        $success_message = "Beta anahtarı başarıyla oluşturuldu.";
    } else {
        $error_message = "Beta anahtarı oluşturulurken bir hata oluştu: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Beta Anahtarı Oluşturma</title>
</head>
<body>
<h1>Beta Anahtarı Oluştur</h1>

<?php if (isset($success_message)): ?>
    <p><?php echo $success_message; ?></p>
<?php endif; ?>
<?php if (isset($error_message)): ?>
    <p><?php echo $error_message; ?></p>
<?php endif; ?>

<form method="post" action="">
    <label for="beta_key">Yeni Beta Anahtarı:</label>
    <input type="text" id="beta_key" name="beta_key" required>
    <label for="user_id">Kullanıcı ID (Opsiyonel):</label>
    <input type="text" id="user_id" name="user_id">
    <button type="submit">Oluştur</button>
</form>
</body>
</html>
