<?php
session_start();
include '../db.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'Admin' && $_SESSION['role'] != 'Moderator')) {
    header("Location: admin_login.php");
    exit();
}

$sql_posts = "SELECT posts.*, users.username, categories.name as category_name FROM posts 
              JOIN users ON posts.user_id = users.id 
              JOIN categories ON posts.category_id = categories.id";
$result_posts = $conn->query($sql_posts);

$sql_comments = "SELECT comments.*, users.username FROM comments 
                 JOIN users ON comments.user_id = users.id";
$result_comments = $conn->query($sql_comments);

$sql_users = "SELECT * FROM users";
$result_users = $conn->query($sql_users);

$sql_categories = "SELECT * FROM categories";
$result_categories = $conn->query($sql_categories);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Admin Paneli</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" href="../assets/css/style.css?v=1.0.15">
    <link rel="stylesheet" href="../assets/css/post.css?v=1.0.15">
    <!--
     <style>
         body {
             font-family: Arial, sans-serif;
             margin: 0;
             padding: 0;
             background-color: #f4f4f4;
         }
         .container {
             width: 80%;
             margin: 0 auto;
             background: #fff;
             padding: 20px;
             box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
         }
         h1, h2, h3 {
             text-align: center;
         }
         table {
             width: 100%;
             border-collapse: collapse;
             margin-bottom: 20px;
         }
         table, th, td {
             border: 1px solid #ddd;
         }
         th, td {
             padding: 10px;
             text-align: left;
         }
         th {
             background-color: #f2f2f2;
         }
         .form-container {
             margin: 20px 0;
         }
         .form-container label {
             display: block;
             margin-bottom: 5px;
         }
         .form-container input[type="text"],
         .form-container select {
             width: 100%;
             padding: 8px;
             margin-bottom: 10px;
             border: 1px solid #ccc;
             border-radius: 4px;
         }
         .form-container input[type="submit"] {
             width: 100%;
             padding: 10px;
             background-color: #4CAF50;
             color: white;
             border: none;
             border-radius: 4px;
             cursor: pointer;
         }
         .form-container input[type="submit"]:hover {
             background-color: #45a049;
         }
         .action-links a {
             margin-right: 10px;
         }
     </style>-->
</head>
<body>
<div class="content">
    <h1>Admin Paneli</h1>

    <div class="posts post-content">
        <h2>Gönderiler</h2>
        <table>
            <tr>
                <th>Başlık</th>
                <th>İçerik</th>
                <th>Kategori</th>
                <th>Yazan</th>
                <th>İşlemler</th>
            </tr>
            <?php while ($post = $result_posts->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($post['title']); ?></td>
                    <td><?php echo htmlspecialchars($post['content']); ?></td>
                    <td><?php echo htmlspecialchars($post['category_name']); ?></td>
                    <td><?php echo htmlspecialchars($post['username']); ?></td>
                    <td class="action-links">
                        <a href="edit_post.php?id=<?php echo htmlspecialchars($post['id']); ?>">Düzenle</a>
                        <a href="delete_post.php?id=<?php echo htmlspecialchars($post['id']); ?>"
                           onclick="return confirm('Bu yazıyı silmek istediğinize emin misiniz?');">Sil</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <div class="posts post-content">
        <h2>Yorumlar</h2>
        <table>
            <tr>
                <th>İçerik</th>
                <th>Yazan</th>
                <th>İşlemler</th>
            </tr>
            <?php while ($comment = $result_comments->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($comment['content']); ?></td>
                    <td><?php echo htmlspecialchars($comment['username']); ?></td>
                    <td class="action-links">
                        <a href="delete_comment.php?id=<?php echo htmlspecialchars($comment['id']); ?>"
                           onclick="return confirm('Bu yorumu silmek istediğinize emin misiniz?');">Sil</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <div class="posts post-content">
        <h2>Kullanıcılar</h2>
        <table>
            <tr>
                <th>Kullanıcı Adı</th>
                <th>Rol</th>
                <th>İşlemler</th>
            </tr>
            <?php while ($user = $result_users->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td>
                        <form action="update_role.php" method="post">
                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">
                            <select name="role">
                                <option value="member" <?php if ($user['role'] == 'Uye') echo 'selected'; ?>>Üye
                                </option>
                                <option value="moderator" <?php if ($user['role'] == 'Moderator') echo 'selected'; ?>>
                                    Moderator
                                </option>
                                <option value="admin" <?php if ($user['role'] == 'Admin') echo 'selected'; ?>>Admin
                                </option>
                            </select>
                            <input type="submit" value="Güncelle">
                        </form>
                    </td>
                    <td class="action-links">
                        <a href="delete_user.php?id=<?php echo htmlspecialchars($user['id']); ?>"
                           onclick="return confirm('Bu kullanıcıyı silmek istediğinize emin misiniz?');">Sil</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <div class="posts post-content">
        <h2>Kategoriler</h2>
        <table>
            <tr>
                <th>Kategori Adı</th>
                <th>İşlemler</th>
            </tr>
            <?php while ($category = $result_categories->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($category['name']); ?></td>
                    <td class="action-links">
                        <a href="edit_category.php?id=<?php echo htmlspecialchars($category['id']); ?>">Düzenle</a>
                        <a href="delete_category.php?id=<?php echo htmlspecialchars($category['id']); ?>"
                           onclick="return confirm('Bu kategoriyi silmek istediğinize emin misiniz?');">Sil</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <div class="posts post-content">
        <h3>Kategori Ekle</h3>
        <div class="form-container">
            <form action="add_category.php" method="post">
                <label for="name">Kategori Adı:</label>
                <input type="text" id="name" name="name" required><br><br>
                <input type="submit" value="Kategori Ekle">
            </form>
        </div>
    </div>

</div>


</body>
</html>
