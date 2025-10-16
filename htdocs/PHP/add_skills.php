<?php
session_start();
require_once('../Configurations/Config_db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get seller_id for the logged-in user
$seller_id = null;
$stmt = $conn->prepare("SELECT seller_id FROM seller_details WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($seller_id);
$stmt->fetch();
$stmt->close();

if (!$seller_id) {
    $_SESSION['error'] = "You are not registered as a seller.";
    header("Location: Seller_Dashboard.php");
    exit();
}

$skills_input = trim($_POST['skills'] ?? '');

if (!$skills_input) {
    $_SESSION['error'] = "Please enter skills.";
    header("Location: Seller_Dashboard.php");
    exit();
}

// Split skills by comma, trim, and insert if not exist
$skills = array_map('trim', explode(',', $skills_input));

foreach ($skills as $skill_title) {
    if ($skill_title === '') continue;

    // Check if skill exists
    $stmt = $conn->prepare("SELECT skills_id FROM skills WHERE title = ?");
    $stmt->bind_param("s", $skill_title);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($skills_id);
        $stmt->fetch();
        $stmt->close();
    } else {
        $stmt->close();
        // Insert new skill
        $stmt = $conn->prepare("INSERT INTO skills (title) VALUES (?)");
        $stmt->bind_param("s", $skill_title);
        $stmt->execute();
        $skills_id = $stmt->insert_id;
        $stmt->close();
    }

    // Link skill to seller if not already linked
    $stmt = $conn->prepare("SELECT 1 FROM seller_skills WHERE seller_id = ? AND skills_id = ?");
    $stmt->bind_param("ii", $seller_id, $skills_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO seller_skills (seller_id, skills_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $seller_id, $skills_id);
        $stmt->execute();
    }
    $stmt->close();
}

$_SESSION['success'] = "Skills added successfully.";
header("Location: Seller_Dashboard.php");
exit();
?>
