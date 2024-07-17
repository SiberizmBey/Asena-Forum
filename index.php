<?php
session_start();

include 'db.php';

// Kategorileri çekmek için sorgu
$category_sql = "SELECT * FROM categories";
$categories_result = $conn->query($category_sql);
if (!$categories_result) {
    die("Kategori sorgusu başarısız: " . $conn->error);
}

// Filtreleme işlemi
$category_id = isset($_GET['category_id']) ? $_GET['category_id'] : '';
$following = isset($_GET['following']) ? $_GET['following'] : '';

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

// Kullanıcı engelleme kontrolü
$sql = "SELECT posts.*, users.username, 
        (SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.id) as comment_count,
        (SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.id AND comments.is_solution = 1) as solution_count,
        (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) as like_count,
        posts.views
        FROM posts 
        JOIN users ON posts.user_id = users.id
        LEFT JOIN blocks b1 ON posts.user_id = b1.blocked_id AND b1.blocker_id = ?
        LEFT JOIN blocks b2 ON posts.user_id = b2.blocker_id AND b2.blocked_id = ?
        WHERE b1.blocked_id IS NULL AND b2.blocker_id IS NULL";

$conditions = [];

$user_role = $_SESSION['role'];


// Kullanıcı giriş yapmamışsa login sayfasına yönlendir
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Kullanıcı 2FA gerektiriyorsa ve doğrulanmamışsa verify_2fa sayfasına yönlendir
if (isset($_SESSION['2fa_required']) && $_SESSION['2fa_required'] && (!isset($_SESSION['2fa_verified']) || !$_SESSION['2fa_verified'])) {
    header("Location: verify_2fa.php");
    exit();
}

// Kullanıcı bilgilerini çek
$user_sql = "SELECT username, level FROM users WHERE id = ?";
$user_stmt = $conn->prepare($user_sql);
$user_stmt->bind_param('i', $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();

if ($user_result->num_rows > 0) {
    $user = $user_result->fetch_assoc();
} else {
    die("Kullanıcı bilgileri alınamadı.");
}


if ($category_id) {
    if ($category_id == 'following') {
        if ($user_id) {
            $following_sql = "SELECT following_id FROM follows WHERE follower_id = ?";
            $following_stmt = $conn->prepare($following_sql);
            $following_stmt->bind_param('i', $user_id);
            $following_stmt->execute();
            $following_result = $following_stmt->get_result();

            if (!$following_result) {
                die("Takip sorgusu başarısız: " . $conn->error);
            }

            $following_ids = [];
            while ($row = $following_result->fetch_assoc()) {
                $following_ids[] = $row['following_id'];
            }

            if (count($following_ids) > 0) {
                $conditions[] = "posts.user_id IN (" . implode(',', array_map('intval', $following_ids)) . ")";
            } else {
                $conditions[] = "1 = 0";
            }
        } else {
            $conditions[] = "1 = 0";
        }
    } else {
        $conditions[] = "posts.category_id = " . intval($category_id);
    }
}

if (count($conditions) > 0) {
    $sql .= " AND " . implode(' AND ', $conditions);
}

$sql .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Post sorgusu başarısız: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Asena Forum - Siber Felsefesi</title>
    <link rel="stylesheet" href="assets/css/style.css?v=1.0.17">
    <link rel="shortcut icon" href="assets/img/favicon.png?v=1.0.0" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"/>

    <meta name="keywords" content="forum, asena, asena forum, members, samsung members, galaxy, siber, siber güvenlik, asena form, form, members rakibi, özgür forum">
    <meta name="description" content="Tamamen özgür forum sitesi!!">
    <meta name="author" content="Asena Space">

    <meta property="og:title" content="Asena Forum - Siber Felsefesi">
    <meta property="og:type" content="website">
    <meta property="og:description" content="Asena, özgür forum sitesi!">
    <meta property="og:url" content="https://asena.space/">
    <meta property="og:image" content="https://asena.space/assets/img/favicon.png">
    <meta property="og:site_name" content="Asena">

    <meta name="theme-color" content="#101013">

</head>
<body>

<script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('./service-worker.js')
            .then(function(registration) {
                console.log('Service Worker registered with scope:', registration.scope);
            }).catch(function(error) {
            console.log('Service Worker registration failed:', error);
        });
    }
</script>

<?php include 'public/search_bar.php'; ?>

<div class="other-cont">

    <div class="content">
        <h1 class="main-title">Asena Forum</h1>

        <div class="user-info" id="user-info">
            <a href="profile.php"><h1><?php echo htmlspecialchars($user['username']); ?><span class="role" id="role"><?php echo htmlspecialchars($user['level']); ?></span><?php echo htmlspecialchars($user['role']); ?></h1></a>
        </div>

        <!-- Kategori Filtre Formu -->
        <form action="" method="get" class="filter_system">
            <select name="category_id">
                <option value="">Tüm Kategoriler</option>
                <option value="following" <?php echo $category_id == 'following' ? 'selected' : ''; ?>>Takip Ettiklerim</option>
                <?php while ($category = $categories_result->fetch_assoc()): ?>
                    <option value="<?php echo $category['id']; ?>" <?php echo $category_id == $category['id'] ? 'selected' : ''; ?>>
                        <?php echo $category['name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button type="submit">Filtre Uygula</button>
        </form>

        <?php while ($row = $result->fetch_assoc()): ?>
            <a class="posts" href="post.php?id=<?php echo $row['id']; ?>">

                <p class="post_author"><?php echo htmlspecialchars($row['username']); ?></p>

                <h2 class="post_title"><?php echo htmlspecialchars($row['title']); ?></h2>
                <p class="post_content">
                    <?php echo htmlspecialchars(substr(strip_tags($row['content']), 0, 50)); ?>...
                </p>

                <p class="post_stats">
                    <i class="fa-regular fa-comments bell" id="like"></i> <span class="space"><?php echo $row['comment_count']; ?></span>
                    <i class="fa-solid fa-people-arrows bell" id="solution"></i> <span class="space"><?php echo $row['solution_count']; ?></span>
                    <i class="fa-regular fa-thumbs-up bell" id="solution"></i> <span class="space"><?php echo $row['like_count']; ?></span>
                    <i class="fa-regular fa-eye bell" id="solution"></i> <span class="space"><?php echo $row['views']; ?></span>
                </p>
            </a>
            <hr>
        <?php endwhile; ?>
    </div>

    <?php include 'public/bottom_bar.php'; ?>
</div>

<script>
    window.addEventListener('scroll', function() {
        const userInfo = document.getElementById('user-info');
        if (window.scrollY > 50) {
            userInfo.classList.add('fixed');
        } else {
            userInfo.classList.remove('fixed');
        }
    });
</script>


</body>
</html>
