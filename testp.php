<?php
session_start();
include 'db.php';

$user_id = isset($_GET['id']) ? intval($_GET['id']) : $_SESSION['user_id'];

// Kullanıcı bilgilerini çek
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!$user) {
    die('Kullanıcı bulunamadı.');
}

// İstatistikleri çek
$sql = "SELECT 
            (SELECT COUNT(*) FROM posts WHERE user_id = ?) AS post_count,
            (SELECT COUNT(*) FROM comments WHERE user_id = ?) AS comment_count,
            (SELECT COUNT(*) FROM comments WHERE user_id = ? AND is_solution = 1) AS solution_count,
            (SELECT COUNT(*) FROM follows WHERE following_id = ?) AS follower_count";
$stmt = $conn->prepare($sql);
$stmt->bind_param('iiii', $user_id, $user_id, $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stats = $result->fetch_assoc();

// Kullanıcının takip edip etmediğini kontrol et
$is_following = false;
if ($user_id != $_SESSION['user_id']) {
    $sql = "SELECT * FROM follows WHERE follower_id = ? AND following_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $_SESSION['user_id'], $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $is_following = $result->num_rows > 0;
}

// Kullanıcının engelleyip engellemediğini kontrol et
$is_blocked = false;
if ($user_id != $_SESSION['user_id']) {
    $sql = "SELECT * FROM blocks WHERE blocker_id = ? AND blocked_id = ?";
    $stmt->prepare($sql);
    $stmt->bind_param('ii', $_SESSION['user_id'], $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $is_blocked = $result->num_rows > 0;
}

// Kullanıcı tarafından engellenip engellenmediğini kontrol et
$is_blocked_by_user = false;
if ($user_id != $_SESSION['user_id']) {
    $sql = "SELECT * FROM blocks WHERE blocker_id = ? AND blocked_id = ?";
    $stmt->prepare($sql);
    $stmt->bind_param('ii', $user_id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $is_blocked_by_user = $result->num_rows > 0;
}

// Engellenmişse profili gösterme
if ($is_blocked_by_user) {
    die('Bu profil size gösterilemez.');
}

// Kullanıcının son 5 postunu çek
$sql = "SELECT id, title FROM posts WHERE user_id = ? ORDER BY created_at DESC LIMIT 5";
$stmt->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$posts_result = $stmt->get_result();
$last_posts = $posts_result->fetch_all(MYSQLI_ASSOC);

// Engellenen kullanıcıları çek
if ($user_id == $_SESSION['user_id']) {
    $blocked_sql = "SELECT users.id, users.username FROM blocks 
                    JOIN users ON blocks.blocked_id = users.id 
                    WHERE blocks.blocker_id = ?";
    $stmt = $conn->prepare($blocked_sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $blocked_result = $stmt->get_result();
    $blocked_users = [];
    while ($row = $blocked_result->fetch_assoc()) {
        $blocked_users[] = $row;
    }
}

// Engellemeyi kaldırma işlemi
if (isset($_POST['unblock'])) {
    $unblock_user_id = $_POST['unblock_user_id'];
    $unblock_sql = "DELETE FROM blocks WHERE blocker_id = ? AND blocked_id = ?";
    $stmt = $conn->prepare($unblock_sql);
    $stmt->bind_param('ii', $user_id, $unblock_user_id);
    if ($stmt->execute()) {
        header("Location: profile.php");
        exit();
    } else {
        echo "Engellemeyi kaldırma işlemi başarısız: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($user['username']); ?> - Profil</title>
    <!-- import font icon (fontawesome) -->
    <script src="https://kit.fontawesome.com/b8b432d7d3.js" crossorigin="anonymous"></script>
    <!-- import css file (style.css) -->
    <link rel="stylesheet" href="assets/css/style.css?v=1.0.5">
</head>
<body>

<?php include 'public/search_bar.php'; ?>

<div class="content">

    <div class="posts">
        <div class="profile-header"><!-- profile header section -->
            <div class="main-profile">
                <div class="profile-names">
                    <h1><?php echo htmlspecialchars($user['username']); ?> - Profil</h1>
                </div>
            </div>
        </div>

        <div class="profile-body"><!-- profile body section -->
            <div class="profile-actions">
                <?php if ($user_id == $_SESSION['user_id']): ?>
                    <a href="edit_profile.php" class="message">Profili Düzenle</a>
                    <!-- Kendi profilinizle ilgili özel işlemler -->
                <?php else: ?>
                    <h2><?php echo htmlspecialchars($user['username']); ?>'nın Profili</h2>
                    <form action="follow.php" method="POST">
                        <input type="hidden" name="follow_id" value="<?php echo $user_id; ?>">
                        <button type="submit"
                                class="follow"><?php echo $is_following ? 'Takipten Çık' : 'Takip Et'; ?></button>
                    </form>
                    <?php if ($is_blocked): ?>
                        <form action="unblock.php" method="POST">
                            <input type="hidden" name="blocked_id" value="<?php echo $user_id; ?>">
                            <button type="submit" class="block">Engeli Kaldır</button>
                        </form>
                    <?php else: ?>
                        <form action="block.php" method="POST">
                            <input type="hidden" name="blocked_id" value="<?php echo $user_id; ?>">
                            <button type="submit" class="block">Engelle</button>
                        </form>
                    <?php endif; ?>
                    <!-- Mesaj Gönderme Butonu -->
                    <button onclick="document.getElementById('messageForm').style.display='block'" class="message">Mesaj Gönder</button>
                    <!-- Mesaj Gönderme Formu -->
                    <div id="messageForm" style="display:none;">
                        <form action="message.php" method="POST">
                            <input type="hidden" name="recipient_id" value="<?php echo $user_id; ?>">
                            <textarea name="message_content" rows="3" placeholder="Mesajınızı buraya yazın..." required></textarea>
                            <button type="submit">Gönder</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>

            <div class="account-info">
                <div class="data">
                    <div class="important-data">
                        <section class="data-item">
                            <h3 class="value"><?php echo htmlspecialchars($stats['post_count']); ?></h3>
                            <small class="title">Paylaşım</small>
                        </section>
                        <section class="data-item">
                            <h3 class="value"><?php echo htmlspecialchars($stats['follower_count']); ?></h3>
                            <small class="title">Takipçi</small>
                        </section>
                        </section>
                        <section class="data-item">
                            <h3 class="value"><?php echo htmlspecialchars($stats['comment_count']); ?></h3>
                            <small class="title">Yorum</small>
                        </section>
                    </div>
                    <div class="other-data">
                        <section class="data-item">
                            <h3 class="value"><?php echo htmlspecialchars($stats['solution_count']); ?></h3>
                            <small class="title">Çözüm</small>
                        </section>
                        <section class="data-item">
                            <h3 class="value"><?php echo htmlspecialchars($user['level']); ?></h3>
                            <small class="title">Seviye</small>
                        </section>
                        <section class="data-item">
                            <h3 class="value">2K</h3>
                            <small class="title">Takip Ettiği</small>
                        </section>
                    </div>
                </div>

                <form action="logout.php">
                    <button>Çıkış Yap</button>
                </form>
            </div>

            <div class="posts-body">
                <!-- Engellenenler sekmesi -->
                <?php if ($user_id == $_SESSION['user_id']): ?>
                    <div class="blocked-users">
                        <h2>Engellenen Kullanıcılar</h2>
                        <?php if (!empty($blocked_users)): ?>
                            <ul>
                                <?php foreach ($blocked_users as $blocked_user): ?>
                                    <li>
                                        <?php echo htmlspecialchars($blocked_user['username']); ?>
                                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>"
                                              method="POST">
                                            <input type="hidden" name="unblock_user_id"
                                                   value="<?php echo htmlspecialchars($blocked_user['id']); ?>">
                                            <button type="submit" name="unblock" class="com-button">Engeli
                                                Kaldır
                                            </button>
                                        </form>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p>Henüz kimseyi engellemediniz.</p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'public/bottom_bar.php'; ?>
</body>
</html>
