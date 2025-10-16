<?php
session_start();
header('Content-Type: application/json');

require_once("../Configurations/Config_db.php");

// Check if user is logged in as buyer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'buyer') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access. Please log in as a buyer.']);
    exit();
}

if (!isset($_GET['product_id']) || !is_numeric($_GET['product_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid product ID.']);
    exit();
}

$item_id = intval($_GET['product_id']);
$customer_id = $_SESSION['user_id'];

// Get current price of the product
$sql_price = "SELECT price FROM items WHERE item_id = ?";
$stmt = $conn->prepare($sql_price);
$stmt->bind_param("i", $item_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Product not found.']);
    exit();
}

$row = $result->fetch_assoc();
$current_price = floatval($row['price']);
$stmt->close();

// Check if item already in cart
$sql_check = "SELECT quantity FROM cart WHERE user_id = ? AND item_id = ?";
$stmt = $conn->prepare($sql_check);
$stmt->bind_param("ii", $customer_id, $item_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Update quantity and net_total
    $existing = $result->fetch_assoc();
    $new_quantity = $existing['quantity'] + 1;
    $new_total = $new_quantity * $current_price;

    $stmt->close();

    $sql_update = "UPDATE cart SET quantity = ?, net_total = ?, price_at_time = ? WHERE user_id = ? AND item_id = ?";
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param("iddii", $new_quantity, $new_total, $current_price, $customer_id, $item_id);
    $stmt->execute();

    echo json_encode(['message' => 'Product quantity updated in cart.']);
} else {
    // Insert new row with quantity = 1
    $quantity = 1;
    $net_total = $current_price * $quantity;

    $stmt->close();

    $sql_insert = "INSERT INTO cart (user_id, item_id, quantity, net_total, price_at_time) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql_insert);
    $stmt->bind_param("iiidd", $customer_id, $item_id, $quantity, $net_total, $current_price);
    $stmt->execute();

    echo json_encode(['message' => 'Product added to cart.']);
}

$stmt->close();
$conn->close();
?>
