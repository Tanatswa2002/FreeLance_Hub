<?php
session_start();
require_once '../Configurations/Config_db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seller') {
    echo "Unauthorized access";
    exit();
}

$seller_id = $_SESSION['user_id'];
$request_id = $_POST['request_id'];
$breakdown = trim($_POST['breakdown'] ?? '');
$total_price = $_POST['total_price'];

if (!$request_id || !$breakdown || !$total_price) {
    echo "Missing fields.";
    exit();
}

// 1. Insert quote
$stmt = $conn->prepare("INSERT INTO quotes (request_id, seller_id, breakdown, total_price) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iisd", $request_id, $seller_id, $breakdown, $total_price);
$stmt->execute();
$stmt->close();

// 2. Update status of request
$update = $conn->prepare("UPDATE quote_requests SET status = 'quoted' WHERE request_id = ?");
$update->bind_param("i", $request_id);
$update->execute();
$update->close();

echo "Quote sent successfully.";
$conn->close();
?>
