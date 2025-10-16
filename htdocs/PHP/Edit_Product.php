<?php
session_start();
require_once('../Configurations/Config_db.php');

// Ensure seller is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seller') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

$seller_id = $_SESSION['user_id'];

// Validate POST data
$product_id     = $_POST['product_id'] ?? null;
$name           = $_POST['name'] ?? null;
$price          = $_POST['price'] ?? null;
$description    = $_POST['description'] ?? null;
$product_type   = $_POST['product_type'] ?? null;

if (!$product_id || !$name || !$price || !$description || !$product_type) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit();
}

// ✅ Check if this seller owns the product
$check_sql = "SELECT user_id FROM items WHERE item_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $product_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Product not found']);
    exit();
}

$owner = $check_result->fetch_assoc();
if ($owner['user_id'] != $seller_id) {
    http_response_code(403);
    echo json_encode(['error' => 'You are not the owner of this product']);
    exit();
}

// Optional image upload for goods
$image_path = null;
if ($product_type === 'good' && isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
    $target_dir = "../uploads/";
    $file_name = time() . "_" . basename($_FILES['image']['name']);
    $target_file = $target_dir . $file_name;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($imageFileType, $allowedTypes)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid image type']);
        exit();
    }

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to upload image']);
        exit();
    }

    $image_path = $target_file;
}

// Update product info
if ($image_path) {
    $sql = "UPDATE items SET name=?, description=?, price=?, product_type=?, image_path=? WHERE item_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdssi", $name, $description, $price, $product_type, $image_path, $product_id);
} else {
    $sql = "UPDATE items SET name=?, description=?, price=?, product_type=? WHERE item_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdsi", $name, $description, $price, $product_type, $product_id);
}

if ($stmt->execute()) {
    echo json_encode(['message' => 'Product updated successfully']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to update product']);
}

$stmt->close();
$conn->close();
?>
