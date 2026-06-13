<?php
require_once 'admin/db_connection.php';

$categories = $conn->query("SELECT * FROM categories ORDER BY name ASC");

$menu_items = $conn->query("
SELECT m.*, c.name AS category_name
 FROM menu_items m
 LEFT JOIN categories c ON m.category_id=c.id
 ORDER BY c.name ASC, m.name ASC
");

$menu_array = [];
while ($item = $menu_items->fetch_assoc()) {
    $menu_array[] = $item;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Menu</title>
    <link rel="stylesheet" href="assets/css/menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="icon" href="images/logo.png" type="image/png">
</head>

<body>

    <?php include 'header.php'; ?>

    <section class="menu-section">
        <h1 class="section-title">⭐ Our Menu</h1>

        <div class="menu-search-container">
            <input type="text" id="menu-search" placeholder="Search menu items...">
        </div>

        <div class="menu-navigation"></div>

        <?php while ($cat = $categories->fetch_assoc()): ?>
            <h2 class="category-title" id="category-<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></h2>
            <div class="menu-container">
                <?php foreach ($menu_array as $item):
                    if ($item['category_id'] != $cat['id'])
                        continue;

                    $image_path = 'images/default.png';

                    if (!empty($item['image'])) {
                        $full_path = __DIR__ . '/' . $item['image'];
                        if (file_exists($full_path)) {
                            $image_path = $item['image'];
                        }
                    }
                    ?>
                    <div class="menu-card menu-item" data-category="<?= htmlspecialchars($cat['name']) ?>">
                        <img src="<?= $image_path ?>" alt="<?= htmlspecialchars($item['name']) ?>" loading="lazy">
                        <h3 class="item-name"><?= htmlspecialchars($item['name']) ?></h3>
                        <p><?= htmlspecialchars($item['description']) ?></p>
                        <p class="price">Rs <?= number_format($item['price'], 2) ?></p>
                        <div class="order-btn"><a href="login.php">Order Now</a></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endwhile; ?>
    </section>

    <script>
        const searchInput = document.getElementById("menu-search");
        searchInput.addEventListener("input", () => {
            const query = searchInput.value.toLowerCase();
            document.querySelectorAll(".menu-item").forEach(item => {
                const name = item.querySelector(".item-name").textContent.toLowerCase();
                const category = item.dataset.category.toLowerCase();
                item.style.display = name.includes(query) || category.includes(query) ? "block" : "none";
            });
        });

        const menuNav = document.querySelector(".menu-navigation");
        const categoriesTitles = document.querySelectorAll(".category-title");
        categoriesTitles.forEach(cat => {
            const btn = document.createElement("button");
            btn.textContent = cat.textContent;
            btn.classList.add("menu-nav-btn");
            btn.addEventListener("click", () => {
                cat.scrollIntoView({ behavior: "smooth" });
            });
            menuNav.appendChild(btn);
        });
    </script>

    <?php include 'footer.php'; ?>
</body>

</html>