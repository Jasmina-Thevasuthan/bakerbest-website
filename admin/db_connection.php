<?php
$host = "127.0.0.1";
$user = "root";
$pass = "root";
$db = "bakerbest-db";

//create db connection
$conn = new mysqli($host, $user, $pass, $db);

//db connection status
if ($conn->connect_error) {
    die("Db connection failed" . $conn->connect_error);
} else {
    // echo "Db Successfully connected<br>";
}
?>