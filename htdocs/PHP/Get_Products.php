<?php
session_start();
header('Content-Type: application/json');

require_once("../Configurations/Config_db.php");

// Check if user is logged in and is a buyer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'buyer') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access. Please log in as a buyer.']);
    exit();
}

// Fetch products from the database
$sql = 'SELECT item_id, name, description, price FROM items';
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = [
            'item_id' => $row['item_id'],
            'name' => $row['name'],
            'description' => $row['description'],
            'price' => $row['price']
        ];
    }
    echo json_encode(['products' => $products]);
} else {
    echo json_encode(['message' => 'No products found.']);
}

$conn->close();
?>
