<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "psv quickbuy"; // Use underscore instead of space

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Failed to connect to the database: " . $conn->connect_error);
}
?>