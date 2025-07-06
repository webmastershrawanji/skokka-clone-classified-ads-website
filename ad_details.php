<?php
session_start();
require 'config.php';

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    header('Location: ads.php');
    exit;
}

$stmt = $pdo->prepare("SELECT ads.*, users.username, categories.name AS category_name, locations.name AS location_name
                       FROM ads
                       JOIN users ON ads.user_id = users.id
                       JOIN categories ON ads.category_id = categories.id
                       JOIN locations ON ads.location_id = locations.id
                       WHERE ads.id = ?");
$stmt->execute([$id]);
$ad = $stmt->fetch();

if (!$ad) {
    header('Location: ads.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= htmlspecialchars($ad['title']) ?> - Skokka Clone</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<?php include 'header.php'; ?>

<div class="container mt-4">
    <h2><?= htmlspecialchars($ad['title']) ?></h2>
    <p><strong>Category:</strong> <?= htmlspecialchars($ad['category_name']) ?></p>
    <p><strong>Location:</strong> <?= htmlspecialchars($ad['location_name']) ?></p>
    <p><strong>Price:</strong> ₹<?= number_format($ad['price'], 2) ?></p>
    <p><strong>Description:</strong></p>
    <p><?= nl2br(htmlspecialchars($ad['description'])) ?></p>
    <p><strong>Posted by:</strong> <?= htmlspecialchars($ad['username']) ?> on <?= date('d M Y', strtotime($ad['created_at'])) ?></p>
    <a href="ads.php" class="btn btn-secondary">Back to Ads</a>
</div>

</body>
</html>
