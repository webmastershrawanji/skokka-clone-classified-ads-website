<?php
session_start();
require 'config.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$errors = [];
$title = '';
$description = '';
$price = '';
$category_id = '';
$location_id = '';

// Fetch categories and locations for form
$categories = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();
$locations = $pdo->query('SELECT * FROM locations ORDER BY name')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $category_id = $_POST['category'] ?? '';
    $location_id = $_POST['location'] ?? '';

    if (empty($title)) {
        $errors[] = 'Title is required';
    }
    if (empty($description)) {
        $errors[] = 'Description is required';
    }
    if (empty($price) || !is_numeric($price)) {
        $errors[] = 'Valid price is required';
    }
    if (empty($category_id)) {
        $errors[] = 'Category is required';
    }
    if (empty($location_id)) {
        $errors[] = 'Location is required';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('INSERT INTO ads (user_id, category_id, location_id, title, description, price) VALUES (?, ?, ?, ?, ?, ?)');
        if ($stmt->execute([$_SESSION['user_id'], $category_id, $location_id, $title, $description, $price])) {
            header('Location: ads.php');
            exit;
        } else {
            $errors[] = 'Failed to post ad, please try again';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Post Ad - Skokka Clone</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<?php include 'header.php'; ?>

<div class="container mt-4">
    <h2>Post a New Ad</h2>

    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                <li><?=htmlspecialchars($error)?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="post_ad.php" novalidate>
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" class="form-control" id="title" name="title" required value="<?=htmlspecialchars($title)?>" />
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="5" required><?=htmlspecialchars($description)?></textarea>
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Price (₹)</label>
            <input type="text" class="form-control" id="price" name="price" required value="<?=htmlspecialchars($price)?>" />
        </div>
        <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <select class="form-select" id="category" name="category" required>
                <option value="">Select Category</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $category_id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="location" class="form-label">Location</label>
            <select class="form-select" id="location" name="location" required>
                <option value="">Select Location</option>
                <?php foreach ($locations as $loc): ?>
                    <option value="<?= $loc['id'] ?>" <?= $loc['id'] == $location_id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($loc['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Post Ad</button>
        <a href="ads.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

</body>
</html>
