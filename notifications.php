<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Bildirimleri çekme
$sql = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Bildirimler</title>
    <link rel="stylesheet" href="assets/css/style.css?v=1.0.16">
    <link rel="stylesheet" href="assets/css/post.css?v=1.0.16">
    <link rel="stylesheet" href="assets/css/notification.css?v=1.0.16">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"/>
</head>
<body>

<?php include 'public/search_bar.php' ?>

<div class="content">
    <div class="posts">
        <h1>Bildirimler</h1>
        <ul>
            <?php while ($notification = $result->fetch_assoc()): ?>
                <?php
                // Commenter'ın username'ini çekme
                $commenter_id = $notification['commenter_id'];
                $commenter_sql = "SELECT username FROM users WHERE id = ?";
                $commenter_stmt = $conn->prepare($commenter_sql);
                $commenter_stmt->bind_param('i', $commenter_id);
                $commenter_stmt->execute();
                $commenter_result = $commenter_stmt->get_result();
                $commenter = $commenter_result->fetch_assoc();
                $commenter_name = htmlspecialchars($commenter['username']);
                ?>

                <li class="noti">
                    <?php if ($notification['type'] == 'comment'): ?>
                        <a class="change" href="post.php?id=<?php echo $notification['post_id']; ?>">
                            Postunuz <span class="colored"><?= htmlspecialchars($notification['post_title']); ?></span> için yeni bir yorum yapıldı: <span class="colored"><?= htmlspecialchars($notification['comment_content']); ?></span> Yazan: <span class="colored"><?= $commenter_name; ?></span>
                        </a>
                    <?php elseif ($notification['type'] == 'reply'): ?>
                        <a class="change" href="post.php?id=<?php echo $notification['post_id']; ?>">
                            Yorumunuza <span class="colored"><?= htmlspecialchars($notification['post_title']); ?></span> konusu altında bir yanıt verildi: <span class="colored"><?= htmlspecialchars($notification['comment_content']); ?></span> <br></br>Yazan: <span class="colored"><?= $commenter_name; ?></span>
                        </a>
                    <?php elseif ($notification['type'] == 'like'): ?>
                        <a class="change" href="post.php?id=<?php echo $notification['post_id']; ?>">
                            <span class="colored"><?= htmlspecialchars($notification['post_title']); ?></span> postunuz <span class="colored"><?= $commenter_name; ?></span> tarafından beğenildi
                        </a>
                    <?php elseif ($notification['type'] == 'solution'): ?>
                        <a class="change" href="post.php?id=<?php echo $notification['post_id']; ?>">
                            Yorumunuz <span class="colored"><?= htmlspecialchars($notification['comment_content']); ?></span>, <span class="colored"><?= htmlspecialchars($notification['post_title']); ?></span> konusunda çözüm olarak işaretlendi.
                        </a>
                    <?php elseif ($notification['type'] == 'follow'): ?>
                        <a class="change" href="profile.php?id=<?php echo $notification['follower_id']; ?>">
                            <span class="colored"><?= htmlspecialchars($notification['follower_name']); ?></span> sizi takip etmeye başladı.
                        </a>
                    <?php elseif ($notification['type'] == 'like_comment'): ?>
                        <a class="change" href="post.php?id=<?php echo $notification['post_id']; ?>">
                            Yorumunuz "<?= htmlspecialchars($notification['comment_content']); ?>" "<?= $commenter_name; ?>" tarafından beğenildi.
                        </a>
                    <?php endif; ?>
                </li>
            <?php endwhile; ?>
            <a href="index.php" class="com-button">Ana Sayfa</a>
        </ul>
    </div>
</div>

<?php include 'public/bottom_bar.php' ?>
</body>
</html>
