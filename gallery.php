<?php
require_once 'admin/db_connection.php';

$images = $conn->query("
    SELECT g.*, c.name AS category_name 
    FROM gallery_images g 
    LEFT JOIN gallery_categories c ON g.category_id = c.id
    ORDER BY g.uploaded_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery</title>
    <link rel="stylesheet" href="assets/css/gallery.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="icon" href="images/logo.png" type="image/png">
</head>

<body>
    <?php include 'header.php'; ?>



    <div class="gallery">
        <h1>Our Gallery</h1>
        <?php while ($img = $images->fetch_assoc()): ?>
            <div class="gallery-item">
                <img src="<?= htmlspecialchars($img['image_path']) ?>" alt="<?= htmlspecialchars($img['description']) ?>">
                <?php if (!empty($img['category_name'])): ?>
                    <p class="category"><?= htmlspecialchars($img['category_name']) ?></p>
                <?php endif; ?>
                <?php if (!empty($img['description'])): ?>
                    <p class="desc"><?= htmlspecialchars($img['description']) ?></p>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>
    <?php include 'footer.php'; ?>

</body>

</html>