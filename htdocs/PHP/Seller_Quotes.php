<?php
session_start();
require_once '../Configurations/Config_db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seller') {
    echo "Unauthorized access";
    exit();
}

$seller_id = $_SESSION['user_id'];

// Fetch all pending quote requests for this seller
$sql = "SELECT * FROM quote_requests WHERE seller_id = ? AND status = 'pending' ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $seller_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quote Requests</title>
    <style>
        body { font-family: Arial; }
        .quote-box { border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; }
        textarea, input, button { width: 100%; margin-top: 5px; }
    </style>
</head>
<body>
    <h2>Quote Requests</h2>

    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="quote-box">
            <p><strong>Title:</strong> <?= htmlspecialchars($row['title']) ?></p>
            <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($row['description'])) ?></p>
            <p><strong>Category:</strong> <?= htmlspecialchars($row['category']) ?></p>
            <p><strong>Due Date:</strong> <?= htmlspecialchars($row['due_date']) ?></p>

            <form action="Send_Quote.php" method="post">
                <input type="hidden" name="request_id" value="<?= $row['request_id'] ?>">
                <label for="breakdown">Price Breakdown:</label>
                <textarea name="breakdown" required></textarea>
                
                <label for="total_price">Total Price (R):</label>
                <input type="number" name="total_price" step="0.01" required>
                
                <button type="submit">Send Quote</button>
            </form>
        </div>
    <?php endwhile; ?>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
