<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $beta_key = $_POST['beta_key'];
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

    $sql = "SELECT * FROM beta_keys WHERE beta_key = ? AND (user_id IS NULL OR user_id = ?) AND used = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $beta_key, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Beta anahtarını kullanıldı olarak işaretle
        $update_sql = "UPDATE beta_keys SET used = 1, user_id = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param('ii', $user_id, $row['id']);
        $update_stmt->execute();

        $_SESSION['beta_key'] = $beta_key;
        header("Location: index.php");
    } else {
        $error_message = "Geçersiz veya zaten kullanılmış bir beta anahtarı girdiniz.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Beta Anahtar Girişi</title>
</head>
<body>
<h1>Beta Anahtarınızı Girin</h1>
<?php if (isset($error_message)): ?>
    <p><?php echo $error_message; ?></p>
<?php endif; ?>
<form method="post" action="">
    <label for="beta_key">Beta Anahtarı:</label>
    <input type="text" id="beta_key" name="beta_key" required>
    <button type="submit">Gönder</button>
</form>
</body>
</html>
