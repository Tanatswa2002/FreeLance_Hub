<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once("../Configurations/Config_db.php");

// Ensure user is logged in and either buyer or seller
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_type'], ['buyer', 'seller'])) {
    echo "Unauthorized access";
    exit();
}

// Get and sanitize inputs
$requester_id = $_SESSION['user_id'];
$requester_role = $_SESSION['user_type'];
$seller_id = $_POST['seller_id'];
$title = trim($_POST['task_title']);
$description = trim($_POST['task_description']);
$date = $_POST['preferred_date'];

// Prevent users from hiring themselves
if ($requester_id == $seller_id) {
    echo "You cannot hire yourself.";
    exit();
}

// Insert hire request
$stmt = $conn->prepare("INSERT INTO hire_requests (buyer_id, seller_id, task_title, task_description, preferred_date) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("iisss", $requester_id, $seller_id, $title, $description, $date);

if ($stmt->execute()) {
    // Redirect to mock payment page
    header("Location: mock_payment.php?amount=500&ref=hire_" . $stmt->insert_id);
    exit();
} else {
    echo "Error processing hire request: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
