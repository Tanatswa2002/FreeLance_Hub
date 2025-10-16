<?php
session_start();
require_once('../Configurations/Config_db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$title = trim($_POST['title'] ?? '');
$company = trim($_POST['company'] ?? '');
$start_date = $_POST['start_date'] ?? null;
$end_date = $_POST['end_date'] ?? null;

if (!$title || !$company || !$start_date) {
    $_SESSION['error'] = "Please fill all required experience fields.";
    header("Location: Seller_Dashboard.php");
    exit();
}

// Insert into experience table
$stmt = $conn->prepare("INSERT INTO experience (title, company, start_date, end_date) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $title, $company, $start_date, $end_date);
if (!$stmt->execute()) {
    $_SESSION['error'] = "Failed to add experience.";
    $stmt->close();
    header("Location: Seller_Dashboard.php");
    exit();
}

$experience_id = $stmt->insert_id;
$stmt->close();

// Link experience to user
$stmt2 = $conn->prepare("INSERT INTO user_experience (user_id, experience_id) VALUES (?, ?)");
$stmt2->bind_param("ii", $user_id, $experience_id);
if ($stmt2->execute()) {
    $_SESSION['success'] = "Experience added successfully.";
} else {
    $_SESSION['error'] = "Failed to link experience.";
}
$stmt2->close();

header("Location: Seller_Dashboard.php");
exit();
?>
<?php
session_start();
require_once('../Configurations/Config_db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$title = trim($_POST['title'] ?? '');
$company = trim($_POST['company'] ?? '');
$start_date = $_POST['start_date'] ?? null;
$end_date = $_POST['end_date'] ?? null;

if (!$title || !$company || !$start_date) {
    $_SESSION['error'] = "Please fill all required experience fields.";
    header("Location: Seller_Dashboard.php");
    exit();
}

// Insert into experience table
$stmt = $conn->prepare("INSERT INTO experience (title, company, start_date, end_date) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $title, $company, $start_date, $end_date);
if (!$stmt->execute()) {
    $_SESSION['error'] = "Failed to add experience.";
    $stmt->close();
    header("Location: Seller_Dashboard.php");
    exit();
}

$experience_id = $stmt->insert_id;
$stmt->close();

// Link experience to user
$stmt2 = $conn->prepare("INSERT INTO user_experience (user_id, experience_id) VALUES (?, ?)");
$stmt2->bind_param("ii", $user_id, $experience_id);
if ($stmt2->execute()) {
    $_SESSION['success'] = "Experience added successfully.";
} else {
    $_SESSION['error'] = "Failed to link experience.";
}
$stmt2->close();

header("Location: Seller_Dashboard.php");
exit();
?>
