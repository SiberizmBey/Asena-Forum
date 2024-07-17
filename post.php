<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$post_id = $_GET['id'];

// Görüntülenme sayısını artır
$update_views_sql = "UPDATE posts SET views = views + 1 WHERE id = ?";
$stmt = $conn->prepare($update_views_sql);
$stmt->bind_param('i', $post_id);
$stmt->execute();

// Postu ve kullanıcı bilgilerini al
$sql = "SELECT posts.*, users.username, users.role, users.level, categories.name AS category_name 
        FROM posts 
        JOIN users ON posts.user_id = users.id 
        JOIN categories ON posts.category_id = categories.id 
        WHERE posts.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $post_id);
$stmt->execute();
$post_result = $stmt->get_result();
$post = $post_result->fetch_assoc();

// Yorumları al
$sql = "SELECT comments.*, users.username 
        FROM comments 
        JOIN users ON comments.user_id = users.id 
        WHERE comments.post_id = ? 
        ORDER BY created_at ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $post_id);
$stmt->execute();
$comments_result = $stmt->get_result();
$comments = [];
while ($row = $comments_result->fetch_assoc()) {
    $comments[] = $row;
}

// Beğeni sayısını al
$sql = "SELECT COUNT(*) as like_count FROM likes WHERE post_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $post_id);
$stmt->execute();
$like_result = $stmt->get_result();
$like_data = $like_result->fetch_assoc();
$like_count = $like_data['like_count'];

// Kullanıcının gönderiyi beğenip beğenmediğini kontrol et
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$is_liked_by_user = false;

if ($user_id) {
    $sql = "SELECT COUNT(*) as is_liked FROM likes WHERE post_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $post_id, $user_id);
    $stmt->execute();
    $like_check_result = $stmt->get_result();
    $like_check_data = $like_check_result->fetch_assoc();
    $is_liked_by_user = $like_check_data['is_liked'] > 0;
}

// Kullanıcının yorumları beğenip beğenmediğini kontrol et
$comment_likes = [];
if ($user_id) {
    $sql = "SELECT comment_id FROM comment_likes WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $comment_likes[] = $row['comment_id'];
    }
}

// Post içeriğindeki URL'leri tıklanabilir hale getiren fonksiyon
function make_links_clickable($text)
{
    return preg_replace(
        '/(https?:\/\/[^\s<]+)/',
        '<a href="$1" target="_blank" class="link">$1</a>',
        $text
    );
}


function display_comments($comments, $post, $comment_likes, $parent_comment_id = 0, $level = 0)
{
    global $conn; // Bu satır ile $conn değişkenine erişim sağlıyoruz

    foreach ($comments as $comment) {
        if ($comment['parent_comment_id'] == $parent_comment_id) {
            echo '<div class="comment level-' . $level . '">';
            echo '<p><strong>' . htmlspecialchars($comment['username']) . ':</strong> ' . nl2br(htmlspecialchars($comment['content'])) . '</p>';
            if ($comment['is_solution']) {
                echo ' <span class="solution"><i class="fa-solid fa-people-arrows" id="solution"></i> Çözüm</span>';
                if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']) {
                    echo ' <a href="unmark_solution.php?id=' . $comment['id'] . '">Çözümü Geri Al</a>';
                }
            } else {
                if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']) {
                    echo ' <a href="mark_solution.php?id=' . $comment['id'] . '" class="mark-button">Çözüm Olarak İşaretle</a>';
                }
            }

            // Yorumun beğeni sayısını al
            $sql = "SELECT COUNT(*) as like_count FROM comment_likes WHERE comment_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $comment['id']);
            $stmt->execute();
            $like_result = $stmt->get_result();
            $like_data = $like_result->fetch_assoc();
            $comment_like_count = $like_data['like_count'];

            // Yorum beğenisi kontrolü ve gösterimi
            $is_comment_liked_by_user = in_array($comment['id'], $comment_likes);
            echo '<div class="comment-likes">';
            echo '<p class="like-count" data-id="' . $comment['id'] . '" data-type="comment">Beğeni Sayısı: ' . $comment_like_count . '</p>';

            if (isset($_SESSION['user_id'])) {
                echo '<form action="like_comment.php" method="POST" class="like">';
                echo '<input type="hidden" name="comment_id" value="' . $comment['id'] . '">';
                echo '<input type="hidden" name="post_id" value="' . $post['id'] . '">';
                echo '<button type="submit" class="com-button like_but">';
                echo $is_comment_liked_by_user ? '<i class="fa-solid fa-heart"></i>' : '<i class="fa-regular fa-heart"></i>';
                echo '</button>';
                echo '</form>';
            }

            echo '</div>';

            if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $comment['user_id']) {
                echo '<a href="delete_comment.php?id=' . $comment['id'] . '" onclick="return confirm(\'Bu yorumu silmek istediğinize emin misiniz?\');"><button  class="com-button">Yorumu Sil</button></a>';
                echo '<a href="edit_comment.php?id=' . $comment['id'] . '"><button  class="com-button">Yorumu Düzenle</button></a>';
            }
            if (isset($_SESSION['user_id'])) {
                echo '<a href="#" onclick="document.getElementById(\'reply_form_' . $comment['id'] . '\').style.display = \'block\';"><button  class="com-button">Cevapla</button></a>';
                echo '<form id="reply_form_' . $comment['id'] . '" action="add_comment.php" method="post" style="display: none; margin-left: 20px;">';
                echo '<input type="hidden" name="post_id" value="' . $comment['post_id'] . '">';
                echo '<input type="hidden" name="parent_comment_id" value="' . $comment['id'] . '">';
                echo '<input type="hidden" name="parent_comment_username" value="' . $comment['username'] . '">';
                echo '<label for="content">Yorum:</label><br>';
                echo '<textarea id="content" name="content">@' . htmlspecialchars($comment['username']) . ' </textarea><br><br>';
                echo '<input type="submit" class="send-com" value="Yorumu Gönder">';
                echo '</form>';
            }
            display_comments($comments, $post, $comment_likes, $comment['id'], $level + 1);
            echo '</div>';
            if ($parent_comment_id == 0) {
                echo '<hr>';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($post['title']); ?></title>
    <link rel="stylesheet" href="assets/css/style.css?v=1.0.17">
    <link rel="stylesheet" href="assets/css/conversation.css?v=1.0.17">
    <link rel="stylesheet" href="assets/css/post.css?v=1.0.17">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"/>
    <style>
        .liked {
            color: red;
        }

        .level-0 {
            margin-left: 0;
        }

        .level-1 {
            margin-left: 20px;
        }

        .comment-likes {
            margin-top: 10px;
        }

        .like-count {
            cursor: pointer;
            text-decoration: underline;
        }

        #likeModal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.4);
        }

        #likeModalContent {
            background-color: var(--background);
            margin: 15% auto;
            padding: 20px;
            align-items: center;
            border-radius: 20px;
            border: 1px solid rgb(58, 58, 58);
            width: 50%;
        }

        .close {
            color: var(--text-alt-color);
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: var(--main-theme);
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>

<?php include 'public/search_bar.php'; ?>

<div class="content">
    <div class="posts post-content">

        <div class="cat-title">
            <p class="cont"><?php echo htmlspecialchars($post['category_name']); ?></p>
        </div>
        <div class="calendar">
            <?php echo date('d M Y, H:i', strtotime($post['created_at'])); ?>
            <i class="fa-solid fa-ellipsis-vertical" id="solution"></i>
            <i class="fa-regular fa-eye bell" id="views"></i><span class="space"><?php echo $post['views']; ?></span>
            <i class="fa-solid fa-ellipsis-vertical" id="solution"></i>
            <i class="fa-regular fa-heart like-count cont" id="views" data-id="<?php echo $post['id']; ?>"
               data-type="post"></i><span class="space"><?php echo $like_count; ?></span>
        </div>
        <p class="cont"><a href="profile.php?id=<?php echo $post['user_id']; ?>"><?php echo htmlspecialchars($post['username']); ?><span class="solution" id="role"><?php echo htmlspecialchars($post['role']); ?></span> <span class="levels"><?php echo htmlspecialchars($post['level']); ?></span></a></p>

        <h1><?php echo htmlspecialchars($post['title']); ?>
            <?php if (isset($_SESSION['user_id'])): ?>
                <form action="like_post.php" method="POST" class="like">
                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                    <button type="submit" class="com-button">
                        <?php echo $is_liked_by_user ? '<i class="fa-solid fa-heart"></i>' : '<i class="fa-regular fa-heart"></i>'; ?>
                    </button>
                    <span class="solution" id="solution">TAŞINDI</span>
                </form>
            <?php endif; ?>
        </h1>

        <?php echo make_links_clickable($post['content']); ?>

        <br>
        <br>

        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']): ?>
            <a href="delete_post.php?id=<?php echo $post['id']; ?>"
               onclick="return confirm('Bu yazıyı silmek istediğinize emin misiniz?');" class="com-button">Yazıyı
                Sil</a>
            <a href="edit_post.php?id=<?php echo $post['id']; ?>" class="com-button">Yazıyı Düzenle</a>
        <?php endif; ?>
    </div>

    <div class="posts post-content">
        <div class="cont"><i class="fa-regular fa-comments"></i></div>
        <?php if (isset($_SESSION['user_id'])): ?>
            <form action="add_comment.php" method="post">
                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                <input type="hidden" name="parent_comment_id" value="0">
            </form>
        <?php endif; ?>
        <?php display_comments($comments, $post, $comment_likes); ?>
    </div>


    <div class="message-bar">
        <?php if (isset($_SESSION['user_id'])): ?>
            <form action="add_comment.php" method="post">
                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                <textarea  id="content" name="content" rows="4" cols="50" required
                           placeholder="Mesajınızı Buraya Yazın"></textarea><br>
                <button type="submit" class="com-button"><i class="fa-solid fa-paper-plane"></i></button>

            </form>
        <?php else: ?>
            <p>Yorum yapabilmek için <a href="login.php">giriş yapmalısınız</a>.</p>
        <?php endif; ?>
    </div>

</div>
</div>

<?php include 'public/bottom_bar.php'; ?>

<!-- Beğeni Modal -->
<div id="likeModal">
    <div id="likeModalContent">
        <span class="close">&times;</span>
        <h2>Beğenen Kullanıcılar</h2>
        <ul id="likeList">
            <li class="bared"></li>
        </ul>
    </div>
</div>

<script src="assets/js/likes.js"></script>

</body>
</html>