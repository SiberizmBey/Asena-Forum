<?php
session_start();
include 'db.php';
require 'vendor/autoload.php';

use Sonata\GoogleAuthenticator\GoogleAuthenticator;

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password = $_POST['password'];
    $code = $_POST['code'];

    // Mevcut şifre kontrolü
    $sql = "SELECT password, 2fa_secret FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!password_verify($password, $user['password'])) {
        echo "Yanlış şifre.";
        exit();
    }

    // 2FA kodu kontrolü
    $g = new GoogleAuthenticator();
    if (!$g->checkCode($user['2fa_secret'], $code)) {
        echo "Yanlış 2FA kodu.";
        exit();
    }

    // 2FA'yı devre dışı bırak
    $sql = "UPDATE users SET 2fa_secret = NULL WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();

    $_SESSION['2fa_enabled'] = false;
    header("Location: profile.php");
    exit();
}
?>
