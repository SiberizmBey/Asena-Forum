<?php
session_start();
include 'db.php';
require 'vendor/autoload.php';

use Sonata\GoogleAuthenticator\GoogleAuthenticator;
use Sonata\GoogleAuthenticator\GoogleQrUrl;

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$g = new GoogleAuthenticator();

// Kullanıcının mevcut 2FA durumunu kontrol edin
$sql = "SELECT 2fa_secret FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($existingSecret);
$stmt->fetch();
$stmt->close();

if (!empty($existingSecret)) {
    header("Location: index.php");
} else {
    // Kullanıcı için gizli anahtar oluşturun
    if (!isset($_SESSION['2fa_secret'])) {
        $secret = $g->generateSecret();
        $_SESSION['2fa_secret'] = $secret;
    } else {
        $secret = $_SESSION['2fa_secret'];
    }

    // QR kod URL'si oluşturun
    $qrCodeUrl = GoogleQrUrl::generate('Asena Üyesi', $secret, 'Asena Forum');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Kullanıcıdan alınan kodu doğrulayın
        $code = $_POST['code'];
        if ($g->checkCode($secret, $code)) {
            // Başarılı doğrulama, gizli anahtarı veritabanında saklayın
            $sql = "UPDATE users SET 2fa_secret = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('si', $secret, $user_id);
            $stmt->execute();

            $_SESSION['2fa_enabled'] = true;
            unset($_SESSION['2fa_secret']); // Gizli anahtarı oturumdan kaldır
            header("Location: profile.php");
            exit();
        } else {
            $error = "<script>alert('Geçersiz doğrulama kodu.')</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Giriş Yap</title>
    <link rel="stylesheet" href="assets/css/2fa.css">
    <script src="https://kit.fontawesome.com/64d58efce2.js" crossorigin="anonymous"></script>
</head>
<body>

<!--    <a href="register.php"><input type="submit" value="Kayıt Ol"></a>-->

<div class="container">
    <div class="forms-container">
        <div class="signin-signup">

            <?php if (isset($error)) echo "<p>$error</p>"; ?>

            <form action="setup_2fa.php" method="post" class="sign-in-form">
                <h2 class="title">2FA Kurulumu</h2>

                <div class="input-field">
                    <i class="fas fa-lock"></i>
                    <input type="text" id="code" name="code" placeholder="123456" required><br><br>
                </div>
                <input type="submit" value="Kur" class="btn solid">
            </form>

        </div>
    </div>
    <div class="panels-container">
        <div class="panel left-panel">
            <div class="content">
                <h3>2FA Kurulumu</h3>
                <p>Hesabını koruma altına almaya çalıştığını farkettik. Lütfen aşağıdaki kodu herhangi bir 2FA uygulamasına okut. Biz sana ENTE Auth'u öneririz</p>
                <img src="<?php echo $qrCodeUrl; ?>" alt="QR Code">
                <p>Eğer QR kodunu tarayamıyorsanız, aşağıdaki anahtarı kullanarak manuel olarak ekleyebilirsiniz:</p>
                <p><strong>Gizli Anahtar:</strong> <?php echo $secret; ?></p>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/showpassword.js"></script>
<script src="assets/js/logreg.js"></script>
</body>
</html>
