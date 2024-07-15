<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['conversation_id'])) {
    header('Location: view_conversations.php');
    exit();
}

$conversation_id = $_GET['conversation_id'];

// Konuşmanın kullanıcılarını doğrula
$sql = "SELECT user1_id, user2_id FROM conversations WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $conversation_id);
$stmt->execute();
$conversation = $stmt->get_result()->fetch_assoc();

if (!$conversation || ($conversation['user1_id'] != $_SESSION['user_id'] && $conversation['user2_id'] != $_SESSION['user_id'])) {
    header('Location: view_conversations.php');
    exit();
}

// Kullanıcı mesajları gördü olarak işaretle
$sql = "UPDATE messages SET is_read = 1 WHERE conversation_id = ? AND sender_id != ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $conversation_id, $_SESSION['user_id']);
$stmt->execute();

// Mesajları al
$sql = "SELECT m.id, m.sender_id, m.content, m.created_at, m.is_read, u.username AS sender_name 
        FROM messages m
        JOIN users u ON m.sender_id = u.id
        WHERE m.conversation_id = ?
        ORDER BY m.created_at ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $conversation_id);
$stmt->execute();
$messages = $stmt->get_result();

// URL'leri tıklanabilir hale getiren fonksiyon
function make_links_clickable($text)
{
    return preg_replace(
        '/(https?:\/\/[^\s]+)/',
        '<a href="$1" target="_blank" class="link">$1</a>',
        $text
    );
}


// Yeni mesaj gönderme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message_content = trim($_POST['message_content']);
    if (!empty($message_content)) {
        $sender_id = $_SESSION['user_id'];
        $sql = "INSERT INTO messages (conversation_id, sender_id, content, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('iis', $conversation_id, $sender_id, $message_content);
        $stmt->execute();
        header("Location: view_conversation.php?conversation_id=$conversation_id");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Conversation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="assets/css/style.css?v=1.0.17">
    <link rel="stylesheet" href="assets/css/post.css?v=1.0.17">
    <link rel="stylesheet" href="assets/css/conversation.css?v=1.0.17">
    <script src="https://kit.fontawesome.com/b8b432d7d3.js" crossorigin="anonymous"></script>
</head>
<body>
<?php include 'public/search_bar.php'; ?>
<div class="content">
    <div class="posts mes-post">
        <h1>Görüşme</h1>
        <div class="messages">
            <?php while ($message = $messages->fetch_assoc()): ?>
                <div class="message <?php echo $message['sender_id'] == $_SESSION['user_id'] ? 'sent-message' : 'received-message'; ?>">

                    <a href="profile.php?id=<?php echo $message['sender_id']; ?>">
                        <strong><?php echo htmlspecialchars($message['sender_name']); ?>:</strong>
                    </a>
                    <p class="mes_cont"><?php echo make_links_clickable(htmlspecialchars($message['content'])); ?></p>
                    <small style="font-size: 13px"><?php echo $message['created_at']; ?></small>
                    <br>
                    <?php if ($message['sender_id'] == $_SESSION['user_id'] && $message['is_read']): ?>
                        <small style="font-size: 13px; color: var(--main-theme);">Görüldü</small>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
        <button class="com-button"><a href="view_conversations.php">Tüm Görüşmeler</a></button>
    </div>
    <div class="message-bar">
        <form action="" method="post">
            <textarea name="message_content" rows="4" cols="50" required
                      placeholder="Mesajınızı Buraya Yazın"></textarea><br>
            <button type="submit" class="com-button"><i class="fa-solid fa-paper-plane"></i></button>
        </form>
    </div>
</div>
<?php include 'public/bottom_bar.php'; ?>
</body>
</html>
