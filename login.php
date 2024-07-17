<?php
session_start();
include 'db.php';
require 'vendor/autoload.php';

use Sonata\GoogleAuthenticator\GoogleAuthenticator;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username_or_email = $_POST['username'];
    $password = $_POST['password'];

    // Check if input is an email or username
    if (filter_var($username_or_email, FILTER_VALIDATE_EMAIL)) {
        $sql = "SELECT * FROM users WHERE email = ?";
    } else {
        $sql = "SELECT * FROM users WHERE username = ?";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $username_or_email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];

            if (!empty($user['2fa_secret'])) {
                $_SESSION['2fa_required'] = true;
                $_SESSION['2fa_verified'] = false; // Bu satırı ekleyelim
                header("Location: verify_2fa.php");
                exit();
            } else {
                header("Location: index.php");
                exit();
            }
        } else {
            echo "Hatalı şifre.";
        }
    } else {
        echo "Kullanıcı bulunamadı.";
    }
}

if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['2fa_required']) && $_SESSION['2fa_required'] && !isset($_SESSION['2fa_verified'])) {
        header("Location: verify_2fa.php");
    } else {
        header("Location: index.php");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Giriş Yap</title>
    <link rel="stylesheet" href="assets/css/login.css">
    <script src="https://kit.fontawesome.com/64d58efce2.js" crossorigin="anonymous"></script>
</head>
<body>

<div class="container">
    <div class="forms-container">
        <div class="signin-signup">
            <form action="login.php" method="POST" class="sign-in-form">
                <h2 class="title">Giriş Yap</h2>
                <div class="input-field">
                    <i class="fas fa-user"></i>
                    <input type="text" id="username" name="username" placeholder="Kullanıcı Adı veya E-posta"><br><br>
                </div>
                <div class="input-field">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Şifre"><br><br>
                </div>
                <input type="button" name="showPasswd" value="Şifreyi Göster" class="btn solid" id="togglePassword">
                <input type="submit" value="Giriş Yap" class="btn solid">
            </form>
            <form action="register.php" method="POST" class="sign-up-form">
                <h2 class="title">Kayıt Ol</h2>
                <div class="input-field">
                    <i class="fas fa-user"></i>
                    <input type="text" id="username" name="username" placeholder="Kullanıcı Adı"><br><br>
                </div>
                <div class="input-field">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" id="email" placeholder="E-Mail" required>
                </div>
                <div class="input-field">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Şifre"><br><br>
                </div>
                <input type="button" name="showPasswd" value="Şifreyi Göster" class="btn solid" id="showpass">
                <input type="submit" name="register" value="Kayıt Ol" class="btn solid">
            </form>
        </div>
    </div>
    <div class="panels-container">
        <div class="panel left-panel">
            <div class="content">
                <h3>Yeni misin?</h3>
                <p>Hemen kaydol ve bizimle birlikte ol. Siber felsefesine sende katıl.</p>
                <button class="btn transparent" id="sign-up-btn">Kayıt Ol</button>
            </div>
        </div>
        <div class="panel right-panel">
            <div class="content">
                <h3>Bizden Biri misin?</h3>
                <p>O halde hesabınla hemen giriş yap ve Siber felsefesine kaldığın yerden devam et!</p>
                <button class="btn transparent" id="sign-in-btn">Giriş Yap</button>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/showpassword.js"></script>
<script src="assets/js/logreg.js"></script>
</body>
</html>
