<?php
session_start();
require_once("../Configurations/Config_db.php");

if (!isset($_SESSION['user_id']) || ($_SESSION['user_type'] ?? '') !== 'buyer') {
    header("Location: Login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $phone_num = trim($_POST['phone_num']);
    $about_me = trim($_POST['about_me']);

    $update = $conn->prepare("UPDATE users SET phone_num = ?, about_me = ?, updated_at = NOW() WHERE user_id = ?");
    $update->bind_param("ssi", $phone_num, $about_me, $user_id);
    $update->execute();
    $update->close();
}

// Re-fetch updated data
$stmt = $conn->prepare("SELECT fname, lname, email, phone_num, about_me FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Buyer Profile</title>
  <link rel="stylesheet" href="../CSS/Buyer_Profile.css" />
</head>
<body>
  <nav class="buyer-dashboard-nav">
    <a href="buyer_dashboard.php">Home</a>
    <a href="buyer_profile.php" class="active">Profile</a>
    <a href="buyer_cart.php">Cart</a>
  </nav>

  <h1>My Profile</h1>

  <section>
    <form method="POST">
      <p><strong>Name:</strong> <?= htmlspecialchars($profile['fname'] . ' ' . $profile['lname']) ?></p>
      <p><strong>Email:</strong> <?= htmlspecialchars($profile['email']) ?></p>

      <label for="phone_num"><strong>Phone Number:</strong></label><br>
      <input type="text" id="phone_num" name="phone_num" value="<?= htmlspecialchars($profile['phone_num']) ?>" required><br><br>

      <label for="about_me"><strong>About Me:</strong></label><br>
      <textarea id="about_me" name="about_me" rows="4" cols="50"><?= htmlspecialchars($profile['about_me']) ?></textarea><br><br>

      <button type="submit">Update Profile</button>
    </form>
  </section>
</body>
</html>
