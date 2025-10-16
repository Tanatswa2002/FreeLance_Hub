<?php
session_start();
require_once '../Configurations/Config_db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'buyer') {
    echo "Unauthorized access";
    exit();
}

$buyer_id = $_SESSION['user_id'];
$seller_id = $_POST['seller_id'] ?? null;
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$category = trim($_POST['category'] ?? '');
$due_date = $_POST['due_date'] ?? '';

if (!$seller_id || !$title || !$due_date) {
    echo "Missing required fields.";
    exit();
}

$stmt = $conn->prepare("INSERT INTO quote_requests (buyer_id, seller_id, title, description, category, due_date) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iissss", $buyer_id, $seller_id, $title, $description, $category, $due_date);

if ($stmt->execute()) {
    echo "Quote request sent successfully!";
} else {
    echo "Error sending request.";
}

$stmt->close();
$conn->close();
?>
