<?php
session_start();
require 'config.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch categories and locations for filters
$categories = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();
$locations = $pdo->query('SELECT * FROM locations ORDER BY name')->fetchAll();

// Prepare filters
$category_filter = $_GET['category'] ?? '';
$location_filter = $_GET['location'] ?? '';
$search = trim($_GET['search'] ?? '');

// Build query with filters
$query = "SELECT ads.*, users.username, categories.name AS category_name, locations.name AS location_name
          FROM ads
          JOIN users ON ads.user_id = users.id
          JOIN categories ON ads.category_id = categories.id
          JOIN locations ON ads.location_id = locations.id
          WHERE 1=1";

$params = [];

if ($category_filter) {
    $query .= " AND category_id = ?";
    $params[] = $category_filter;
}
if ($location_filter) {
    $query .= " AND location_id = ?";
    $params[] = $location_filter;
}
if ($search) {
    $query .= " AND (ads.title LIKE ? OR ads.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$query .= " ORDER BY ads.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$ads = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Ads - Skokka Clone</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<?php include 'header.php'; ?>

<div class="container mt-4">
    <h2>Classified Ads</h2>

    <form method="get" class="row g-3 mb-4">
        <div class="col-md-3">
            <select name="category" class="form-select">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $category_filter ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <select name="location" class="form-select">
                <option value="">All Locations</option>
                <?php foreach ($locations as $loc): ?>
                    <option value="<?= $loc['id'] ?>" <?= $loc['id'] == $location_filter ? 'selected' : '' ?>>
                        <?= htmlspecialchars($loc['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Search ads..." value="<?= htmlspecialchars($search) ?>" />
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
    </form>

    <?php if ($ads): ?>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php foreach ($ads as $ad): ?>
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($ad['title']) ?></h5>
                            <h6 class="card-subtitle mb-2 text-muted">
                                <?= htmlspecialchars($ad['category_name']) ?> - <?= htmlspecialchars($ad['location_name']) ?>
                            </h6>
                            <p class="card-text"><?= nl2br(htmlspecialchars(substr($ad['description'], 0, 100))) ?>...</p>
                            <p class="card-text"><strong>Price:</strong> ₹<?= number_format($ad['price'], 2) ?></p>
                            <a href="ad_details.php?id=<?= $ad['id'] ?>" class="btn btn-primary">View Details</a>
                        </div>
                        <div class="card-footer text-muted">
                            Posted by <?= htmlspecialchars($ad['username']) ?> on <?= date('d M Y', strtotime($ad['created_at'])) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No ads found.</p>
    <?php endif; ?>
</div>

</body>
</html>
