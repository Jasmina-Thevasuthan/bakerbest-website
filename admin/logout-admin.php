<?php
session_start();

// Logout if confirmed
if (isset($_GET['action']) && $_GET['action'] === 'logout_confirmed') {
    session_unset();
    session_destroy();
    header("Location: login-admin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Logout Confirmation</title>
<link rel="icon" href="../images/logo.png" type="image/png">
<style>
/* =========================
   BODY & OVERLAY
========================= */
body {
    margin: 0;
    font-family: 'Arial', sans-serif;
    background: #f5f5f5;
}
.overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    display: flex;
    justify-content: center;
    align-items: center;
}

/* =========================
   POPUP MODAL
========================= */
.popup {
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
    padding: 30px 40px;
    max-width: 400px;
    width: 90%;
    text-align: center;
    position: relative;
    animation: popupAppear 0.3s ease-out;
}
@keyframes popupAppear {
    from { transform: scale(0.7); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}

.popup h2 {
    color: #7d3939;
    margin-bottom: 20px;
    font-size: 24px;
}

.popup p {
    color: #555;
    margin-bottom: 25px;
    font-size: 16px;
}

/* BUTTONS */
.popup .btn {
    padding: 10px 25px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.2s ease;
    margin: 0 10px;
}
.btn-confirm {
    background: #d32f2f;
    color: white;
}
.btn-confirm:hover {
    background: #f44336;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(244,67,54,0.4);
}
.btn-cancel {
    background: #eee;
    color: #333;
}
.btn-cancel:hover {
    background: #ccc;
    transform: translateY(-2px);
}

/* ICON */
.popup .icon {
    font-size: 50px;
    color: #f3961c;
    margin-bottom: 15px;
}
</style>
</head>
<body>

<div class="overlay">
    <div class="popup">
        <div class="icon">⚠️</div>
        <h2>Confirm Logout</h2>
        <p>Are you sure you want to logout from your admin account?</p>
        <button class="btn btn-confirm" onclick="logoutConfirmed()">Yes, Logout</button>
        <button class="btn btn-cancel" onclick="cancelLogout()">Cancel</button>
    </div>
</div>

<script>
function logoutConfirmed() {
    window.location.href = "?action=logout_confirmed";
}

function cancelLogout() {
    window.location.href = "dashboard-admin.php"; // Change to your dashboard page
}
</script>

</body>
</html>
