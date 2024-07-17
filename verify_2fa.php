<?php
session_start();
include 'db.php';
require 'vendor/autoload.php';

use Sonata\GoogleAuthenticator\GoogleAuthenticator;

if (!isset($_SESSION['2fa_required']) || !$_SESSION['2fa_required']) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$g = new GoogleAuthenticator();

$sql = "SELECT 2fa_secret FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$secret = $user['2fa_secret'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = $_POST['code'];
    if ($g->checkCode($secret, $code)) {
        $_SESSION['2fa_verified'] = true;
        unset($_SESSION['2fa_required']);
        header("Location: index.php");
        exit();
    } else {
        $error = "<script>alert('Girdiğin kod yanlış. Umarım kodunu kaybetmemişsindir')</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>2FA Aktif</title>
    <link rel="stylesheet" href="assets/css/login.css">
    <script src="https://kit.fontawesome.com/64d58efce2.js" crossorigin="anonymous"></script>
</head>
<body>

<div class="container">
    <div class="forms-container">
        <div class="signin-signup">
            <?php if (isset($error)) echo "<p>$error</p>"; ?>
            <form action="verify_2fa.php" method="post" class="sign-in-form">
                <h2 class="title">2FA Açık</h2>
                <div class="input-field">
                    <i class="fas fa-lock"></i>
                    <input type="text" id="code" name="code" placeholder="123456" required><br><br>
                </div>
                <input type="submit" value="Doğrula" class="btn solid">
            </form>
        </div>
    </div>
    <div class="panels-container">
        <div class="panel left-panel">
            <div class="content">
                <h3>2FA Açık</h3>
                <p>Hesabını koruduğunu farkettik. Bu harika bir hareket! Ancak hesabına erişmen için seninde kodunu girmen gerekli</p>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/showpassword.js"></script>
<script src="assets/js/logreg.js"></script>
</body>
</html>
