<?php
session_start();
include 'db.php';

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];

    // Profil fotoğrafı yükleme
    $profile_picture = '';
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $target_dir = "uploads/";
        $profile_picture = $target_dir . basename($_FILES["profile_picture"]["name"]);
        move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $profile_picture);
    }

    $sql = "UPDATE users SET username = ?" . ($profile_picture ? ", profile_picture = ?" : "") . " WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($profile_picture) {
        $stmt->bind_param('ssi', $username, $profile_picture, $user_id);
    } else {
        $stmt->bind_param('si', $username, $user_id);
    }
    $stmt->execute();

    header("Location: profile.php");
    exit();
}

$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Profili Düzenle</title>
</head>
<body>
    <h1>Profili Düzenle</h1>
    <form action="edit_profile.php" method="post" enctype="multipart/form-data">
        <label for="username">Kullanıcı Adı:</label><br>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>"><br><br>
        <label for="profile_picture">Profil Fotoğrafı:</label><br>
        <input type="file" id="profile_picture" name="profile_picture"><br><br>
        <input type="submit" value="Profili Güncelle">
    </form>
</body>
</html>
