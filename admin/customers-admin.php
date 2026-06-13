<?php
session_start();
require_once "db_connection.php";

// Login check
if (!isset($_SESSION['admin_id'])) {
    header("Location: login-admin.php");
    exit();
}

$msg = "";

// ADD CUSTOMER
if (isset($_POST['add_customer'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $phone, $password);
    $msg = $stmt->execute() ? "Customer added successfully!" : "Error: " . $conn->error;
    $stmt->close();
}

// UPDATE CUSTOMER
if (isset($_POST['update_customer'])) {
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET name=?, email=?, phone=?, password=? WHERE id=?");
        $stmt->bind_param("ssssi", $name, $email, $phone, $password, $id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET name=?, email=?, phone=? WHERE id=?");
        $stmt->bind_param("sssi", $name, $email, $phone, $id);
    }
    $msg = $stmt->execute() ? "Customer updated successfully!" : "Error updating customer!";
    $stmt->close();
}

// DELETE CUSTOMER
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $msg = $stmt->execute() ? "Customer deleted successfully!" : "Failed to delete customer!";
    $stmt->close();
}

// FETCH CUSTOMERS
$customers = $conn->query("SELECT * FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Manage Customers</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #7d3939;
            --secondary: #f3961c;
            --bg: #fdf6f0;
            --card: #fff;
            --danger: #d9534f;
            --success: #28a745;
        }

        * { box-sizing: border-box; margin:0; padding:0; font-family: 'Poppins', sans-serif; }
        body { display:flex; min-height:100vh; background: var(--bg); color:#333; }

        /* --- SIDEBAR --- */
        .sidebar {
            width: 260px;
            height: 100vh;
            background: var(--primary);
            color: white;
            position: fixed;
            transition: 0.3s ease;
            overflow: hidden;
            z-index: 1000;
        }

        .sidebar.collapsed { width: 80px; }

        .logo-container {
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            padding: 25px 20px; 
            height: 80px;
        }

        .logo-text { font-size: 24px; font-weight: 700; white-space: nowrap; }

        .toggle-btn {
            background: none; border: none; color: white;
            font-size: 24px; cursor: pointer;
        }

        .menu { list-style: none; padding: 0 10px; margin-top: 20px; }
        .menu li { margin: 5px 0; }

        .menu a {
            display: flex; align-items: center;
            text-decoration: none; color: white;
            padding: 15px 15px;
            transition: 0.3s;
            border-radius: 12px;
            white-space: nowrap;
        }

        .menu a:hover { background: rgba(255, 255, 255, 0.1); }
        
        /* Active Item Styling */
        .menu li.active a { background: var(--secondary); font-weight: 600; }

        /* Icon Fix */
        .menu a i { 
            width: 35px; 
            font-size: 20px; 
            text-align: center;
            font-family: "Font Awesome 6 Free" !important;
            font-weight: 900;
        }

        .text { margin-left: 15px; font-size: 18px; transition: 0.3s; }
        .sidebar.collapsed .text, .sidebar.collapsed .logo-text { display: none; }

        /* --- MAIN CONTENT --- */
        .main-content { margin-left: 260px; padding: 40px; width: 100%; transition: 0.3s; }
        .sidebar.collapsed ~ .main-content { margin-left: 80px; }

        h1 { color: var(--primary); margin-bottom: 30px; font-size: 28px; font-weight: 600; }

        .container {
            background: var(--card);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        thead { background: var(--primary); color: white; }
        thead th { padding: 15px; text-align: left; }
        tbody td { padding: 15px; border-bottom: 1px solid #f0f0f0; }

        .btn { padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer; font-weight: 500; transition: 0.3s; }
        .btn-add { background: var(--secondary); color: white; margin-bottom: 20px; }
        .btn-edit { background: #4a4a4a; color: white; margin-right: 5px; }
        .btn-del { background: var(--danger); color: white; }

        /* Modals */
        .modal { position: fixed; top: 0; left: 0; width: 100%; height: 100%; display: none; justify-content: center; align-items: center; background: rgba(0,0,0,0.4); z-index: 2000; }
        .modal-content { background: white; padding: 30px; border-radius: 15px; width: 450px; }
        .modal-content input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 8px; }

        @media (max-width: 900px) {
            .sidebar { transform: translateX(-100%); }
            .main-content { margin-left: 0; }
        }
    </style>
</head>
<body>

<div class="sidebar" id="sidebar">
    <div class="logo-container">
        <span class="logo-text">BakerBest</span>
        <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
    </div>
    <ul class="menu">
        <li>
            <a href="dashboard-admin.php">
                <i class="fa-solid fa-house"></i>
                <span class="text">Dashboard</span>
            </a>
        </li>
        <li>
            <a href="orders-admin.php">
                <i class="fa-solid fa-box"></i>
                <span class="text">Orders</span>
            </a>
        </li>
        <li class="active">
            <a href="customers-admin.php">
                <i class="fa-solid fa-users"></i>
                <span class="text">Customers</span>
            </a>
        </li>
        <li>
            <a href="menu-admin.php">
                <i class="fa-solid fa-utensils"></i>
                <span class="text">Menu</span>
            </a>
        </li>
        <li>
            <a href="gallery-admin.php">
                <i class="fa-solid fa-images"></i>
                <span class="text">Gallery</span>
            </a>
        </li>
        <li>
            <a href="contact-admin.php">
                <i class="fa-solid fa-envelope"></i>
                <span class="text">Contact</span>
            </a>
        </li>
        <li style="margin-top: 50px;">
            <a href="logout-admin.php">
                <i class="fa-solid fa-sign-out-alt"></i>
                <span class="text">Logout</span>
            </a>
        </li>
    </ul>
</div>

<div class="main-content">
    <h1>Manage Customers</h1>

    <?php if($msg): ?>
        <div style="background: var(--success); color: white; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
            <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($msg) ?>
        </div>
    <?php endif; ?>

    <div class="container">
        <button class="btn btn-add" onclick="openAddModal()">+ Add Customer</button>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $customers->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['phone']) ?></td>
                        <td><?= $row['created_at'] ?></td>
                        <td>
                            <button class="btn btn-edit" onclick="openEditModal('<?= $row['id'] ?>','<?= addslashes($row['name']) ?>','<?= $row['email'] ?>','<?= $row['phone'] ?>')">Edit</button>
                            <button class="btn btn-del" onclick="openDeleteConfirm(<?= $row['id'] ?>)">Delete</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal" id="addModal">
    <div class="modal-content">
        <h3>Add New Customer</h3>
        <form method="post">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="text" name="phone" placeholder="Phone Number" required>
            <input type="password" name="password" placeholder="Password" required>
            <button class="btn btn-add" type="submit" name="add_customer" style="width:100%;">Save Customer</button>
            <button type="button" onclick="closeAddModal()" style="width:100%; background:none; border:none; margin-top:10px; cursor:pointer;">Cancel</button>
        </form>
    </div>
</div>

<div class="modal" id="editModal">
    <div class="modal-content">
        <h3>Edit Customer</h3>
        <form method="post">
            <input type="hidden" name="id" id="edit_id">
            <input type="text" name="name" id="edit_name" required>
            <input type="email" name="email" id="edit_email" required>
            <input type="text" name="phone" id="edit_phone" required>
            <input type="password" name="password" placeholder="New Password (leave blank to keep)">
            <button class="btn btn-edit" type="submit" name="update_customer" style="width:100%; background:var(--secondary);">Update Customer</button>
            <button type="button" onclick="closeEditModal()" style="width:100%; background:none; border:none; margin-top:10px; cursor:pointer;">Cancel</button>
        </form>
    </div>
</div>

<div class="modal" id="deleteConfirmModal">
    <div class="modal-content" style="text-align:center;">
        <i class="fa-solid fa-triangle-exclamation" style="font-size:40px; color: var(--danger); margin-bottom:15px;"></i>
        <h3>Confirm Delete?</h3>
        <p>This will permanently remove the customer.</p><br>
        <div style="display:flex; gap:10px; justify-content:center;">
            <a href="#" id="deleteConfirmLink" class="btn btn-del" style="text-decoration:none;">Delete</a>
            <button class="btn" onclick="closeDeleteConfirm()" style="background:#eee; color:#333;">Cancel</button>
        </div>
    </div>
</div>

<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('collapsed');
    }
    function openAddModal() { document.getElementById('addModal').style.display='flex'; }
    function closeAddModal() { document.getElementById('addModal').style.display='none'; }
    function openEditModal(id, name, email, phone) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_phone').value = phone;
        document.getElementById('editModal').style.display = 'flex';
    }
    function closeEditModal() { document.getElementById('editModal').style.display = 'none'; }
    function openDeleteConfirm(id) {
        document.getElementById('deleteConfirmLink').href = '?delete=' + id;
        document.getElementById('deleteConfirmModal').style.display = 'flex';
    }
    function closeDeleteConfirm() { document.getElementById('deleteConfirmModal').style.display = 'none'; }
</script>

</body>
</html>