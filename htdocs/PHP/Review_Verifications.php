<?php
require_once 'config_db.php'; // Include DB config

// Handle Approve/Reject
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $verification_id = $_POST['verification_id'];
    $new_status = $_POST['action'] === 'approve' ? 'Approved' : 'Rejected';

    $update_sql = "UPDATE verification SET verification_status = ? WHERE verification_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("si", $new_status, $verification_id);
    $stmt->execute();
}

// Fetch verifications
$sql = "SELECT v.verification_id, v.seller_id, v.verification_status, u.fname, u.lname 
        FROM verification v
        JOIN seller_details s ON v.seller_id = s.seller_id
        JOIN users u ON s.user_id = u.user_id
        WHERE v.verification_status IS NULL OR v.verification_status = 'Pending'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Review Verifications</title>
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: center; }
        form { display: inline; }
        button { padding: 5px 10px; margin: 2px; }
    </style>
</head>
<body>
    <h2>Pending Verifications</h2>

    <table>
        <thead>
            <tr>
                <th>Seller Name</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['fname'] . ' ' . $row['lname']) ?></td>
                    <td><?= htmlspecialchars($row['verification_status'] ?? 'Pending') ?></td>
                    <td>
                        <a href="View_Profile.php?seller_id=<?= $row['seller_id'] ?>">
                            <button type="button">View Profile</button>
                        </a>
                        <form method="post">
                            <input type="hidden" name="verification_id" value="<?= $row['verification_id'] ?>">
                            <button type="submit" name="action" value="approve">Approve</button>
                            <button type="submit" name="action" value="reject">Reject</button>

                            //Confirm verification
                            <form method="post" onsubmit="return confirm('Are you sure you want to APPROVE this verification?');">
                                    <input type="hidden" name="verification_id" value="<?= $row['verification_id'] ?>">
                                    <button type="submit" name="action" value="approve">Approve</button>
                                </form>

                                <form method="post" onsubmit="return confirm('Are you sure you want to REJECT this verification?');">
                                    <input type="hidden" name="verification_id" value="<?= $row['verification_id'] ?>">
                                    <button type="submit" name="action" value="reject">Reject</button>
                                </form>

                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="3">No pending verifications</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
