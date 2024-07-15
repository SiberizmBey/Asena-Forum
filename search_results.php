<?php
include 'db.php';

$username = isset($_GET['username']) ? $_GET['username'] : '';

$sql = "SELECT id, username FROM users WHERE username LIKE ?";
$stmt = $conn->prepare($sql);
$search_term = '%' . $username . '%';
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
                <li><a href="profile.php?id=<?php echo $row['id']; ?>"><?php echo $row['username']; ?></a></li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>Sonuç bulunamadı.</p>
    <?php endif; ?>
</body>
</html>
