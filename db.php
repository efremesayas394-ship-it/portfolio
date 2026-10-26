<?php
$host = "localhost";
$user = "root";
$pass = ""; // Your database password
$dbname = "portfolio_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>