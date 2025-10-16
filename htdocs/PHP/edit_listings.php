<?php

session_start();

require_once '../Configurations/Config_db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'seller') {
    echo "Unauthorized access";
    exit();
}

$item_id = $_GET['id'] ?? null;
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM items WHERE item_id = ? AND user_id = ?");
$stmt->bind_param("ii", $item_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();

if (!$item) {
    echo "Item not found or access denied.";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head><title>Edit Listing</title>
<style>
/* General page styling */
body {
  background-color: #f9f9f9;
  font-family: Arial, sans-serif;
  color: #2c3e50;
  padding: 40px 20px;
  display: flex;
  justify-content: center;
}

/* Form container */
form {
  background-color: #fff;
  padding: 30px 40px;
  border-radius: 12px;
  box-shadow: 0 8px 20px rgba(11, 62, 130, 0.1);
  width: 100%;
  max-width: 500px;
}

/* Heading */
h2 {
  color: #0b3e82;
  margin-bottom: 25px;
  font-weight: 700;
  text-align: center;
}

/* Labels */
label {
  display: block;
  margin-bottom: 8px;
  font-weight: 600;
  font-size: 1rem;
  color: #0b3e82;
}

/* Input fields and textarea */
input[type="text"],
input[type="number"],
textarea {
  width: 100%;
  padding: 10px 12px;
  margin-bottom: 20px;
  border: 2px solid #0b3e82;
  border-radius: 6px;
  font-size: 1rem;
  resize: vertical;
  transition: border-color 0.3s ease;
}

input[type="text"]:focus,
input[type="number"]:focus,
textarea:focus {
  border-color: #164baf;
  outline: none;
}

/* Textarea height */
textarea {
  min-height: 100px;
}

/* Submit button */
button {
  width: 100%;
  background-color: #0b3e82;
  color: white;
  border: none;
  padding: 12px 0;
  border-radius: 25px;
  font-size: 1.1rem;
  font-weight: 700;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

button:hover {
  background-color: #164baf;
}
</style>
</head>
<body>


<h2>Edit Listing</h2>
<form action="Seller_Dashboard.php" method="post">
    <input type="hidden" name="item_id" value="<?= $item['item_id'] ?>">
    Name: <input type="text" name="name" value="<?= htmlspecialchars($item['name']) ?>"><br><br>
    Description: <textarea name="description"><?= htmlspecialchars($item['description']) ?></textarea><br><br>
    Price: <input type="number" step="0.01" name="price" value="<?= $item['price'] ?>"><br><br>
    <button type="submit">Update</button>
</form>
</body>
</html>