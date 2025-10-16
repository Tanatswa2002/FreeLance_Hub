<?php
session_start();
require_once '../Configurations/Config_db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seller') {
    echo "Unauthorized access";
    exit();
}

$item_id = $_POST['item_id'];
$name = $_POST['name'];
$description = $_POST['description'];
$price = $_POST['price'];
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("UPDATE items SET name = ?, description = ?, price = ? WHERE item_id = ? AND user_id = ?");
$stmt->bind_param("ssdii", $name, $description, $price, $item_id, $user_id);

if ($stmt->execute()) {
    echo "Listing updated successfully.";
    header("Location: Manage_Listings.php");
} else {
    echo "Failed to update listing.";
}
$stmt->close();
$conn->close();
?>