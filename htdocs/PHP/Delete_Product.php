<?php
session_start();
require_once("../Configurations/Config_db.php");
header('Content-Type: application/json');

// Ensure seller is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seller') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access.']);
    exit();
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'] ?? null;

if (!$product_id || !is_numeric($product_id)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid product ID.']);
    exit();
}

// Check ownership and get image path (if any)
$sql_check = "SELECT image_path, product_type FROM items WHERE item_id = ? AND user_id = ?";
$stmt = $conn->prepare($sql_check);
$stmt->bind_param("ii", $product_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(403);
    echo json_encode(['error' => 'You are not authorized to delete this product.']);
    exit();
}

$row = $result->fetch_assoc();
$image_path = $row['image_path'] ?? null;
$product_type = $row['product_type'] ?? null;

// Delete product from DB
$sql_delete = "DELETE FROM items WHERE item_id = ? AND user_id = ?";
$stmt = $conn->prepare($sql_delete);
$stmt->bind_param("ii", $product_id, $user_id);

if ($stmt->execute()) {
    // Optionally delete image if it's a 'good' and image exists
    if ($product_type === 'good' && $image_path && file_exists($image_path)) {
        unlink($image_path); // delete image from server
    }
    echo json_encode(['message' => 'Product deleted successfully.']);
} else {
    echo json_encode(['error' => 'Failed to delete product.']);
}

$stmt->close();
$conn->close();
?>
