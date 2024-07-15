<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            header("Location: index.php");
        } else {
            echo "Hatalı şifre.";
        }
    } else {
        echo "Kullanıcı bulunamadı.";
    }
}

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
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

<!--    <a href="register.php"><input type="submit" value="Kayıt Ol"></a>-->


    <div class="container">
        <div class="forms-container">
            <div class="signin-signup">

                <form action="login.php" method="POST" class="sign-in-form">

                    <h2 class="title">Giriş Yap</h2>

                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <input type="text" id="username" name="username" placeholder="Kullanıcı Adı"><br><br>
                    </div>

                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" placeholder="Şifre"><br><br>
                    </div>

                    <input type="button" name="showPasswd" value="Şifreyi Göster" class="btn solid" id="togglePassword">

                    <input type="submit" value="Giriş Yap"  class="btn solid">

                </form>

                <form action="register.php" method="POST" class="sign-up-form">

                    <h2 class="title">Kayıt Ol</h2>

                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <input type="text" id="username" name="username" placeholder="Kullanıcı Adı" disabled><br><br>
                    </div>

                    <div class="input-field">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" id="email" placeholder="E-Mail (Opsiyonel)" disabled>
                    </div>

                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" placeholder="Şifre" disabled><br><br>
                    </div>

                    <input type="button" name="showPasswd" value="Şifreyi Göster" class="btn solid" id="showpass" disabled>

                    <input type="submit" name="register" value="Kayıt Ol" class="btn solid" disabled>
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
                    <h3>Eyoooo!!!</h3>
                    <p>Görüyoruz ki çok acelecisin. Ancak seni bekletmek zorundayız. Asena Forum henüz geliştirici önzileme testindedir. Bu süre zarfınca davetli değilseniz kayıt olamaz ve giriş yapamazsınız. Davetliyseniz size verilen kullancı adı ve şifre ile giriş yapabilirsiniz. İlginiz için teşekkür ederiz...</p>
                    <button class="btn transparent" id="sign-in-btn">Giriş Yap</button>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/showpassword.js"></script>
    <script src="assets/js/logreg.js"></script>
</body>
</html>