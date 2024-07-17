<?php
session_start();
include 'db.php';

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $email = $_POST['email'];

    // Profil fotoğrafı yükleme
    $profile_picture = '';
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $target_dir = "uploads/";
        $profile_picture = $target_dir . basename($_FILES["profile_picture"]["name"]);
        move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $profile_picture);
    }

    // Mevcut şifre kontrolü
    $sql = "SELECT password FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!password_verify($current_password, $user['password'])) {
        echo "Mevcut şifre yanlış.";
        exit();
    }

    // Yeni şifre kontrolü
    if (!empty($new_password)) {
        if ($current_password === $new_password) {
            echo "Yeni şifre mevcut şifreyle aynı olmamalı.";
            exit();
        }
        $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
    } else {
        $hashed_new_password = $user['password']; // Şifre değişikliği yoksa mevcut şifre kullanılır
    }

    $sql = "UPDATE users SET username = ?, email = ?, password = ?" . ($profile_picture ? ", profile_picture = ?" : "") . " WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($profile_picture) {
        $stmt->bind_param('ssssi', $username, $email, $hashed_new_password, $profile_picture, $user_id);
    } else {
        $stmt->bind_param('sssi', $username, $email, $hashed_new_password, $user_id);
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Profili Düzenle</title>
    <script src="https://kit.fontawesome.com/b8b432d7d3.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="assets/css/style.css?v=1.0.17">
    <link rel="stylesheet" href="assets/css/conversation.css?v=1.0.17">
    <link rel="stylesheet" href="assets/css/post.css?v=1.0.17">
    <style>
        .form-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .form-container form {
            flex: 1;
            margin: 20px 0;
            width: 100%;
            max-width: 400px;
        }
        @media (min-width: 768px) {
            .form-container {
                flex-direction: row;
            }
            .form-container form {
                margin-right: 20px;
                margin-left: 20px;
            }
        }
    </style>
</head>
<body>

<?php include 'public/search_bar.php'; ?>

<div class="content">
    <div class="posts">

        <h1>Profili Düzenle</h1>
        <div class="form-container">
            <form action="edit_profile.php" method="post" enctype="multipart/form-data">
                <label for="username">Kullanıcı Adı:</label><br>
                <input class="com-button" type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>"><br><br>

                <label for="email">E-Mail:</label><br>
                <input class="com-button" type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" placeholder="örnek@sibermail.com"><br><br>

                <label for="current_password">Mevcut Şifre:</label><br>
                <input class="com-button" type="password" id="current_password" name="current_password" required><br><br>

                <label for="new_password">Yeni Şifre:</label><br>
                <input class="com-button" type="password" id="new_password" name="new_password"><br><br>

                <input class="com-button" type="submit" value="Profili Güncelle">
            </form>

            <?php if (!empty($user['2fa_secret'])): ?>
                <form action="disable_2fa.php" method="post">
                    <h3>2FA'yı Devre Dışı Bırak</h3>
                    <label for="password">Şifre:</label><br>
                    <input class="com-button" type="password" id="password" name="password" required><br><br>
                    <label for="code">2FA Kodu:</label><br>
                    <input class="com-button" type="text" id="code" name="code" required><br><br>
                    <input class="com-button" type="submit" value="2FA'yı Devre Dışı Bırak">
                </form>
            <?php else: ?>
                <a href="setup_2fa.php">
                    <button type="submit" class="com-button">2FA Kur</button>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php include 'public/bottom_bar.php'; ?>
</body>
</html>
