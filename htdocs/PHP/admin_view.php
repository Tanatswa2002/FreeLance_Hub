<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once("../Configurations/Config_db.php");

$user_id = $_GET['user_id'] ?? null;
if (!$user_id) {
    echo "No user specified.";
    exit();
}

// Fetch user info from users table
$stmt = $conn->prepare("
  SELECT fname, lname, email, phone_num, about_me, profile_image, role
  FROM users 
  WHERE user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_info = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user_info) {
    echo "User not found.";
    exit();
}

// Initialize empty arrays for optional info
$skills = [];
$educations = [];
$experiences = [];

// If user is a seller, fetch extra details
if ($user_info['role'] === 'seller') {

    // Fetch skills
    $stmt = $conn->prepare("
      SELECT s.title 
      FROM seller_skills ss 
      JOIN skills s ON ss.skills_id = s.skills_id 
      WHERE ss.seller_id = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $skills = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Fetch education
    $stmt = $conn->prepare("
      SELECT e.degree, e.field_of_study, e.school, e.start_date, e.end_date
      FROM education e
      JOIN user_education ue ON e.education_id = ue.education_id
      WHERE ue.user_id = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $educations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Fetch experience
    $stmt = $conn->prepare("
      SELECT ex.title, ex.company, ex.start_date, ex.end_date
      FROM experience ex
      JOIN user_experience ue ON ex.experience_id = ue.experience_id
      WHERE ue.user_id = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $experiences = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>User Profile</title>
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
    .deactivate-btn {
      background-color: #d93025;
      border: none;
      color: white;
      padding: 12px 20px;
      font-size: 1rem;
      border-radius: 6px;
      cursor: pointer;
      transition: background-color 0.3s ease;
      margin-top: 20px;
    }
    .deactivate-btn:hover {
      background-color: #a5271f;
    }
  </style>
  <script>
    function confirmDeactivate() {
      return confirm('Are you sure you want to deactivate this user?');
    }
  </script>
</head>
<body>
  <h1>User Profile: <?= htmlspecialchars($user_info['fname'] . ' ' . $user_info['lname']) ?></h1>

  <?php if (!empty($user_info['profile_image'])): ?>
    <img src="../uploads/<?= htmlspecialchars($user_info['profile_image']) ?>" alt="Profile Image" class="profile-image" />
  <?php endif; ?>

  <section>
    <h2>About Me</h2>
    <p><?= nl2br(htmlspecialchars($user_info['about_me'] ?: 'No description available.')) ?></p>
  </section>

  <section>
    <h2>Contact Info</h2>
    <p><strong>Email:</strong> <?= htmlspecialchars($user_info['email']) ?></p>
    <p><strong>Phone Number:</strong> <?= htmlspecialchars($user_info['phone_num'] ?: 'Not provided') ?></p>
    <p><strong>Role:</strong> <?= htmlspecialchars(ucfirst($user_info['role'])) ?></p>
  </section>

  <?php if ($user_info['role'] === 'seller'): ?>
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
  <?php endif; ?>

  <form method="POST" action="deactivate_user.php" onsubmit="return confirmDeactivate();">
    <input type="hidden" name="user_id" value="<?= (int)$user_id ?>">
    <button type="submit" class="deactivate-btn">Deactivate User</button>
  </form>
</body>
</html>
