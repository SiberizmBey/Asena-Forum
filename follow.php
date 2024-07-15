<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['follow_id'])) {
    header("Location: login.php");
    exit();
}

$follower_id = $_SESSION['user_id'];
$following_id = $_POST['follow_id'];

// Kullanıcının zaten takip edip etmediğini kontrol et
$sql = "SELECT * FROM follows WHERE follower_id = ? AND following_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $follower_id, $following_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // Takip et
    $sql = "INSERT INTO follows (follower_id, following_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $follower_id, $following_id);
    $stmt->execute();

    // Bildirim oluştur
    $sql = "SELECT username FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $follower_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $follower = $result->fetch_assoc();
        $notification_sql = "INSERT INTO notifications (user_id, type, follower_id, follower_name, post_id) VALUES (?, 'follow', ?, ?, NULL)";
        $stmt = $conn->prepare($notification_sql);
        $stmt->bind_param('iis', $following_id, $follower_id, $follower['username']);
        $stmt->execute();
    }

} else {
    // Takibi bırak
    $sql = "DELETE FROM follows WHERE follower_id = ? AND following_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $follower_id, $following_id);
    $stmt->execute();
}

header("Location: profile.php?id=$following_id");
exit();
?>
