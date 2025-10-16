<?php
// admin_dashboard_data.php
header('Content-Type: application/json');

$conn = new mysqli("sql313.infinityfree.com", "root", "", "if0_39245338_graduates_dbs");
$data = $conn->query("
  SELECT e.escrowPayment_id, o.order_id, e.amount, o.order_status, e.ep_status, c.customer_confirmed, c.seller_confirmed
  FROM escrow_payment e
  JOIN customer_order o ON o.customer_id = e.customer_id
  JOIN escrow_confirmations c ON c.order_id = o.order_id
");

$results = [];
while ($row = $data->fetch_assoc()) {
  $results[] = $row;
}
echo json_encode($results);
?>
