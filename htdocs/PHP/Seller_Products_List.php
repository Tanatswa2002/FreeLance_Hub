<?php
session_start();
require_once("../Configurations/Config_db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'seller') {
    echo "Unauthorized access";
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch all products by this seller only
$stmt = $conn->prepare("SELECT item_id, name, price FROM items WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$results = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head><title>My Products</title>
<style>
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
  font-family: Arial, sans-serif;
  color: #2c3e50;
}

body {
  background-color: #f5f7fa;
  padding: 30px;
}

h1 {
  color: #0b3e82;
  margin-bottom: 20px;
  font-weight: 700;
}

ul {
  list-style: none;
  max-width: 600px;
  margin: 0 auto;
  padding: 0;
}

li {
  background: #ffffff;
  padding: 15px 20px;
  margin-bottom: 15px;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgb(11 62 130 / 0.1);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

li:hover {
  box-shadow: 0 4px 14px rgb(11 62 130 / 0.2);
}

a {
  text-decoration: none;
  color: #0b3e82;
  font-weight: 600;
  border: 2px solid #0b3e82;
  padding: 6px 14px;
  border-radius: 20px;
  transition: background-color 0.3s ease, color 0.3s ease;
}

a:hover {
  background-color: #0b3e82;
  color: #fff;
}
</style>

</head>
<body>
<h1>Your Products</h1>
<?php if ($results->num_rows === 0): ?>
    <p>You have no products listed yet.</p>
<?php else: ?>
    <ul>
        <?php while ($product = $results->fetch_assoc()): ?>
            <li>
                <?= htmlspecialchars($product['name']) ?> — R<?= htmlspecialchars($product['price']) ?>
                <a href="edit_listings.php?id=<?= $product['item_id'] ?>">Edit</a>
            </li>
        <?php endwhile; ?>
    </ul>
<?php endif; ?>
</body>
</html>
