<?php
session_start();
require_once '../Configurations/Config_db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seller') {
    echo "Unauthorized access";
    exit();
}

$item_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("DELETE FROM items WHERE item_id = ? AND user_id = ?");
$stmt->bind_param("ii", $item_id, $user_id);

if ($stmt->execute()) {
    echo "Listing deleted successfully.";
} else {
    echo "Failed to delete listing.";
}
$stmt->close();
$conn->close();
header("Location: Manage_Listings.php");
exit();
?>