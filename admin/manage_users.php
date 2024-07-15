<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Profil fotoğrafı yükleme
    $profile_picture = 'uploads/default.png'; // Varsayılan profil fotoğrafı
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
        
        // Dosya yolunu ve izinlerini kontrol et
        if (is_writable($target_dir)) {
            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                $profile_picture = $target_file;
                echo "Profil fotoğrafı başarıyla yüklendi: " . $profile_picture . "<br>";
            } else {
                echo "Profil fotoğrafı yüklenemedi. Dosya taşıma hatası.<br>";
            }
        } else {
            echo "Yazma izni hatası: 'uploads' dizini yazılabilir değil.<br>";
        }
    } else {
        // Dosya yükleme hatalarını kontrol et
        if ($_FILES['profile_picture']['error'] != 0) {
            switch ($_FILES['profile_picture']['error']) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    echo "Dosya boyutu çok büyük.<br>";
                    break;
                case UPLOAD_ERR_PARTIAL:
                    echo "Dosya yalnızca kısmen yüklendi.<br>";
                    break;
                case UPLOAD_ERR_NO_FILE:
                    echo "Dosya yüklenmedi.<br>";
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    echo "Geçici klasör eksik.<br>";
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    echo "Diske yazma hatası.<br>";
                    break;
                case UPLOAD_ERR_EXTENSION:
                    echo "Bir PHP uzantısı dosya yüklemesini durdurdu.<br>";
                    break;
                default:
                    echo "Bilinmeyen bir hata oluştu.<br>";
                    break;
            }
        }
    }

    // Veritabanı kaydını kontrol et
    $sql = "INSERT INTO users (username, password, profile_picture) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die('Prepare hatası: ' . htmlspecialchars($conn->error) . "<br>");
    }

    $stmt->bind_param('sss', $username, $password, $profile_picture);
    if ($stmt->execute() === TRUE) {
        echo "Veritabanına başarıyla kaydedildi.<br>";
        header("Location: login.php");
        exit();
    } else {
        echo "Veritabanı kaydetme hatası: " . htmlspecialchars($stmt->error) . "<br>";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kayıt Ol</title>
</head>
<body>
    <h1>Kayıt Ol</h1>
    <form action="register.php" method="post" enctype="multipart/form-data">
        <label for="username">Kullanıcı Adı:</label><br>
        <input type="text" id="username" name="username"><br><br>
        <label for="password">Şifre:</label><br>
        <input type="password" id="password" name="password"><br><br>
        <label for="profile_picture">Profil Fotoğrafı:</label><br>
        <input type="file" id="profile_picture" name="profile_picture"><br><br>
        <input type="submit" value="Kayıt Ol">
    </form>
</body>
</html>
