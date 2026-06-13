<?php
session_start();
require 'admin/db_connection.php';
$errors = [];

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $errors[] = "All fields are required.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows > 0) {
            $user = $res->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                header("Location: order.php");
                exit;
            } else {
                $errors[] = "Incorrect password.";
            }
        } else {
            $errors[] = "Email not registered.";
        }
    }
}

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if (empty($name) || empty($email) || empty($phone) || empty($password) || empty($confirm)) {
        $errors[] = "All fields are required.";
    } elseif ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name,email,phone,password) VALUES (?,?,?,?)");
        $stmt->bind_param("ssss", $name, $email, $phone, $hashed);
        if ($stmt->execute()) {
            $_SESSION['user_id'] = $conn->insert_id;
            $_SESSION['user_name'] = $name;
            header("Location: order.php");
            exit;
        } else {
            $errors[] = "Email already exists.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login/Register - BakerBest</title>
    <link rel="icon" href="images/logo.png" type="image/png">

    <style>
        :root {
            --primary-color: #7d3939;
            --accent-color: #f3961c;
            --bg-color: #f5f5f5;
            --dark-color: #2c2c2c;
            --light-gray: #e0e0e0;
            --font-heading: 'Roboto Slab', serif;
            --font-body: 'Poppins', sans-serif;
            --shadow-soft: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: var(--font-body);
        }

        body {
            background: var(--bg-color);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .card {
            display: flex;
            width: 800px;
            max-width: 90%;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow-soft);
            transition: transform 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .left {
            flex: 1;
            background: var(--primary-color);
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            padding: 40px 20px;
        }

        .left h1 {
            font-family: var(--font-heading);
            font-size: 2.5em;
            margin-bottom: 20px;
        }

        .left p {
            font-size: 1.1em;
            color: #fff;
            opacity: 0.85;
            text-align: center;
        }

        .right {
            flex: 1;
            padding: 40px 30px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #fff;
        }

        .tab-buttons {
            display: flex;
            margin-bottom: 30px;
            border-bottom: 2px solid var(--light-gray);
        }

        .tab-buttons div {
            flex: 1;
            text-align: center;
            padding: 12px 0;
            cursor: pointer;
            font-weight: 600;
            color: var(--dark-color);
            transition: color 0.3s, border-bottom 0.3s;
            border-bottom: 3px solid transparent;
        }

        .tab-buttons .active {
            border-bottom: 3px solid var(--accent-color);
            color: var(--accent-color);
        }

        form {
            display: none;
            flex-direction: column;
            gap: 15px;
        }

        form.active {
            display: flex;
        }

        input {
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid var(--light-gray);
            font-size: 1em;
            transition: 0.3s;
        }

        input:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 8px rgba(243, 150, 28, 0.3);
            outline: none;
        }

        button {
            padding: 12px;
            background: var(--accent-color);
            border: none;
            border-radius: 8px;
            color: #fff;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: var(--primary-color);
        }

        .error {
            background: #ffe6e6;
            color: var(--primary-color);
            padding: 10px;
            border-radius: 6px;
            text-align: center;
            font-weight: 600;
        }

        @media(max-width:900px) {
            .card {
                flex-direction: column;
                width: 90%;
            }

            .left,
            .right {
                flex: none;
                width: 100%;
                padding: 30px;
            }
        }
    </style>
</head>

<body>

    <div class="card">
        <div class="left">
            <h1>BakerBest</h1>
            <p>Welcome! Please login or register to place your order online. Enjoy fresh bakery delights delivered to
                your doorstep!</p>
        </div>
        <div class="right">
            <div class="tab-buttons">
                <div id="login-tab" class="active">Login</div>
                <div id="register-tab">Register</div>
            </div>

            <?php foreach ($errors as $e) {
                echo "<p class='error'>$e</p>";
            } ?>

            <form id="login-form" class="active" method="POST">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login">Login</button>
            </form>

            <form id="register-form" method="POST">
                <input type="text" name="name" placeholder="Full Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="text" name="phone" placeholder="Phone Number" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                <button type="submit" name="register">Register</button>
            </form>
        </div>
    </div>

    <script>
        const loginTab = document.getElementById('login-tab');
        const registerTab = document.getElementById('register-tab');
        const loginForm = document.getElementById('login-form');
        const registerForm = document.getElementById('register-form');

        loginTab.addEventListener('click', () => {
            loginTab.classList.add('active');
            registerTab.classList.remove('active');
            loginForm.classList.add('active');
            registerForm.classList.remove('active');
        });

        registerTab.addEventListener('click', () => {
            registerTab.classList.add('active');
            loginTab.classList.remove('active');
            registerForm.classList.add('active');
            loginForm.classList.remove('active');
        });
    </script>

</body>

</html>