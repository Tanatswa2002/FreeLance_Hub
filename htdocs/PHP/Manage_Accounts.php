<?php
session_start();
require_once("../Configurations/Config_db.php");

// Restrict to admin only
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: Login.php");
    exit();
}

$query = "SELECT user_id, fname, lname, email, role FROM users";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>User Accounts</title>
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background-color: #f5f8fc;
      padding: 40px 20px;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    h2 {
      color: #0b3e82;
      font-size: 2rem;
      margin-bottom: 30px;
    }

    .accounts-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 20px;
      width: 100%;
      max-width: 1000px;
    }

    .account-card {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
      transition: transform 0.2s ease;
    }

    .account-card:hover {
      transform: translateY(-5px);
    }

    .account-card h3 {
      margin: 0 0 10px;
      color: #0b3e82;
      font-size: 1.2rem;
    }

    .account-card p {
      margin: 4px 0;
      font-size: 0.95rem;
      color: #333;
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
  </style>
</head>
<body>

  <h2>User Accounts</h2>

  <div class="accounts-container">
    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="account-card">
        <h3><?= htmlspecialchars($row['fname'] . ' ' . $row['lname']) ?></h3>
        <p><strong>Role:</strong> <?= htmlspecialchars($row['role']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($row['email']) ?></p>
        <a href="admin_view.php?user_id=<?= urlencode($row['user_id']) ?>" class="view-btn">View Profile</a>
      </div>
    <?php endwhile; ?>
  </div>

</body>
</html>
