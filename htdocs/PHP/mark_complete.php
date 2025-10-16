<?php
session_start();
$user_id = $_SESSION['user_id'];
$order_id = $_POST['order_id'];
$role = $_SESSION['role']; // 'buyer' or 'seller'

$conn = new mysqli("sql313.infinityfree.com", "root", "", "if0_39245338_graduates_dbs");

if ($role == 'buyer') {
  $conn->query("UPDATE escrow_confirmations SET customer_confirmed=1 WHERE order_id=$order_id");
} else if ($role == 'seller') {
  $conn->query("UPDATE escrow_confirmations SET seller_confirmed=1 WHERE order_id=$order_id");
}

// Check if both confirmed
$res = $conn->query("SELECT * FROM escrow_confirmations WHERE order_id=$order_id");
$row = $res->fetch_assoc();

if ($row['customer_confirmed'] && $row['seller_confirmed']) {
  // Release funds
  $conn->query("UPDATE escrow_payment SET ep_status='Released', release_date=NOW() WHERE customer_id=$user_id");
  $conn->query("UPDATE customer_order SET order_status='Completed' WHERE order_id=$order_id");
}
?>
