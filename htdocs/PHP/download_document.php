<?php
require_once 'config_db.php';

$seller_id = $_GET['seller_id'] ?? 0;

$sql = "SELECT supporting_docs FROM verification WHERE seller_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $seller_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc() && $row['supporting_docs']) {
    header("Content-Type: application/pdf"); // You can customize based on actual doc type
    header("Content-Disposition: inline; filename=document.pdf");
    echo $row['supporting_docs'];
} else {
    echo "No document found.";
}
?>
