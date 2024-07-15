<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $post_id = intval($_POST['post_id']);
    $content = $conn->real_escape_string($_POST['content']);
    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'];  // Kullanıcının adını al

    $parent_comment_id = isset($_POST['parent_comment_id']) ? intval($_POST['parent_comment_id']) : null;

    // Yorum ekle
    $sql = "INSERT INTO comments (post_id, user_id, content, parent_comment_id) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }
    $stmt->bind_param("iisi", $post_id, $user_id, $content, $parent_comment_id);
    if (!$stmt->execute()) {
        die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
    }

    $comment_id = $stmt->insert_id;
    $stmt->close();

    // Post sahibini bul
    $sql = "SELECT user_id, title FROM posts WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        die("Post not found.");
    }
    $post = $result->fetch_assoc();
    $post_owner_id = $post['user_id'];
    $post_title = $post['title'];
    $stmt->close();

    // Yorum yapana bildirim gönder
    if ($parent_comment_id) {
        $sql = "SELECT user_id FROM comments WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        $stmt->bind_param("i", $parent_comment_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $parent_comment = $result->fetch_assoc();
            $parent_commenter_id = $parent_comment['user_id'];
            $stmt->close();

            // Bildirim türünü belirle
            $notification_type = 'reply';

            $notification_sql = "INSERT INTO notifications (user_id, post_id, comment_id, comment_content, commenter_id, commenter_name, post_title, type) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($notification_sql);
            if (!$stmt) {
                die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
            }
            $stmt->bind_param("iiisssss", $parent_commenter_id, $post_id, $comment_id, $content, $user_id, $username, $post_title, $notification_type);  // Kullanıcı adını ekle
            if (!$stmt->execute()) {
                die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
            }
            $stmt->close();
        }
    }

    // Post sahibine bildirim gönder
    if ($post_owner_id !== $user_id) {
        // Bildirim türünü belirle
        $notification_type = 'comment';

        $notification_sql = "INSERT INTO notifications (user_id, post_id, comment_id, comment_content, commenter_id, commenter_name, post_title, type) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($notification_sql);
        if (!$stmt) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        $stmt->bind_param("iiisssss", $post_owner_id, $post_id, $comment_id, $content, $user_id, $username, $post_title, $notification_type);  // Kullanıcı adını ekle
        if (!$stmt->execute()) {
            die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        }
        $stmt->close();
    }

    header("Location: post.php?id=$post_id");
    exit();
}
?>
