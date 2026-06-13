<?php
session_start();
require_once "db_connection.php";

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login-admin.php");
    exit();
}

$msg = "";
$msg_type = "";

// ======================
// IMAGE UPLOAD
// ======================
if (isset($_POST['upload_image'])) {
    $category_id = !empty($_POST['category_id']) ? intval($_POST['category_id']) : NULL;
    $description = trim($_POST['description']);

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $image_name = basename($_FILES['image']['name']);
        $image_tmp = $_FILES['image']['tmp_name'];
        $target_dir = "images/gallery/";

        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

        $target_file = $target_dir . $image_name;

        if (move_uploaded_file($image_tmp, $target_file)) {
            $stmt = $conn->prepare("INSERT INTO gallery_images (category_id, image_path, description) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $category_id, $target_file, $description);
            $stmt->execute();
            $stmt->close();
            $msg = "Image uploaded successfully!";
            $msg_type = "success";
        } else {
            $msg = "Failed to upload image.";
            $msg_type = "error";
        }
    } else {
        $msg = "Please select a valid image.";
        $msg_type = "error";
    }
}

// ======================
// IMAGE DELETE
// ======================
if (isset($_POST['delete_image'])) {
    $image_id = intval($_POST['image_id']);
    $img_res = $conn->query("SELECT image_path FROM gallery_images WHERE id=$image_id LIMIT 1");
    $img_data = $img_res->fetch_assoc();

    if ($img_data) {
        if (file_exists($img_data['image_path'])) unlink($img_data['image_path']);
        $conn->query("DELETE FROM gallery_images WHERE id=$image_id");
        $msg = "Image deleted successfully!";
        $msg_type = "success";
    }
}

// ======================
// FETCH DATA
// ======================
$categories = $conn->query("SELECT id, name FROM gallery_categories ORDER BY name ASC");
$images = $conn->query("
    SELECT g.id, g.image_path, g.description, c.name AS category_name 
    FROM gallery_images g 
    LEFT JOIN gallery_categories c ON g.category_id = c.id
    ORDER BY g.uploaded_at DESC
");

// Dashboard stats
$totalOrders = $conn->query("SELECT COUNT(*) AS total FROM orders")->fetch_assoc()['total'] ?? 0;
$totalCustomers = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'] ?? 0;
$totalMenu = $conn->query("SELECT COUNT(*) AS total FROM menu_items")->fetch_assoc()['total'] ?? 0;
$totalGallery = $conn->query("SELECT COUNT(*) AS total FROM gallery_images")->fetch_assoc()['total'] ?? 0;
$totalMessages = $conn->query("SELECT COUNT(*) AS total FROM contact_messages")->fetch_assoc()['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Gallery | BakerBest</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
/* ======================
   SIDEBAR & GLOBAL
====================== */
body {
    margin: 0;
    font-family: Arial, sans-serif;
    display: flex;
    background: #fdf6f0;
}
.sidebar {
    width: 240px;
    height: 100vh;
    background: linear-gradient(180deg, #7d3939, #a84747);
    color: white;
    position: fixed;
    overflow: hidden;
}
.logo {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    font-size: 20px;
}
.toggle-btn {
    background: none;
    border: none;
    color: white;
    font-size: 20px;
    cursor: pointer;
}
.menu {
    list-style: none;
    padding: 0;
}
.menu li { margin: 10px 0; }
.menu a {
    display: flex;
    align-items: center;
    text-decoration: none;
    color: white;
    padding: 12px 20px;
    transition: 0.3s;
}
.menu a:hover { background: #f3961c; border-radius: 8px; }
.icon { font-size: 20px; width: 30px; text-align: center; }
.text { margin-left: 10px; transition: 0.3s; }
.sidebar.collapsed .text { display: none; }
.sidebar.collapsed .logo-text { display: none; }

/* ======================
   MAIN
====================== */
.main {
    margin-left: 240px;
    padding: 30px;
    width: calc(100% - 240px);
}
.header { display: flex; justify-content: space-between; align-items: center; }
.header h1 { color: #7d3939; }

/* CARDS */
.cards {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 20px;
    margin-top: 30px;
}
.card {
    background: white;
    padding: 20px;
    border-radius: 15px;
    text-align: center;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
.card h3 { color: #7d3939; }
.card p { font-size: 22px; font-weight: bold; color: #f3961c; }

/* GALLERY */
.gallery {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 20px;
    margin-top: 30px;
}
.gallery-item {
    background: white;
    padding: 15px;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    text-align: center;
}
.gallery-item img {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 10px;
}
.gallery-item p {
    margin: 5px 0;
}

/* FORM */
.admin-form {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 25px;
}
.admin-form input[type="text"], .admin-form select, .admin-form input[type="file"] {
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #ccc;
}
.admin-form button {
    padding: 10px 20px;
    background: #a84747;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
}
.admin-form button:hover { background: #f3961c; }

/* DELETE BUTTON */
.delete-btn {
    padding: 5px 10px;
    background: #d32f2f;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}
.delete-btn:hover { background: #f44336; }

/* POPUP */
.popup {
    display: none;
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.5);
    justify-content: center;
    align-items: center;
}
.popup-content {
    background: white;
    padding: 20px;
    border-radius: 12px;
    text-align: center;
}
.popup-content button {
    margin-top: 10px;
    padding: 8px 16px;
    background: #a84747;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}
.popup-content button:hover { background: #f3961c; }

/* RESPONSIVE */
@media (max-width: 900px) {
    .cards, .gallery { grid-template-columns: repeat(1,1fr); }
    .sidebar { display: none; }
    .main { margin-left: 0; width: 100%; }
}
</style>

<script>
function toggleSidebar() {
    document.getElementById("sidebar").classList.toggle("collapsed");
}
</script>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
    <div class="logo">
        <span class="logo-text">BakerBest</span>
        <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
    </div>
    <ul class="menu">
        <li><a href="dashboard-admin.php"><span class="icon">🏠</span><span class="text">Dashboard</span></a></li>
        <li><a href="orders-admin.php"><span class="icon">📦</span><span class="text">Orders</span></a></li>
        <li><a href="customers-admin.php"><span class="icon">👥</span><span class="text">Customers</span></a></li>
        <li><a href="menu-admin.php"><span class="icon">🍰</span><span class="text">Menu</span></a></li>
        <li class="active"><a href="gallery-admin.php"><span class="icon">🖼️</span><span class="text">Gallery</span></a></li>
        <li><a href="contact-admin.php"><span class="icon">📩</span><span class="text">Contact</span></a></li>
        <li><a href="logout-admin.php"><span class="icon">🚪</span><span class="text">Logout</span></a></li>
    </ul>
</div>

<!-- MAIN -->
<div class="main">

    <div class="header">
        <h1>Gallery Management</h1>
        <div>Welcome, Admin</div>
    </div>

    <!-- Stats Cards -->
    <div class="cards">
        <div class="card"><h3>Total Orders</h3><p><?= $totalOrders ?></p></div>
        <div class="card"><h3>Total Customers</h3><p><?= $totalCustomers ?></p></div>
        <div class="card"><h3>Menu Items</h3><p><?= $totalMenu ?></p></div>
        <div class="card"><h3>Gallery Images</h3><p><?= $totalGallery ?></p></div>
        <div class="card"><h3>Contact Messages</h3><p><?= $totalMessages ?></p></div>
    </div>

    <!-- Upload Form -->
    <form action="" method="POST" enctype="multipart/form-data" class="admin-form">
        <select name="category_id">
            <option value="">Select Category (Optional)</option>
            <?php while ($cat = $categories->fetch_assoc()): ?>
                <option value="<?= intval($cat['id']) ?>"><?= htmlspecialchars($cat['name']) ?></option>
            <?php endwhile; ?>
        </select>
        <input type="text" name="description" placeholder="Image description (optional)">
        <input type="file" name="image" required>
        <button type="submit" name="upload_image">Upload</button>
    </form>

    <!-- Gallery -->
    <div class="gallery">
        <?php while ($img = $images->fetch_assoc()): ?>
            <div class="gallery-item">
                <img src="<?= htmlspecialchars($img['image_path']) ?>" alt="<?= htmlspecialchars($img['description']) ?>">
                <p><?= htmlspecialchars($img['category_name'] ?? 'Uncategorized') ?></p>
                <p><?= htmlspecialchars($img['description']) ?></p>
                <form action="" method="POST" onsubmit="return confirm('Are you sure you want to delete this image?');">
                    <input type="hidden" name="image_id" value="<?= intval($img['id']) ?>">
                    <button type="submit" name="delete_image" class="delete-btn">Delete</button>
                </form>
            </div>
        <?php endwhile; ?>
    </div>

</div>

<!-- Popup -->
<div id="popup" class="popup">
    <div class="popup-content">
        <span id="popup-message"></span>
        <button id="popup-close">OK</button>
    </div>
</div>

<script>
const popup = document.getElementById('popup');
const popupMessage = document.getElementById('popup-message');
const popupClose = document.getElementById('popup-close');
popupClose.onclick = () => popup.style.display = 'none';

<?php if ($msg): ?>
popupMessage.innerText = "<?= htmlspecialchars($msg) ?>";
popup.style.display = 'flex';
<?php endif; ?>
</script>

</body>
</html>
