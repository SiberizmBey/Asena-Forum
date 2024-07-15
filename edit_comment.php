<?php
session_start();
include 'db.php';

$comment_id = $_GET['id'];

// Yorumu çek
$sql = "SELECT * FROM comments WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $comment_id);
$stmt->execute();
$result = $stmt->get_result();
$comment = $result->fetch_assoc();

if (!$comment) {
    die('Yorum bulunamadı.');
}

if ($_SESSION['user_id'] != $comment['user_id']) {
    die('Bu yorumu düzenleme yetkiniz yok.');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $content = $_POST['content'];

    $sql = "UPDATE comments SET content = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $content, $comment_id);
    if ($stmt->execute()) {
        header("Location: post.php?id=" . $comment['post_id']);
    } else {
        echo "Hata: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>Yorumu Düzenle</title>
    <link rel="stylesheet" href="assets/css/style.css?v=1.0.16">
    <link rel="stylesheet" href="assets/css/post.css?v=1.0.16">
    <link rel="stylesheet" href="assets/css/newpost.css?v=1.0.16">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"/>
</head>
<body>

<?php

include 'public/search_bar.php'

?>

<div class="content">

    <div class="posts comment-content">

        <h1>Yorumu Düzenle</h1>
        <form action="edit_comment.php?id=<?php echo $comment_id; ?>" method="post">
            <textarea id="content" name="content" class="post-content"><?php echo $comment['content']; ?></textarea><br><br>
            <input type="submit" class="com-button" value="Kaydet">
        </form>

    </div>
</div>

<?php

include 'public/bottom_bar.php'

?>

</body>
</html>
