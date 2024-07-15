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
        
        // Hedef dosya yolunun doğruluğunu kontrol edin
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            $profile_picture = $target_file;
        } else {
            echo "Profil fotoğrafı yüklenemedi.";
        }
    } else {
        // Dosya yükleme hatalarını kontrol et
        if ($_FILES['profile_picture']['error'] != 0) {
            switch ($_FILES['profile_picture']['error']) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    echo "Dosya boyutu çok büyük.";
                    break;
                case UPLOAD_ERR_PARTIAL:
                    echo "Dosya yalnızca kısmen yüklendi.";
                    break;
                case UPLOAD_ERR_NO_FILE:
                    echo "Dosya yüklenmedi.";
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    echo "Geçici klasör eksik.";
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    echo "Diske yazma hatası.";
                    break;
                case UPLOAD_ERR_EXTENSION:
                    echo "Bir PHP uzantısı dosya yüklemesini durdurdu.";
                    break;
                default:
                    echo "Bilinmeyen bir hata oluştu.";
                    break;
            }
        }
    }

    $sql = "INSERT INTO users (username, password, profile_picture) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sss', $username, $password, $profile_picture);
    if ($stmt->execute() === TRUE) {
        header("Location: login.php");
        exit();
    } else {
        echo "Hata: " . $sql . "<br>" . $conn->error;
    }
}
?>
