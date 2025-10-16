<?php
session_start();
require_once("../Configurations/Config_db.php");

// Get seller_id from URL
$seller_id = $_GET['seller_id'] ?? null;
if (!$seller_id) {
    echo "No seller specified.";
    exit();
}

// Fetch seller basic info
$stmt = $conn->prepare("
  SELECT u.fname, u.lname, u.email, u.phone_num, u.about_me, u.profile_image
  FROM users u
  JOIN seller_details s ON u.user_id = s.user_id
  WHERE s.seller_id = ?
");
$stmt->bind_param("i", $seller_id);
$stmt->execute();
$seller_info = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$seller_info) {
    echo "Seller not found.";
    exit();
}

// Fetch skills
$stmt = $conn->prepare("
  SELECT s.title 
  FROM seller_skills ss 
  JOIN skills s ON ss.skills_id = s.skills_id 
  WHERE ss.seller_id = ?
");
$stmt->bind_param("i", $seller_id);
$stmt->execute();
$skills = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch education
$stmt = $conn->prepare("
  SELECT e.degree, e.field_of_study, e.school, e.start_date, e.end_date
  FROM education e
  JOIN user_education ue ON e.education_id = ue.education_id
  JOIN users u ON ue.user_id = u.user_id
  JOIN seller_details sd ON sd.user_id = u.user_id
  WHERE sd.seller_id = ?
");
$stmt->bind_param("i", $seller_id);
$stmt->execute();
$educations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch experience
$stmt = $conn->prepare("
  SELECT ex.title, ex.company, ex.start_date, ex.end_date
  FROM experience ex
  JOIN user_experience ue ON ex.experience_id = ue.experience_id
  JOIN users u ON ue.user_id = u.user_id
  JOIN seller_details sd ON sd.user_id = u.user_id
  WHERE sd.seller_id = ?
");
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
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f5f6fa;
      color: #2c3e50;
    }

    .profile-container {
      max-width: 1100px;
      margin: 40px auto;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.07);
      padding: 40px;
      display: flex;
      flex-direction: row;
      gap: 40px;
    }

    .profile-left {
      width: 300px;
      text-align: center;
      border-right: 1px solid #e0e0e0;
      padding-right: 30px;
    }

    .profile-left img {
      width: 160px;
      height: 160px;
      object-fit: cover;
      border-radius: 50%;
      border: 4px solid #0b3e82;
    }

    .profile-left h2 {
      margin-top: 20px;
      font-size: 22px;
    }

    .profile-left p {
      font-size: 14px;
      color: #777;
    }

    .profile-right {
      flex: 1;
    }

    section {
      margin-bottom: 30px;
    }

    section h2 {
      font-size: 18px;
      color: #0b3e82;
      margin-bottom: 10px;
    }

    section ul {
      list-style: none;
      padding: 0;
    }

    section ul li {
      padding: 6px 0;
      border-bottom: 1px solid #eee;
    }

    .skill-badge {
      display: inline-block;
      background: #e6f0ff;
      color: #0b3e82;
      font-size: 13px;
      padding: 6px 12px;
      border-radius: 15px;
      margin: 4px 4px 4px 0;
    }

    #hireBtn {
      background-color: #0b3e82;
      color: white;
      padding: 12px 24px;
      border: none;
      border-radius: 25px;
      cursor: pointer;
      margin-top: 20px;
    }

    #hireBtn:hover {
      background-color: #093169;
    }

    /* Modal styling */
    #hireModal {
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100vw; height: 100vh;
      background: rgba(0, 0, 0, 0.5);
      justify-content: center;
      align-items: center;
    }

    #hireModal > div {
      background: white;
      padding: 30px;
      border-radius: 12px;
      width: 95%;
      max-width: 500px;
      position: relative;
    }

    #closeModal {
      position: absolute;
      top: 10px;
      right: 15px;
      font-size: 20px;
      cursor: pointer;
    }
  </style>
</head>

<body>
  <div class="profile-container">
    <div class="profile-left">
      <img src="../<?= htmlspecialchars($seller_info['profile_image'] ?: 'images/default_profile.jpg') ?>" alt="Profile Picture">
      <h2><?= htmlspecialchars($seller_info['fname'] . ' ' . $seller_info['lname']) ?></h2>
      <p><?= htmlspecialchars($seller_info['email']) ?></p>
      <p><?= htmlspecialchars($seller_info['phone_num']) ?></p>
      <button id="hireBtn">Hire</button>
    </div>

    <div class="profile-right">
      <section>
        <h2>About Me</h2>
        <p><?= nl2br(htmlspecialchars($seller_info['about_me'] ?: 'No description available.')) ?></p>
      </section>

      <section>
        <h2>Skills</h2>
        <?php if (count($skills) === 0): ?>
          <p>No skills listed.</p>
        <?php else: ?>
          <?php foreach ($skills as $skill): ?>
            <span class="skill-badge"><?= htmlspecialchars($skill['title']) ?></span>
          <?php endforeach; ?>
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
    </div>
  </div>

  <!-- Modal Popup -->
  <div id="hireModal">
    <div>
      <span id="closeModal">&times;</span>
      <h2>Hire <?= htmlspecialchars($seller_info['fname']) ?></h2>
      <form method="POST" action="ProcessHire.php">
        <input type="hidden" name="seller_id" value="<?= $seller_id ?>">
        <label>Task Title:</label>
        <input type="text" name="task_title" required style="width:100%; padding:10px; margin:10px 0;">

        <label>Task Description:</label>
        <textarea name="task_description" rows="4" required style="width:100%; padding:10px; margin:10px 0;"></textarea>

        <label>Preferred Date:</label>
        <input type="date" name="preferred_date" required style="width:100%; padding:10px; margin:10px 0;">

        <button type="submit" style="background-color:#0b3e82; color:white; padding:10px 20px; border:none; border-radius:5px;">Submit Request</button>
      </form>
    </div>
  </div>

  <script>
    const hireBtn = document.getElementById("hireBtn");
    const hireModal = document.getElementById("hireModal");
    const closeModal = document.getElementById("closeModal");

    hireBtn.addEventListener("click", () => {
      hireModal.style.display = "flex";
    });

    closeModal.addEventListener("click", () => {
      hireModal.style.display = "none";
    });

    window.addEventListener("click", (e) => {
      if (e.target === hireModal) {
        hireModal.style.display = "none";
      }
    });
  </script>
</body>
</html>
