<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$search_term = isset($_GET['q']) ? $_GET['q'] : '';
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

$user_results = [];
$post_results = [];

if ($search_term) {
    // Kullanıcı arama
    $sql = "SELECT id, username FROM users 
            LEFT JOIN blocks b1 ON users.id = b1.blocked_id AND b1.blocker_id = ?
            LEFT JOIN blocks b2 ON users.id = b2.blocker_id AND b2.blocked_id = ?
            WHERE (b1.blocked_id IS NULL AND b2.blocker_id IS NULL)
            AND username LIKE ?";
    $stmt = $conn->prepare($sql);
    $search_term_wildcard = '%' . $search_term . '%';
    $stmt->bind_param('iis', $user_id, $user_id, $search_term_wildcard);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $user_results[] = $row;
    }

    // Gönderi arama
    $sql = "SELECT id, title FROM posts WHERE title LIKE ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $search_term_wildcard);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $post_results[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Arama Sonuçları</title>
    <link rel="stylesheet" href="assets/css/style.css?v=1.0.16">
    <link rel="stylesheet" href="assets/css/post.css?v=1.0.16">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"/>
</head>
<body>

<?php include 'public/search_bar.php'; ?>

<div class="content">
    <div class="posts">
        <h1>Arama Sonuçları</h1>

        <h2>Kullanıcılar</h2>
        <?php if (!empty($user_results)): ?>
            <ul>
                <?php foreach ($user_results as $user): ?>
                    <li><a href="profile.php?id=<?php echo $user['id']; ?>"><?php echo $user['username']; ?></a></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Kullanıcı bulunamadı.</p>
        <?php endif; ?>
    </div>

    <div class="posts">
        <h2>Gönderiler</h2>
        <?php if (!empty($post_results)): ?>
            <ul>
                <?php foreach ($post_results as $post): ?>
                    <li><a href="post.php?id=<?php echo $post['id']; ?>"><?php echo $post['title']; ?></a></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Gönderi bulunamadı.</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'public/bottom_bar.php'; ?>

</body>
</html>
