<?php
include 'db.php';

$id = $_GET['id'];
$type = $_GET['type'];

if ($type == 'post') {
    $sql = "SELECT users.username FROM likes 
            JOIN users ON likes.user_id = users.id 
            WHERE likes.post_id = ?";
} else if ($type == 'comment') {
    $sql = "SELECT users.username FROM comment_likes 
            JOIN users ON comment_likes.user_id = users.id 
            WHERE comment_likes.comment_id = ?";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

$likes = [];
while ($row = $result->fetch_assoc()) {
    $likes[] = $row;
}

echo json_encode(['likes' => $likes]);
?>
