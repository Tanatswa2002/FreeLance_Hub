<?php
session_start();
require_once("../Configurations/Config_db.php");

// Only allow sellers
if (!isset($_SESSION['user_id']) || ($_SESSION['user_type'] ?? '') !== 'seller') {
    echo "Unauthorized access";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];

    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $product_type = $_POST['product_type'] ?? '';
    $image_path = null;

    // Handle image upload if product_type is good
    if ($product_type === 'good' && isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $target_dir = "../uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        $file_name = time() . "_" . basename($_FILES['image']['name']);
        $target_file = $target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($imageFileType, $allowed_types)) {
            die("Invalid image type. Only JPG, JPEG, PNG, GIF allowed.");
        }

        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            die("Failed to upload image.");
        }

        $image_path = "uploads/" . $file_name;
    }

    // Insert into DB
    $stmt = $conn->prepare("INSERT INTO items (user_id, name, description, price, category, product_type, image_path) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issdsss", $user_id, $name, $description, $price, $category, $product_type, $image_path);

    if ($stmt->execute()) {
        echo "<p style='color: green; font-weight: bold; text-align:center;'>Product successfully added!</p>";
        echo "<p style='text-align:center;'>Redirecting to dashboard...</p>";
        header("refresh:2; url=Seller_Dashboard.php");
        exit();
    } else {
        echo "<p style='color: red; font-weight: bold; text-align:center;'>Error adding product: " . htmlspecialchars($stmt->error) . "</p>";
    }

    $stmt->close();
    $conn->close();
}
?>

<form action="Add_Product.php" method="POST" enctype="multipart/form-data" style="max-width:500px; margin:auto; background:#f5f5f5; padding:20px; border-radius:10px;">
  <h2 style="color:#0b3e82;">Add New Product</h2>

  <label>Name:</label><br>
  <input type="text" name="name" required><br><br>

  <label>Description:</label><br>
  <textarea name="description" required></textarea><br><br>

  <label>Category:</label><br>
  <input type="text" name="category" required><br><br>

  <label>Price (R):</label><br>
  <input type="number" step="0.01" name="price" required><br><br>

  <label>Product Type:</label><br>
  <select name="product_type" required>
    <option value="good">Good</option>
    <option value="service">Service</option>
  </select><br><br>

  <label>Image (only for goods):</label><br>
  <input type="file" name="image" accept="image/*"><br><br>

  <button type="submit" style="background:#0b3e82; color:white; padding:10px 20px; border:none; border-radius:5px;">Add Product</button>
</form>
