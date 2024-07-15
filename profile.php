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
            (SELECT COUNT(*) FROM follows WHERE following_id = ?) AS follower_count,
            (SELECT COUNT(*) FROM follows WHERE follower_id = ?) AS following_count";
$stmt = $conn->prepare($sql);
$stmt->bind_param('iiiii', $user_id, $user_id, $user_id, $user_id, $user_id);
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

// Kullanıcının tüm postlarını çek
$sql = "SELECT id, title FROM posts WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$posts_result = $stmt->get_result();
$all_posts = $posts_result->fetch_all(MYSQLI_ASSOC);

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

// Fetch followers
$sql = "SELECT users.id, users.username FROM follows 
        JOIN users ON follows.follower_id = users.id 
        WHERE follows.following_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$followers_result = $stmt->get_result();
$followers = [];
while ($row = $followers_result->fetch_assoc()) {
    $followers[] = $row;
}

// Fetch following
$sql = "SELECT users.id, users.username FROM follows 
        JOIN users ON follows.following_id = users.id 
        WHERE follows.follower_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$following_result = $stmt->get_result();
$following = [];
while ($row = $following_result->fetch_assoc()) {
    $following[] = $row;
}

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?php echo htmlspecialchars($user['username']); ?> - Profil</title>
    <!-- import font icon (fontawesome) -->
    <script src="https://kit.fontawesome.com/b8b432d7d3.js" crossorigin="anonymous"></script>
    <!-- import css file (style.css) -->
    <link rel="stylesheet" href="assets/css/style.css?v=1.0.16">
    <link rel="stylesheet" href="assets/css/post.css?v=1.0.16">
    <link rel="stylesheet" href="assets/css/profile.css?v=1.0.16">

</head>
<body>

<?php include 'public/search_bar.php'; ?>

<div class="content">

    <div class="posts">
        <div class="profile-header"><!-- profile header section -->
            <div class="main-profile">
                <div class="profile-names">
                    <h1><?php echo htmlspecialchars($user['username']); ?><br><span class="solution" id="role"><?php echo htmlspecialchars($user['level']); ?></span></h1>

                    <div class="profile-actions">
                        <?php if ($user_id == $_SESSION['user_id']): ?>
                            <a href="edit_profile.php"><button class="com-button">Profili Düzenle</button></a>
                            <!-- Kendi profilinizle ilgili özel işlemler -->
                            <form action="logout.php">
                                <button class="com-button">Çıkış Yap</button>
                            </form>
                            <button onclick="document.getElementById('blockedUsers').style.display='block'"
                                    class="com-button">Engellenenler</button>
                        <?php else: ?>
                            <form action="follow.php" method="POST">
                                <input type="hidden" name="follow_id" value="<?php echo $user_id; ?>">
                                <button type="submit"
                                        class="follow com-button"><?php echo $is_following ? 'Takipten Çık' : 'Takip Et'; ?></button>
                            </form>
                            <?php if ($is_blocked): ?>
                                <form action="unblock.php" method="POST">
                                    <input type="hidden" name="blocked_id" value="<?php echo $user_id; ?>">
                                    <button type="submit" class="block com-button">Engeli Kaldır</button>
                                </form>
                            <?php else: ?>
                                <form action="block.php" method="POST">
                                    <input type="hidden" name="blocked_id" value="<?php echo $user_id; ?>">
                                    <button type="submit" class="block com-button">Engelle</button>
                                </form>
                            <?php endif; ?>
                            <!-- Mesaj Gönderme Butonu -->
                            <button onclick="document.getElementById('messageForm').style.display='block'"
                                    class="message com-button">Mesaj Gönder
                            </button>
                        <?php endif; ?>
                    </div>

                </div>
                <div class="other-data">
                    <section class="data-item">
                        <h3 class="value"><?php echo htmlspecialchars($stats['solution_count']); ?> Çözüm</h3>
                    </section>
                    <section class="data-item">
                        <h3 class="value"><?php echo htmlspecialchars($stats['post_count']); ?> Paylaşım</h3>
                    </section>
                    <section class="data-item">
                        <h3 class="value"><?php echo htmlspecialchars($stats['comment_count']); ?> Yorum</h3>
                    </section>

                </div>
            </div>
        </div>

        <div class="profile-body"><!-- profile body section -->

            <div class="account-info">
                <div class="data">
                    <div class="important-data">
                                                <section class="data-item">
                            <h3 class="value"><a href="javascript:void(0);" onclick="document.getElementById('followersPopup').style.display='block'"><?php echo htmlspecialchars($stats['follower_count']); ?> Takipçi</a></h3>
                        </section>
                        <section class="data-item">
                            <h3 class="value"><a href="javascript:void(0);" onclick="document.getElementById('followingPopup').style.display='block'"><?php echo htmlspecialchars($stats['following_count']); ?> Takip Ettiği</a></h3>
                        </section>
                    </div>
                </div>
            </div>

            <div class="posts-body">
                <!-- Kullanıcının tüm postlarını listeleme -->
                <div class="user-posts">
                    <h2><?php echo htmlspecialchars($user['username']); ?>'nın Paylaşımları</h2>
                    <?php if (!empty($all_posts)): ?>
                        <ul>
                            <?php foreach ($all_posts as $post): ?>
                                <li>
                                    <a href="post.php?id=<?php echo htmlspecialchars($post['id']); ?>">
                                        <?php echo htmlspecialchars($post['title']); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>Henüz paylaşım yapılmamış.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pop-up için eklemeler -->
<div id="messageForm" class="popup">
    <div class="popup-content">
        <span class="close" onclick="document.getElementById('messageForm').style.display='none'">&times;</span>
        <form action="message.php" method="POST">
            <input type="hidden" name="recipient_id" value="<?php echo $user_id; ?>">
            <textarea name="message_content" rows="3" placeholder="Mesajınızı buraya yazın..." required></textarea>
            <button type="submit" class="com-button">Gönder</button>
        </form>
    </div>
</div>

<!-- Followers pop-up -->
<div id="followersPopup" class="popup">
    <div class="popup-content">
        <span class="close" onclick="document.getElementById('followersPopup').style.display='none'">&times;</span>
        <h2>Takipçiler</h2>
        <?php if (!empty($followers)): ?>
            <ul>
                <?php foreach ($followers as $follower): ?>
                    <li>
                        <a href="profile.php?id=<?php echo htmlspecialchars($follower['id']); ?>">
                            <?php echo htmlspecialchars($follower['username']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Henüz kimse sizi takip etmiyor.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Following pop-up -->
<div id="followingPopup" class="popup">
    <div class="popup-content">
        <span class="close" onclick="document.getElementById('followingPopup').style.display='none'">&times;</span>
        <h2>Takip Edilenler</h2>
        <?php if (!empty($following)): ?>
            <ul>
                <?php foreach ($following as $followed_user): ?>
                    <li>
                        <a href="profile.php?id=<?php echo htmlspecialchars($followed_user['id']); ?>">
                            <?php echo htmlspecialchars($followed_user['username']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Henüz kimseyi takip etmiyorsunuz.</p>
        <?php endif; ?>
    </div>
</div>


<!-- Engellenen kullanıcılar pop-up'ı -->
<?php if ($user_id == $_SESSION['user_id']): ?>
    <div id="blockedUsers" class="popup">
        <div class="popup-content">
            <span class="close" onclick="document.getElementById('blockedUsers').style.display='none'">&times;</span>
            <h2>Engellenen Kullanıcılar</h2>
            <?php if (!empty($blocked_users)): ?>
                <ul>
                    <?php foreach ($blocked_users as $blocked_user): ?>
                        <li>
                            <?php echo htmlspecialchars($blocked_user['username']); ?>
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                                <input type="hidden" name="unblock_user_id" value="<?php echo htmlspecialchars($blocked_user['id']); ?>">
                                <button type="submit" name="unblock" class="com-button">Engeli Kaldır</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Henüz kimseyi engellemediniz.</p>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<?php include 'public/bottom_bar.php'; ?>

<script>
    // Pop-up açma ve kapama işlemleri
    var messagePopup = document.getElementById('messageForm');
    var blockedUsersPopup = document.getElementById('blockedUsers');

    window.onclick = function(event) {
        if (event.target == messagePopup) {
            messagePopup.style.display = "none";
        }
        if (event.target == blockedUsersPopup) {
            blockedUsersPopup.style.display = "none";
        }
    }
</script>
</body>
</html>
