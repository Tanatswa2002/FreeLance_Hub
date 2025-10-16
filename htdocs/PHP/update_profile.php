<?php
session_start();
require_once('../Configurations/Config_db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$about_me = trim($_POST['about_me'] ?? '');

if ($about_me === '') {
    $_SESSION['error'] = "About Me cannot be empty.";
    header("Location: Seller_Dashboard.php");
    exit();
}

$stmt = $conn->prepare("UPDATE users SET about_me = ? WHERE user_id = ?");
$stmt->bind_param("si", $about_me, $user_id);
if ($stmt->execute()) {
    $_SESSION['success'] = "Profile updated successfully.";
} else {
    $_SESSION['error'] = "Failed to update profile.";
}
$stmt->close();

header("Location: Seller_Dashboard.php");
exit();
?>
