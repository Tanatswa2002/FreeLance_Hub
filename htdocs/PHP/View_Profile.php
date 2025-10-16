<?php
session_start();
require_once("../Configurations/Config_db.php");

// Get item_id from URL
$item_id = $_GET['item_id'] ?? null;
if (!$item_id) {
    echo "No item specified.";
    exit();
}

// Fetch item details and seller_id
$stmt = $conn->prepare("SELECT name, product_type, user_id FROM items WHERE item_id = ?");
$stmt->bind_param("i", $item_id);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$item) {
    echo "Item not found.";
    exit();
}

// Fetch seller_id from seller_details table (assuming user_id is seller)
$seller_id_query = $conn->prepare("SELECT seller_id FROM seller_details WHERE user_id = ?");
$seller_id_query->bind_param("i", $item['user_id']);
$seller_id_query->execute();
$seller_result = $seller_id_query->get_result();
$seller_data = $seller_result->fetch_assoc();
$seller_id_query->close();

if (!$seller_data) {
    echo "Seller not found.";
    exit();
}
$seller_id = $seller_data['seller_id'];

// Fetch seller basic info (user info linked to seller)
$stmt = $conn->prepare("
  SELECT u.fname, u.lname, u.email, u.phone_num, u.about_me, u.profile_image
  FROM users u
  JOIN seller_details s ON u.user_id = s.user_id
  WHERE s.seller_id = ?");
$stmt->bind_param("i", $seller_id);
$stmt->execute();
$seller_info = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$seller_info) {
    echo "Seller info not found.";
    exit();
}

// Fetch seller skills
$stmt = $conn->prepare("
  SELECT s.title 
  FROM seller_skills ss 
  JOIN skills s ON ss.skills_id = s.skills_id 
  WHERE ss.seller_id = ?");
$stmt->bind_param("i", $seller_id);
$stmt->execute();
$skills = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch seller education
$stmt = $conn->prepare("
  SELECT e.degree, e.field_of_study, e.school, e.start_date, e.end_date
  FROM education e
  JOIN user_education ue ON e.education_id = ue.education_id
  JOIN users u ON ue.user_id = u.user_id
  JOIN seller_details sd ON sd.user_id = u.user_id
  WHERE sd.seller_id = ?");
$stmt->bind_param("i", $seller_id);
$stmt->execute();
$educations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch seller experience
$stmt = $conn->prepare("
  SELECT ex.title, ex.company, ex.start_date, ex.end_date
  FROM experience ex
  JOIN user_experience ue ON ex.experience_id = ue.experience_id
  JOIN users u ON ue.user_id = u.user_id
  JOIN seller_details sd ON sd.user_id = u.user_id
  WHERE sd.seller_id = ?");
$stmt->bind_param("i", $seller_id);
$stmt->execute();
$experiences = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Seller Profile</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 40px; background: #f9f9f9; color: #333; }
    section { background: white; padding: 20px; margin-bottom: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);}
    h1, h2 { color: #0b3e82; }
    ul { list-style-type: disc; margin-left: 20px; }
    .profile-image {
      max-width: 150px;
      max-height: 150px;
      border-radius: 50%;
      object-fit: cover;
      margin-bottom: 20px;
      border: 2px solid #0b3e82;
    }
  </style>
</head>
<body>
  <h1>Seller Profile: <?= htmlspecialchars($seller_info['fname'] . ' ' . $seller_info['lname']) ?></h1>

  <?php if ($item['product_type'] === 'service' && !empty($seller_info['profile_image'])): ?>
    <img src="../uploads/<?= htmlspecialchars($seller_info['profile_image']) ?>" alt="Profile Image" class="profile-image" />
  <?php endif; ?>

  <section>
    <h2>About Me</h2>
    <p><?= nl2br(htmlspecialchars($seller_info['about_me'] ?: 'No description available.')) ?></p>
  </section>

  <section>
    <h2>Skills</h2>
    <?php if (count($skills) === 0): ?>
      <p>No skills listed.</p>
    <?php else: ?>
      <ul>
        <?php foreach ($skills as $skill): ?>
          <li><?= htmlspecialchars($skill['title']) ?></li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </section>

  <section>
    <h2>Education</h2>
    <?php if (count($educations) === 0): ?>
      <p>No education information available.</p>
    <?php else: ?>
      <ul>
        <?php foreach ($educations as $edu): ?>
          <li>
            <?= htmlspecialchars($edu['degree']) ?> in <?= htmlspecialchars($edu['field_of_study']) ?>,
            <?= htmlspecialchars($edu['school']) ?> (<?= htmlspecialchars($edu['start_date']) ?> - <?= htmlspecialchars($edu['end_date'] ?: 'Present') ?>)
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </section>

  <section>
    <h2>Experience</h2>
    <?php if (count($experiences) === 0): ?>
      <p>No experience information available.</p>
    <?php else: ?>
      <ul>
        <?php foreach ($experiences as $exp): ?>
          <li>
            <?= htmlspecialchars($exp['title']) ?> at <?= htmlspecialchars($exp['company']) ?> (<?= htmlspecialchars($exp['start_date']) ?> - <?= htmlspecialchars($exp['end_date'] ?: 'Present') ?>)
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </section>
</body>
</html>

