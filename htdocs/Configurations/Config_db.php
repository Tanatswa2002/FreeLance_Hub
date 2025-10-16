<?php
$host = "sql313.infinityfree.com"; // local MySQL
$username = "if0_39245338";
$password = "zO6sNglzff4v"; // default XAMPP password is empty
$database = "if0_39245338_MarketPlace_DB"; // make sure this DB exists in phpMyAdmin
$port = "3306";

$conn = new mysqli($host, $username, $password, $database, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

