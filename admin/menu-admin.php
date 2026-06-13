<?php
session_start();
require_once "db_connection.php";

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login-admin.php");
    exit();
}

/* ======================
   CATEGORY HANDLING
======================*/
if (isset($_POST['add_category'])) {
    $name = trim($_POST['category_name']);
    if (!empty($name)) {
        $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        header("Location: menu-admin.php?success=category_added");
        exit();
    }
}

if (isset($_POST['update_category'])) {
    $id = intval($_POST['category_id']);
    $name = trim($_POST['category_name']);
    if (!empty($name)) {
        $stmt = $conn->prepare("UPDATE categories SET name=? WHERE id=?");
        $stmt->bind_param("si", $name, $id);
        $stmt->execute();
        header("Location: menu-admin.php?success=category_updated");
        exit();
    }
}

if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'delete_category') {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM categories WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: menu-admin.php?success=category_deleted");
    exit();
}

// Category to edit
$category_to_edit = null;
if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'edit_category') {
    $id = intval($_GET['id']);
    $category_to_edit = $conn->query("SELECT * FROM categories WHERE id=$id")->fetch_assoc();
}

/* ======================
   MENU ITEM HANDLING
======================*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['add_item']) || isset($_POST['update_item']))) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = $_POST['price'];
    $category = intval($_POST['category']);
    $image_path = $_POST['current_image'] ?? null;

    // Image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $target_dir = "../images/menu/";
        if (!is_dir($target_dir))
            mkdir($target_dir, 0777, true);

        $image_name = time() . '_' . basename($_FILES['image']['name']);
        $target_file = $target_dir . $image_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_path = "images/menu/" . $image_name;
            if (!empty($_POST['current_image']) && file_exists("../" . $_POST['current_image'])) {
                unlink("../" . $_POST['current_image']);
            }
        }
    }

    if (isset($_POST['add_item'])) {
        $stmt = $conn->prepare("INSERT INTO menu_items (name, description, price, image, category_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdsi", $name, $description, $price, $image_path, $category);
        $stmt->execute();
        header("Location: menu-admin.php?success=item_added");
        exit();
    }

    if (isset($_POST['update_item'])) {
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("UPDATE menu_items SET name=?, description=?, price=?, image=?, category_id=? WHERE id=?");
        $stmt->bind_param("ssdssi", $name, $description, $price, $image_path, $category, $id);
        $stmt->execute();
        header("Location: menu-admin.php?success=item_updated");
        exit();
    }
}

// Delete menu item
if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'delete') {
    $id = intval($_GET['id']);
    $item = $conn->query("SELECT image FROM menu_items WHERE id=$id")->fetch_assoc();
    if (!empty($item['image']) && file_exists("../" . $item['image']))
        unlink("../" . $item['image']);
    $conn->query("DELETE FROM menu_items WHERE id=$id");
    header("Location: menu-admin.php?success=item_deleted");
    exit();
}

// Fetch categories and menu items
$categories = $conn->query("SELECT * FROM categories ORDER BY name ASC");
$all_categories = $conn->query("SELECT * FROM categories ORDER BY name ASC");
$item_to_edit = null;
if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'edit') {
    $id = intval($_GET['id']);
    $item_to_edit = $conn->query("SELECT * FROM menu_items WHERE id=$id")->fetch_assoc();
}
$menu_items = $conn->query("SELECT m.*, c.name AS category_name FROM menu_items m LEFT JOIN categories c ON m.category_id=c.id ORDER BY c.name ASC, m.name ASC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Admin | BakerBest</title>
    <style>
        /* GLOBAL */
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
            transition: 0.3s;
            overflow: hidden;
        }

        .sidebar.collapsed {
            width: 80px;
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

        .menu li {
            margin: 10px 0;
        }

        .menu a {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: white;
            padding: 12px 20px;
            transition: 0.3s;
        }

        .menu a:hover {
            background: #f3961c;
            border-radius: 8px;
        }

        .icon {
            font-size: 20px;
            width: 30px;
            text-align: center;
        }

        .text {
            margin-left: 10px;
            transition: 0.3s;
        }

        .sidebar.collapsed .text {
            display: none;
        }

        .sidebar.collapsed .logo-text {
            display: none;
        }

        .main {
            margin-left: 240px;
            transition: 0.3s;
        }

        .sidebar.collapsed~.main {
            margin-left: 80px;
        }

        .main {
            margin-left: 240px;
            padding: 30px;
            width: calc(100% - 240px);
        }

        h1,
        h2,
        h3,
        h4 {
            color: #7d3939;
        }

        .container {
            max-width: 1200px;
            margin: auto;
        }

        .form-container {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 40px;
        }

        .form-box {
            background: white;
            padding: 20px;
            border-radius: 15px;
            flex: 1;
            min-width: 280px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .form-box.full-width {
            flex: 100%;
        }

        input,
        select,
        textarea,
        button {
            display: block;
            width: 100%;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        button {
            background: #a84747;
            color: white;
            border: none;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #7d3939;
        }

        .btn.cancel {
            background: #ccc;
            color: #333;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            margin-top: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th,
        table td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }

        .action-buttons a {
            display: inline-block;
            margin-right: 5px;
            margin-bottom: 5px;
            padding: 5px 10px 5px 10px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
        }

        .action-buttons a.edit {
            background: #f3961c;
        }

        .action-buttons a.delete {
            background: #7d3939;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        @media (max-width:900px) {
            .main {
                margin-left: 0;
                width: 100%;
            }

            .form-container {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <div class="sidebar" id="sidebar">
        <div class="logo">
            <span class="logo-text">BakerBest</span>
            <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
        </div>

        <ul class="menu">
            <li>
                <a href="dashboard-admin.php">
                    <span class="icon">🏠</span>
                    <span class="text">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="orders-admin.php">
                    <span class="icon">📦</span>
                    <span class="text">Manage Orders</span>
                </a>
            </li>
            <li>
                <a href="customers-admin.php">
                    <span class="icon">👥</span>
                    <span class="text">Manage Customers</span>
                </a>
            </li>
            <li class="active">
                <a href="menu-admin.php">
                    <span class="icon">🍰</span>
                    <span class="text">Manage Menu</span>
                </a>
            </li>
            <li>
                <a href="gallery-admin.php">
                    <span class="icon">🖼️</span>
                    <span class="text">Manage Gallery</span>
                </a>
            </li>
            <li>
                <a href="contact-admin.php">
                    <span class="icon">📩</span>
                    <span class="text">Manage Contact</span>
                </a>
            </li>
            <li>
                <a href="logout-admin.php">
                    <span class="icon">🚪</span>
                    <span class="text">Logout</span>
                </a>
            </li>
        </ul>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById("sidebar").classList.toggle("collapsed");
        }
    </script>



    <div class="main">
        <div class="container">
            <h1>Manage Menu</h1>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert-success">
                    ✔ Successfully <?= str_replace('_', ' ', htmlspecialchars($_GET['success'])) ?>!
                </div>
            <?php endif; ?>

            <!-- Categories -->
            <h2>Categories</h2>
            <div class="form-container">
                <div class="form-box">
                    <h3><?= $category_to_edit ? 'Edit Category' : 'Add New Category' ?></h3>
                    <form method="POST">
                        <?php if ($category_to_edit): ?>
                            <input type="hidden" name="category_id" value="<?= $category_to_edit['id'] ?>">
                        <?php endif; ?>
                        <input type="text" name="category_name" placeholder="Category Name"
                            value="<?= $category_to_edit['name'] ?? '' ?>" required>
                        <button type="submit"
                            name="<?= $category_to_edit ? 'update_category' : 'add_category' ?>"><?= $category_to_edit ? 'Update' : 'Add' ?></button>
                        <?php if ($category_to_edit): ?><a href="menu-admin.php"
                                class="btn cancel">Cancel</a><?php endif; ?>
                    </form>
                </div>
                <div class="form-box">
                    <h3>Existing Categories</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $all_categories->data_seek(0);
                            while ($cat = $all_categories->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $cat['id'] ?></td>
                                    <td><?= htmlspecialchars($cat['name']) ?></td>
                                    <td class="action-buttons">
                                        <a href="menu-admin.php?action=edit_category&id=<?= $cat['id'] ?>"
                                            class="edit">Edit</a>
                                        <a href="menu-admin.php?action=delete_category&id=<?= $cat['id'] ?>" class="delete"
                                            onclick="return confirm('Deleting this category will remove it from all menu items. Proceed?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <hr>

            <!-- Menu Items -->
            <h2>Menu Items</h2>
            <div class="form-box full-width">
                <h3><?= $item_to_edit ? 'Edit Item' : 'Add Item' ?></h3>
                <form method="POST" enctype="multipart/form-data">
                    <?php if ($item_to_edit): ?>
                        <input type="hidden" name="id" value="<?= $item_to_edit['id'] ?>">
                        <input type="hidden" name="current_image" value="<?= $item_to_edit['image'] ?>">
                    <?php endif; ?>
                    <input type="text" name="name" placeholder="Item Name" value="<?= $item_to_edit['name'] ?? '' ?>"
                        required>
                    <textarea name="description"
                        placeholder="Description"><?= $item_to_edit['description'] ?? '' ?></textarea>
                    <input type="number" step="0.01" name="price" placeholder="Price"
                        value="<?= $item_to_edit['price'] ?? '' ?>" required>
                    <select name="category" required>
                        <option value="">Select Category</option>
                        <?php $categories->data_seek(0);
                        while ($c = $categories->fetch_assoc()): ?>
                            <option value="<?= $c['id'] ?>" <?= ($item_to_edit && $item_to_edit['category_id'] == $c['id']) ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <input type="file" name="image" accept="image/*">
                    <?php if ($item_to_edit && !empty($item_to_edit['image']) && file_exists("../" . $item_to_edit['image'])): ?>
                        <img src="../<?= $item_to_edit['image'] ?>" width="100" style="margin-bottom:10px;">
                    <?php endif; ?>
                    <button type="submit"
                        name="<?= $item_to_edit ? 'update_item' : 'add_item' ?>"><?= $item_to_edit ? 'Update' : 'Add' ?></button>
                    <?php if ($item_to_edit): ?><a href="menu-admin.php" class="btn cancel">Cancel</a><?php endif; ?>
                </form>
            </div>

            <h3>Existing Menu Items</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($item = $menu_items->fetch_assoc()): ?>
                        <tr>
                            <td><?= $item['id'] ?></td>
                            <td><?= htmlspecialchars($item['name']) ?></td>
                            <td><?= htmlspecialchars($item['description']) ?></td>
                            <td><?= htmlspecialchars($item['category_name']) ?></td>
                            <td>Rs <?= number_format($item['price'], 2) ?></td>
                            <td>
                                <?php if (!empty($item['image']) && file_exists("../" . $item['image'])): ?>
                                    <img src="../<?= $item['image'] ?>" width="60">
                                <?php else: ?>N/A<?php endif; ?>
                            </td>
                            <td class="action-buttons">
                                <a href="menu-admin.php?action=edit&id=<?= $item['id'] ?>" class="edit">Edit</a>
                                <a href="menu-admin.php?action=delete&id=<?= $item['id'] ?>" class="delete"
                                    onclick="return confirm('Delete this item?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

        </div>
    </div>
</body>

</html>