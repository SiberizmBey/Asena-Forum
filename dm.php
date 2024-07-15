<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Direct Messages</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<h1>Direct Messages</h1>

<!-- List of conversations -->
<div class="conversations">
    <h2>Your Conversations</h2>
    <?php
    session_start();
    include 'db.php';

    $user_id = $_SESSION['user_id'];
    $sql = "SELECT * FROM conversations WHERE user1_id = ? OR user2_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $user_id, $user_id);
    $stmt->execute();
    $conversations = $stmt->get_result();
    while ($conversation = $conversations->fetch_assoc()):
        ?>
        <a href="view_conversations.php?id=<?php echo $conversation['id']; ?>">Conversation with User <?php echo $conversation['user1_id'] == $user_id ? $conversation['user2_id'] : $conversation['user1_id']; ?></a>
    <?php endwhile; ?>
</div>

<!-- Start a new conversation -->
<div class="new-conversation">
    <h2>Start a New Conversation</h2>
    <form action="start_conversation.php" method="post">
        <label for="user_id">User ID:</label>
        <input type="text" name="user_id" required>
        <button type="submit">Start Conversation</button>
    </form>
</div>
</body>
</html>
