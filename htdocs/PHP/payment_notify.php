
<?php
// Validate PayFast IPN (You’d use their SDK or manual logic)
// Assume it’s valid for now
$order_id = $_POST['custom_str1'];
$amount = $_POST['amount_gross'];
$buyer_id = $_POST['custom_str2'];  // optional
$seller_id = $_POST['custom_str3']; // optional

$conn = new mysqli("sql313.infinityfree.com", "root", "", "if0_39245338_graduates_dbs");

// 1. Update order status
$conn->query("UPDATE customer_order SET order_status='Pending Escrow Release' WHERE order_id=$order_id");

// 2. Create escrow record
$conn->query("INSERT INTO escrow_payment (escrow_agent, amount, payment_due, customer_id, seller_id, ep_status)
VALUES ('PayFast', $amount, CURDATE(), $buyer_id, $seller_id, 'Held')");

// 3. Track confirmation status
$conn->query("INSERT INTO escrow_confirmations (order_id) VALUES ($order_id)");
?>
