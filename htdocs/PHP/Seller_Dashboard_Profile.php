<?php
session_start();
require_once('../Configurations/Config_db.php');

if (!isset($_SESSION['user_id'])) {
    echo "Unauthorized access";
    exit();
}

$user_id = $_SESSION['user_id'];

// 1. Get seller_id for this user
$seller_id = null;
$seller_query = $conn->prepare("SELECT seller_id FROM seller_details WHERE user_id = ?");
$seller_query->bind_param("i", $user_id);
$seller_query->execute();
$seller_query->bind_result($seller_id);
$seller_query->fetch();
$seller_query->close();

if (!isset($seller_id) || $seller_id == 0) {
    echo "You are not registered as a seller.";
    exit();
}

// Fetch top picks (example: top 5 items ordered by popularity or price)
$top_picks_query = $conn->prepare("SELECT item_id, name, price, description FROM items ORDER BY price DESC LIMIT 5");
$top_picks_query->execute();
$top_picks = $top_picks_query->get_result();


// 2. Get About Me
$user_query = $conn->prepare("SELECT about_me FROM users WHERE user_id = ?");
$user_query->bind_param("i", $user_id);
$user_query->execute();
$user_result = $user_query->get_result()->fetch_assoc();
$user_query->close();

// 3. Get Education
$edu_query = $conn->prepare("
    SELECT e.*
    FROM user_education ue
    JOIN education e ON ue.education_id = e.education_id
    WHERE ue.user_id = ?");
$edu_query->bind_param("i", $user_id);
$edu_query->execute();
$educations = $edu_query->get_result();

// 4. Get Experience
$exp_query = $conn->prepare("
    SELECT ex.*
    FROM user_experience ue
    JOIN experience ex ON ue.experience_id = ex.experience_id
    WHERE ue.user_id = ?");
$exp_query->bind_param("i", $user_id);
$exp_query->execute();
$experiences = $exp_query->get_result();

// 5. Get Skills (via seller_skills)
$skills_query = $conn->prepare("
    SELECT s.title
    FROM seller_skills ss
    JOIN skills s ON ss.skills_id = s.skills_id
    WHERE ss.seller_id = ?");
$skills_query->bind_param("i", $seller_id);
$skills_query->execute();
$skills = $skills_query->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Seller Dashboard</title>
     <link rel="stylesheet" href="../CSS/Seller_Dashboard.css" />
</head>
<body>

<h1>Seller Dashboard</h1>

<section>
    <h2>About Me</h2>
    <form method="POST" action="update_profile.php">
        <textarea name="about_me" rows="4" cols="60"><?php echo htmlspecialchars($user_result['about_me'] ?? ''); ?></textarea><br>
        <input type="submit" value="Update">
    </form>
</section>

<section>
    <h2>Education</h2>
    <ul>
        <?php while ($edu = $educations->fetch_assoc()): ?>
            <li><strong><?= htmlspecialchars($edu['degree']) ?></strong> in <?= htmlspecialchars($edu['field_of_study']) ?> at <?= htmlspecialchars($edu['school']) ?> (<?= htmlspecialchars($edu['start_date']) ?> to <?= htmlspecialchars($edu['end_date']) ?>)</li>
        <?php endwhile; ?>
    </ul>
    <form method="POST" action="add_education.php">
        <div class="text-inputs">
        <input name="school" placeholder="School" type="text"><br>
        <input name="degree" placeholder="Degree" type="text"><br>
        </div>
        <input name="field_of_study" placeholder="Field" type="text">
        
        <br>
        <div class="date-inputs">
        <input name="start_date" type="date">
        <input name="end_date" type="date"><br>
        </div>
        <button>Add Education</button>
    </form>
</section>

<section>
    <h2>Experience</h2>
    <ul>
        <?php while ($exp = $experiences->fetch_assoc()): ?>
            <li><?= htmlspecialchars($exp['title']) ?> at <?= htmlspecialchars($exp['company']) ?> (<?= htmlspecialchars($exp['start_date']) ?> to <?= htmlspecialchars($exp['end_date']) ?>)</li>
        <?php endwhile; ?>
    </ul>
    <form name = "Experience" method="POST" action="add_experience.php">
        <input name="title" placeholder="Job Title" type="text"><br>
        <input name="company" placeholder="Company" type="text"><br>
        <div class="date-inputs">
        <input name="start_date" type="date">
        <input name="end_date" type="date"><br>
        </div>
        <button>Add Experience</button>
    </form>
</section>

<section>
    <h2>Skills</h2>
    <ul>
        <?php while ($skill = $skills->fetch_assoc()): ?>
            <li><?= htmlspecialchars($skill['title']) ?></li>
        <?php endwhile; ?>
    </ul>
    <form method="POST" action="add_skills.php">
        <input name="skills" placeholder="e.g. PHP, JavaScript, SQL" type="text"><br>
        <button>Add Skills</button>
    </form>
</section>

<section>
  <h2>Top Picks</h2>
  <ul>
    <?php while ($top = $top_picks->fetch_assoc()): ?>
      <li>
        <strong><?= htmlspecialchars($top['name']) ?></strong><br />
        Price: R<?= htmlspecialchars($top['price']) ?><br />
        Description: <?= htmlspecialchars($top['description']) ?><br />
      </li>
    <?php endwhile; ?>
  </ul>
</section>


</body>
</html>
