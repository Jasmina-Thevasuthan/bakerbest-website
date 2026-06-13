<?php
session_start();
require_once "db_connection.php";

// Redirect if already logged in
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard-admin.php");
    exit();
}

$msg = "";

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();

            if ($password === $admin['password']) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];

                header("Location: dashboard-admin.php");
                exit();
            } else {
                $msg = "Invalid password.";
            }
        } else {
            $msg = "Admin not found.";
        }
    } else {
        $msg = "Please enter both username and password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login</title>

<style>
    body {
    margin: 0;
    font-family: Arial, sans-serif;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: linear-gradient(135deg, #fff8f0, #fdebd2);
    overflow: hidden;
}

/* Layout Wrapper */
.login-wrapper {
    width: 900px;
    height: 550px;
    display: flex;
    border-radius: 25px;
    overflow: hidden;
    box-shadow: 0 30px 60px rgba(0,0,0,0.2);
    background: white;
}

/* Left Branding Side */
.login-left {
    flex: 1;
    background: linear-gradient(135deg, #7d3939, #f3961c);
    color: white;
    padding: 60px 40px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.login-left h1 {
    font-size: 36px;
    margin-bottom: 20px;
}

.login-left p {
    font-size: 16px;
    line-height: 1.6;
}

/* Right Login Form */
.login-container {
    flex: 1;
    padding: 60px 50px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.login-container h2 {
    color: #7d3939;
    margin-bottom: 25px;
}

/* Floating Input */
.input-group {
    position: relative;
    margin-bottom: 25px;
}

.input-group input {
    width: 100%;
    padding: 14px 10px;
    border: none;
    border-bottom: 2px solid #7d3939;
    font-size: 15px;
    outline: none;
    background: transparent;
}

.input-group label {
    position: absolute;
    left: 5px;
    top: 14px;
    color: #777;
    transition: 0.3s ease;
    pointer-events: none;
}

.input-group input:focus + label,
.input-group input:valid + label {
    top: -10px;
    font-size: 12px;
    color: #f3961c;
}

/* Button */
button {
    padding: 14px;
    background: #7d3939;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    cursor: pointer;
    transition: 0.3s ease;
}

button:hover {
    background: #f3961c;
    transform: translateY(-3px);
}

/* Error */
.error-msg {
    color: red;
    margin-bottom: 15px;
    font-weight: bold;
}

/* Decorative Shapes */
.background-shapes::before,
.background-shapes::after {
    content: "";
    position: absolute;
    border-radius: 50%;
    z-index: -1;
}

.background-shapes::before {
    width: 300px;
    height: 300px;
    background: rgba(125,57,57,0.15);
    top: -80px;
    left: -80px;
}

.background-shapes::after {
    width: 250px;
    height: 250px;
    background: rgba(243,150,28,0.2);
    bottom: -80px;
    right: -80px;
}

/* Responsive */
@media (max-width: 900px) {
    .login-wrapper {
        flex-direction: column;
        height: auto;
        width: 90%;
    }

    .login-left {
        text-align: center;
        padding: 40px;
    }

    .login-container {
        padding: 40px;
    }
}

</style>
</head>

<body>

<div class="background-shapes"></div>

<div class="login-wrapper">

    <div class="login-left">
        <h1>🍰 Baker Best</h1>
        <p>Welcome back Admin. Manage your bakery dashboard, products and orders easily.</p>
    </div>

    <div class="login-container">
        <h2>Admin Login</h2>

        <?php if (!empty($msg)): ?>
            <p class="error-msg"><?= htmlspecialchars($msg) ?></p>
        <?php endif; ?>

        <form method="POST" autocomplete="off">
            <div class="input-group">
                <input type="text" name="username" required>
                <label>Enter Username</label>
            </div>

            <div class="input-group">
                <input type="password" name="password" required>
                <label>Enter Password</label>
            </div>

            <button type="submit">Login</button>
        </form>
    </div>

</div>

</body>

</html>
