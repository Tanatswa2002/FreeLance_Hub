<?php
session_start();
require_once("../Configurations/Config_db.php");

$query = "
    SELECT e.escrowPayment_id, u.fname, u.lname, e.amount, e.ep_status, e.payment_due
    FROM escrow_payment e
    LEFT JOIN users u ON e.customer_id = u.user_id
    ORDER BY e.payment_due DESC
";
$result = $conn->query($query);
?>

<h2>Escrow Transactions</h2>
<ul>
<?php while ($row = $result->fetch_assoc()): ?>
    <li><?php echo $row['fname'] . ' ' . $row['lname']; ?> - R<?php echo $row['amount']; ?> (<?php echo $row['ep_status']; ?>)</li>
<?php endwhile; ?>
</ul>
