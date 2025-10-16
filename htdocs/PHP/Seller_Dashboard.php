<?php
session_start();
require_once("../Configurations/Config_db.php");

// Redirect if not logged in as buyer or seller (assuming only these two user types)
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_type'] ?? '', ['buyer', 'seller'])) {
    header("Location: Login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$userName = $_SESSION['user_name'] ?? 'Guest';
$userType = $_SESSION['user_type'] ?? 'buyer';

// Handle search
$searchTerm = $_GET['search'] ?? '';
$browse_items = [];

if (!empty($searchTerm)) {
    $search_query = $conn->prepare("
        SELECT i.item_id, i.name, i.price, i.description, i.category, i.product_type, i.image_path, s.seller_id
        FROM items i
        JOIN seller_details s ON i.user_id = s.user_id
        WHERE i.name LIKE ?
    ");
    $likeTerm = "%" . $searchTerm . "%";
    $search_query->bind_param("s", $likeTerm);
    $search_query->execute();
    $browse_items = $search_query->get_result()->fetch_all(MYSQLI_ASSOC);
    $search_query->close();
} else {
    $browse_query = $conn->prepare("
        SELECT i.item_id, i.name, i.price, i.description, i.category, i.product_type, i.image_path, s.seller_id
        FROM items i
        JOIN seller_details s ON i.user_id = s.user_id
        LIMIT 10
    ");
    $browse_query->execute();
    $browse_items = $browse_query->get_result()->fetch_all(MYSQLI_ASSOC);
    $browse_query->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title><?= ucfirst($userType) ?> Dashboard</title>
  <style>
    /* Reset and Base Styles */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Arial', sans-serif;
    }

    body {
      background-color: #f9f9f9;
      color: #333;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    /* Navigation Bar */
    .dashboard-nav {
      display: flex;
      justify-content: center;
      gap: 30px;
      margin-bottom: 40px;
      background-color: #0b3e82;
      padding: 10px 20px;
      align-items: center;
    }

    .dashboard-nav a,
    .dashboard-nav button.dropbtn {
      color: white;
      font-weight: bold;
      text-decoration: none;
      padding: 10px 20px;
      border-radius: 30px;
      background: none;
      border: none;
      cursor: pointer;
      font-size: 1rem;
      position: relative;
      transition: background-color 0.3s ease;
    }

    .dashboard-nav a:hover,
    .dashboard-nav a.active,
    .dashboard-nav button.dropbtn:hover {
      background-color: #164baf;
      color: white;
    }

    .dashboard-nav a.logout-btn {
      margin-left: auto;
      background-color: #0e3b65;
      font-weight: 700;
    }

    /* Dropdown container */
    .dropdown {
      position: relative;
      display: inline-block;
    }

    /* Dropdown content (hidden by default) */
    .dropdown-content {
      display: none;
      position: absolute;
      background-color: white;
      min-width: 160px;
      box-shadow: 0 8px 16px rgba(0,0,0,0.2);
      z-index: 10;
      border-radius: 5px;
      top: 100%;
      left: 0;
    }

    /* Links inside dropdown */
    .dropdown-content a {
      color: #0b3e82;
      padding: 12px 16px;
      text-decoration: none;
      display: block;
      border-bottom: 1px solid #eee;
    }

    .dropdown-content a:last-child {
      border-bottom: none;
    }

    .dropdown-content a:hover {
      background-color: #e6f0ff;
    }

    /* Show dropdown on hover */
    .dropdown:hover .dropdown-content {
      display: block;
    }

    /* Welcome and search styles */
    .header-container {
      max-width: 900px;
      width: 100%;
      margin: 0 auto 30px auto;
      padding: 0 20px;
      box-sizing: border-box;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .welcome-msg {
      font-size: 2rem;
      color: #0b3e82;
      white-space: nowrap;
    }

    /* Search Bar */
    .search-form {
      display: flex;
      justify-content: center;
      gap: 10px;
      margin: 0 auto 30px auto;
      width: 100%;
      max-width: 600px;
    }

    .search-form input[type="text"] {
      flex: 1;
      padding: 12px 16px;
      font-size: 1rem;
      border: 1.5px solid #ccc;
      border-radius: 5px;
    }

    .search-form button {
      padding: 12px 18px;
      background-color: #0b3e82;
      color: white;
      border: none;
      border-radius: 5px;
      font-weight: 600;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .search-form button:hover {
      background-color: #164baf;
    }

    /* Browse Section */
    section {
      width: 100%;
      max-width: 900px;
      background: white;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      margin: 0 auto 50px auto;
    }

    section h2 {
      color: #0b3e82;
      margin-bottom: 20px;
      font-weight: 700;
    }

    /* Item Cards */
    .items-list {
      list-style: none;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 20px;
    }

    .item-card {
      background-color: #f0f4ff;
      border-radius: 8px;
      padding: 20px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.08);
      transition: transform 0.2s ease;
    }

    .item-card:hover {
      transform: translateY(-5px);
    }

    .item-card h3 {
      color: #0b3e82;
      margin-bottom: 10px;
    }

    .item-card p {
      margin-bottom: 8px;
      font-size: 0.95rem;
      color: #444;
    }

    .view-btn {
      display: inline-block;
      margin-top: 10px;
      padding: 8px 14px;
      background-color: #0b3e82;
      color: white;
      text-decoration: none;
      border-radius: 20px;
      font-size: 0.9rem;
      font-weight: bold;
      transition: background-color 0.3s ease;
    }

    .view-btn:hover {
      background-color: #164baf;
    }

    /* Responsive Tweaks */
    @media (max-width: 600px) {
      .search-form {
        flex-direction: column;
        align-items: stretch;
      }
    }
  </style>
</head>
<body>

  <!-- Navigation Bar -->
  <nav class="dashboard-nav">
    <a href="Seller_Dashboard.php" class="active">Home</a>
    <a href="Seller_Dashboard_Profile.php">Profile</a>
    <a href="buyer_cart.php">Cart</a>

    <?php if ($userType === 'seller'): ?>
    <div class="dropdown">
      <button class="dropbtn">Products ▼</button>
      <div class="dropdown-content">
        <a href="Seller_Products_List.php">Update Product</a>
        <a href="Add_Product.php">Add Product</a>
      </div>
    </div>
    <?php endif; ?>

    <a href="Logout.php" class="logout-btn">Logout</a>
  </nav>

  <div class="header-container">
    <h1 class="welcome-msg">Welcome, <?= htmlspecialchars($userName) ?>!</h1>
  </div>

  <!-- Search Bar -->
  <form method="GET" class="search-form" action="">
    <input
      type="text"
      name="search"
      placeholder="Search for services..."
      value="<?= htmlspecialchars($searchTerm) ?>"
      autocomplete="off"
    />
    <button type="submit">Search</button>
  </form>

  <!-- Browse Items -->
  <section>
    <h2>Browse Services</h2>
    <?php if (empty($browse_items)): ?>
      <p>No items found.</p>
    <?php else: ?>
      <ul class="items-list">
        <?php foreach ($browse_items as $item): ?>
          <li class="item-card">
            <?php if (!empty($item['image_path'])): ?>
              <img src="../<?= htmlspecialchars($item['image_path']) ?>" alt="Service Image" style="width: 100%; max-width: 250px; border-radius: 8px; margin-bottom: 10px;">
            <?php endif; ?>
            <h3><?= htmlspecialchars($item['name']) ?></h3>
            <p><strong>Category:</strong> <?= htmlspecialchars($item['category']) ?></p>
            <p><strong>Type:</strong> <?= htmlspecialchars($item['product_type']) ?></p>
            <p><strong>Price:</strong> R<?= htmlspecialchars($item['price']) ?></p>
            <p><?= htmlspecialchars($item['description']) ?></p>
            <a href="view_seller_profile.php?seller_id=<?= $item['seller_id'] ?>" class="view-btn">View Seller</a>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </section>

</body>
</html>
