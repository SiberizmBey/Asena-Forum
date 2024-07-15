<?php
session_start();
include 'db.php';

$post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];

// Yazıyı kontrol et ve kullanıcıya ait mi kontrol et
$sql = "SELECT * FROM posts WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $post_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

if (!$post) {
    die('Yazı bulunamadı veya yetkiniz yok.');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];

    $sql = "UPDATE posts SET title = ?, content = ? WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssii', $title, $content, $post_id, $user_id);
    if ($stmt->execute()) {
        header("Location: post.php?id=$post_id");
        exit();
    } else {
        echo "Hata: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yazıyı Düzenle</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
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
        <h1>Yazıyı Düzenle</h1>
        <form action="edit_post.php?id=<?php echo $post_id; ?>" method="post" onsubmit="prepareContent()">
            <input type="text" id="title" class="post-name" name="title" value="<?php echo htmlspecialchars($post['title']); ?>"><br><br>

            <div class="toolbar">
                <button type="button" onclick="formatText('bold')"><i class="fas fa-bold"></i></button>
                <button type="button" onclick="formatText('italic')"><i class="fas fa-italic"></i></button>
                <button type="button" onclick="formatText('underline')"><i class="fas fa-underline"></i></button>
                <button type="button" onclick="formatText('justifyleft')"><i class="fas fa-align-left"></i></button>
                <button type="button" onclick="formatText('justifycenter')"><i class="fas fa-align-center"></i></button>
                <button type="button" onclick="formatText('justifyright')"><i class="fas fa-align-right"></i></button>
                <button type="button" onclick="formatText('insertunorderedlist')"><i class="fas fa-list-ul"></i></button>

            </div>

            <div contenteditable="true" id="content" class="post-content"><?php echo html_entity_decode($post['content']); ?></div>
            <textarea id="hiddenContent" name="content" style="display:none;"></textarea><br><br>

            <input type="submit" class="com-button" value="Güncelle">
        </form>
        <br>
        <a href="post.php?id=<?php echo $post_id; ?>" class="com-button">Geri Dön</a>
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
