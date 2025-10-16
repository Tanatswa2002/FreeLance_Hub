
<?php
session_start();
require_once("../Configurations/Config_db.php");

// Redirect if not logged in as buyer
if (!isset($_SESSION['user_id']) || ($_SESSION['user_type'] ?? '') !== 'buyer') {
    header("Location: Login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$userName = $_SESSION['user_name'] ?? 'Guest';

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
  <title>Buyer Dashboard</title>
  <link rel="stylesheet" href="../CSS/buyer_dashboard.css" />
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
.buyer-dashboard-nav {
  display: flex;
  justify-content: center;
  gap: 30px;
  margin-bottom: 40px;
}

.buyer-dashboard-nav a {
  text-decoration: none;
  color: #0b3e82;
  font-weight: bold;
  padding: 10px 20px;
  border-radius: 30px;
  transition: background-color 0.3s ease;
}

.buyer-dashboard-nav a:hover,
.buyer-dashboard-nav a.active {
  background-color: #0b3e82;
  color: white;
}

.top{
    display:flex;
    align-items:left;
    text-align:left;
    gap:400px;
    margin-left:0px;
}

/* Header */
h1 {
  margin-bottom: 30px;
  font-size: 2rem;
  color: #0b3e82;
}

/* Search Bar */
.search-form {
  display: flex;
  justify-content: center;
  gap: 10px;
  margin-bottom: 30px;
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


    .header-container {
      display: flex;
      gap:200px;
      justify-content: space-between;
      align-items: center;
      max-width: 900px;
      width: 100%;
      margin-bottom: 30px;
      padding: 0 20px;
      box-sizing: border-box;
    }

    .welcome-msg {
      font-size: 2rem;
      color: #0b3e82;
      text-align:left;
      white-space: nowrap;       /* don't break text */
    

      
    }

    .buyer-dashboard-nav {
      display: flex;
      gap: 20px;
    }

    .buyer-dashboard-nav a {
      text-decoration: none;
      color: #0b3e82;
      font-weight: bold;
      padding: 10px 20px;
      border-radius: 30px;
      transition: background-color 0.3s ease;
    }

    .buyer-dashboard-nav a:hover,
    .buyer-dashboard-nav a.active {
      background-color: #0b3e82;
      color: white;
    }

    .buyer-dashboard-nav a.logout-btn {
      color: #0e3b65;
      font-weight: 700;
    }
  </style>
</head>
<body>

  <!-- Header with Welcome Left + Nav Right -->
  <header class="header-container">
  <div class = "top">
    <h1 class="welcome-msg">Welcome, <?= htmlspecialchars($userName) ?> !</h1>
    <nav class="buyer-dashboard-nav">
      <a href="buyer_dashboard.php" class="active">Home</a>
      <a href="buyer_profile.php">Profile</a>
      <a href="buyer_cart.php">Cart</a>
      <a href="Logout.php" class="logout-btn">Logout</a>
    </nav>
    </div>
  </header>

  <!-- Search Bar -->
  <form method="GET" class="search-form">
    <input
      type="text"
      name="search"
      placeholder="Search for services..."
      value="<?= htmlspecialchars($searchTerm) ?>"
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

