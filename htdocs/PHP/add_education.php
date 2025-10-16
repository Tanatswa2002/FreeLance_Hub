<?php
session_start();
require_once('../Configurations/Config_db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$school = trim($_POST['school'] ?? '');
$degree = trim($_POST['degree'] ?? '');
$field_of_study = trim($_POST['field_of_study'] ?? '');
$start_date = $_POST['start_date'] ?? null;
$end_date = $_POST['end_date'] ?? null;

if (!$school || !$degree || !$field_of_study || !$start_date) {
    $_SESSION['error'] = "Please fill all required education fields.";
    header("Location: Seller_Dashboard.php");
    exit();
}

// Insert into education table
$stmt = $conn->prepare("INSERT INTO education (school, degree, field_of_study, start_date, end_date) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $school, $degree, $field_of_study, $start_date, $end_date);
if (!$stmt->execute()) {
    $_SESSION['error'] = "Failed to add education.";
    $stmt->close();
    header("Location: Seller_Dashboard.php");
    exit();
}

$education_id = $stmt->insert_id;
$stmt->close();

// Link education to user
$stmt2 = $conn->prepare("INSERT INTO user_education (user_id, education_id) VALUES (?, ?)");
$stmt2->bind_param("ii", $user_id, $education_id);
if ($stmt2->execute()) {
    $_SESSION['success'] = "Education added successfully.";
} else {
    $_SESSION['error'] = "Failed to link education.";
}
$stmt2->close();

header("Location: Seller_Dashboard.php");
exit();
?>
