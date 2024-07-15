<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content']; // Doğrudan gönderilen içeriği al
    $category_id = $_POST['category_id'];
    $user_id = $_SESSION['user_id'];

    $sql = "INSERT INTO posts (title, content, category_id, user_id) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $title, $content, $category_id, $user_id);
    if ($stmt->execute()) {
        header("Location: index.php");
    } else {
        echo "Gönderi eklerken hata oluştu.";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Yeni Gönderi Ekle</title>
    <link rel="stylesheet" href="assets/css/style.css?v=1.0.16">
    <link rel="stylesheet" href="assets/css/post.css?v=1.0.16">
    <link rel="stylesheet" href="assets/css/newpost.css?v=1.0.16">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"/>
    <style>
        .toolbar {
            padding: 10px;
            margin-bottom: 10px;
        }

        .toolbar button {
            background: none;
            border: none;
            border-radius: 5px;
            margin: 0 2px;
            cursor: pointer;
            font-size: 16px;
            padding: 10px;
        }

        .toolbar button:hover {
            background-color: var(--main-theme);

        }

        .post-content {
            width: 100%;
            height: 300px;
            border: 1px solid #ccc;
            padding: 10px;
            overflow-y: auto;
        }
    </style>
</head>
<body>

<?php include 'public/search_bar.php'; ?>

<div class="content">
    <div class="posts">
        <h1>Yeni Gönderi Ekle</h1>
        <form action="add_post.php" method="post" onsubmit="prepareContent()">
            <input type="text" class="post-name" id="title" name="title" placeholder="Başlık" required><br><br>

            <div class="toolbar">
                <button type="button" onclick="formatText('bold')"><i class="fas fa-bold"></i></button>
                <button type="button" onclick="formatText('italic')"><i class="fas fa-italic"></i></button>
                <button type="button" onclick="formatText('underline')"><i class="fas fa-underline"></i></button>
                <button type="button" onclick="formatText('justifyleft')"><i class="fas fa-align-left"></i></button>
                <button type="button" onclick="formatText('justifycenter')"><i class="fas fa-align-center"></i></button>
                <button type="button" onclick="formatText('justifyright')"><i class="fas fa-align-right"></i></button>
                <button type="button" onclick="formatText('insertunorderedlist')"><i class="fas fa-list-ul"></i></button>

            </div>

            <div contenteditable="true" id="content" class="post-content"></div>
            <textarea id="hiddenContent" name="content" style="display:none;"></textarea>
            <br><br>

            <select id="category_id" name="category_id" class="filter_system" required>
                <option value="">Kategori Seçin</option>
                <?php
                $sql = "SELECT * FROM categories";
                $result = $conn->query($sql);
                while ($category = $result->fetch_assoc()) {
                    echo '<option value="' . $category['id'] . '">' . $category['name'] . '</option>';
                }
                ?>
            </select><br><br>
            <input type="submit" class="com-button" value="Gönderi Ekle">
        </form>
    </div>
</div>

<?php include 'public/bottom_bar.php'; ?>

<script>
    function formatText(command) {
        document.execCommand(command, false, null);
    }

    function createLink() {
        var url = prompt("Bağlantı URL'si girin:", "http://");
        if (url) {
            document.execCommand('createLink', false, url);
        }
    }

    function insertImage() {
        var url = prompt("Resim URL'si girin:", "http://");
        if (url) {
            document.execCommand('insertImage', false, url);
        }
    }

    function prepareContent() {
        document.getElementById('hiddenContent').value = document.getElementById('content').innerHTML;
    }
</script>

</body>
</html>
