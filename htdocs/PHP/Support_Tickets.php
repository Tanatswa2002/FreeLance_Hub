<?php
session_start();
require_once("../Configurations/Config_db.php");

$query = "
    SELECT d.dispute_id, u.fname, u.lname, d.dispute_status, d.dispute_date 
    FROM dispute d
    JOIN users u ON d.customer_id = u.user_id
    WHERE d.dispute_status = 'Pending'
";
$result = $conn->query($query);
?>

<h2>Open Disputes (Support Tickets)</h2>
<ul>
<?php while ($row = $result->fetch_assoc()): ?>
    <li><?php echo $row['fname'] . ' ' . $row['lname']; ?> - <?php echo $row['dispute_date']; ?> - <?php echo $row['dispute_status']; ?></li>
<?php endwhile; ?>
</ul>
