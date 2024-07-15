<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Mevcut konuşmaları son mesaj tarihine göre sıralayın
$sql = "SELECT c.id, u1.username AS user1, u2.username AS user2, u1.id AS user1_id, u2.id AS user2_id,
               (SELECT MAX(m.created_at) FROM messages m WHERE m.conversation_id = c.id) AS last_message_time,
               (SELECT COUNT(*) FROM messages m WHERE m.conversation_id = c.id AND m.sender_id != ? AND m.is_read = 0) AS unread_count,
               (SELECT m.content FROM messages m WHERE m.conversation_id = c.id ORDER BY m.created_at DESC LIMIT 1) AS last_message_content
        FROM conversations c
        JOIN users u1 ON c.user1_id = u1.id
        JOIN users u2 ON c.user2_id = u2.id
        WHERE c.user1_id = ? OR c.user2_id = ?
        ORDER BY last_message_time DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('iii', $user_id, $user_id, $user_id);
$stmt->execute();
$conversations = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Conversations</title>
    <link rel="stylesheet" href="assets/css/style.css?v=1.0.16">
    <style>
        .unread {
            color: var(--main-theme);
        }
        .last-message-preview {
            font-size: 0.9em;
            color: gray;
        }
        .unread-badge {
            font-weight: bold;
            color: red;
        }
    </style>
    <script src="https://kit.fontawesome.com/b8b432d7d3.js" crossorigin="anonymous"></script>
</head>
<body>

<?php include 'public/search_bar.php'; ?>

<div class="content">
    <div class="posts">
        <h1>Görüşmeler</h1>
        <ul>
            <?php while ($conversation = $conversations->fetch_assoc()): ?>
                <a href="view_conversation.php?conversation_id=<?php echo $conversation['id']; ?>">
                    <li class="<?php echo $conversation['unread_count'] > 0 ? 'unread' : ''; ?>">
                        <?php
                        if ($conversation['user1_id'] == $user_id) {
                            echo htmlspecialchars($conversation['user2']);
                        } else {
                            echo htmlspecialchars($conversation['user1']);
                        }
                        ?>
                        <?php if ($conversation['unread_count'] > 0): ?>
                            <span class="unread-badge">( <?php echo $conversation['unread_count']; ?> )</span>
                        <?php endif; ?>
                        <div class="last-message-preview">
                            <?php
                            $words = explode(' ', $conversation['last_message_content']);
                            $preview = implode(' ', array_slice($words, 0, 5));
                            echo htmlspecialchars($preview) . (count($words) > 5 ? '...' : '');
                            ?>
                        </div>
                    </li>
                </a>
            <?php endwhile; ?>
        </ul>
    </div>
</div>

<?php include 'public/bottom_bar.php'; ?>
</body>
</html>
