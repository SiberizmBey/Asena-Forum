<?php
include 'db.php';

$title = isset($_GET['title']) ? $_GET['title'] : '';

$sql = "SELECT id, title, content FROM posts WHERE title LIKE ?";
$stmt = $conn->prepare($sql);
$search_term = '%' . $title . '%';
$stmt->bind_param('s', $search_term);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Arama Sonuçları</title>
</head>
<body>
    <h1>Arama Sonuçları</h1>
    <?php if ($result->num_rows > 0): ?>
        <ul>
            <?php while ($row = $result->fetch_assoc()): ?>
                <li><a href="post.php?id=<?php echo $row['id']; ?>"><?php echo $row['title']; ?></a></li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>Sonuç bulunamadı.</p>
    <?php endif; ?>
</body>
</html>
