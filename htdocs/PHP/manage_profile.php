<?php
session_start();
require_once '../Configurations/Config_db.php';

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access");
}

$user_id = $_SESSION['user_id'];

function make_date($month, $year) {
    if (empty($month) || empty($year)) return null;
    $months_map = [
        'January' => '01', 'February' => '02', 'March' => '03', 'April' => '04',
        'May' => '05', 'June' => '06', 'July' => '07', 'August' => '08',
        'September' => '09', 'October' => '10', 'November' => '11', 'December' => '12'
    ];
    $month_num = $months_map[$month] ?? null;
    if (!$month_num) return null;
    return "$year-$month_num-01";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update About Me
    if (isset($_POST['update_about'])) {
        $about = trim($_POST['About'] ?? '');
        $stmt = $conn->prepare("UPDATE users SET about_me = ? WHERE user_id = ?");
        $stmt->bind_param("si", $about, $user_id);
        $stmt->execute();
        $stmt->close();
    }

    // Add Education
    if (isset($_POST['add_education'])) {
        $school = trim($_POST['School'] ?? '');
        $degree = trim($_POST['Degree'] ?? '');
        $field_of_study = trim($_POST['field_of_study'] ?? '');
        $edu_start_date = make_date($_POST['edu_month'] ?? '', $_POST['edu_start_year'] ?? '');
        $edu_end_date = make_date($_POST['edu_month_end'] ?? '', $_POST['edu_end_year'] ?? '');

        if ($school && $degree && $field_of_study) {
            $stmt = $conn->prepare("INSERT INTO education (school, degree, field_of_study, start_date, end_date) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $school, $degree, $field_of_study, $edu_start_date, $edu_end_date);
            $stmt->execute();
            $education_id = $stmt->insert_id;
            $stmt->close();

            $stmt = $conn->prepare("INSERT INTO user_education (user_id, education_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $user_id, $education_id);
            $stmt->execute();
            $stmt->close();
        }
    }

    // Edit Education
    if (isset($_POST['edit_education'])) {
        $education_id = intval($_POST['education_id']);
        // Verify ownership
        $check = $conn->prepare("SELECT 1 FROM user_education WHERE education_id = ? AND user_id = ?");
        $check->bind_param("ii", $education_id, $user_id);
        $check->execute();
        $check->store_result();
        if ($check->num_rows === 1) {
            $school = trim($_POST['School'] ?? '');
            $degree = trim($_POST['Degree'] ?? '');
            $field_of_study = trim($_POST['field_of_study'] ?? '');
            $edu_start_date = make_date($_POST['edu_month'] ?? '', $_POST['edu_start_year'] ?? '');
            $edu_end_date = make_date($_POST['edu_month_end'] ?? '', $_POST['edu_end_year'] ?? '');

            $stmt = $conn->prepare("UPDATE education SET school = ?, degree = ?, field_of_study = ?, start_date = ?, end_date = ? WHERE education_id = ?");
            $stmt->bind_param("sssssi", $school, $degree, $field_of_study, $edu_start_date, $edu_end_date, $education_id);
            $stmt->execute();
            $stmt->close();
        }
        $check->close();
    }

    // Delete Education
    if (isset($_POST['delete_education'])) {
        $education_id = intval($_POST['education_id']);
        // Delete only if belongs to user
        $stmt = $conn->prepare("DELETE e FROM education e INNER JOIN user_education ue ON e.education_id = ue.education_id WHERE e.education_id = ? AND ue.user_id = ?");
        $stmt->bind_param("ii", $education_id, $user_id);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $stmt2 = $conn->prepare("DELETE FROM user_education WHERE education_id = ? AND user_id = ?");
            $stmt2->bind_param("ii", $education_id, $user_id);
            $stmt2->execute();
            $stmt2->close();
        }
        $stmt->close();
    }

    // Add Experience
    if (isset($_POST['add_experience'])) {
        $title = trim($_POST['Title'] ?? '');
        $company = trim($_POST['Company'] ?? '');
        $exp_start_date = make_date($_POST['exp_month'] ?? '', $_POST['exp_start_year'] ?? '');
        $exp_end_date = make_date($_POST['exp_month_end'] ?? '', $_POST['exp_end_year'] ?? '');

        if ($title && $company) {
            $stmt = $conn->prepare("INSERT INTO experience (title, company, start_date, end_date) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $title, $company, $exp_start_date, $exp_end_date);
            $stmt->execute();
            $experience_id = $stmt->insert_id;
            $stmt->close();

            $stmt = $conn->prepare("INSERT INTO user_experience (user_id, experience_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $user_id, $experience_id);
            $stmt->execute();
            $stmt->close();
        }
    }

    // Edit Experience
    if (isset($_POST['edit_experience'])) {
        $experience_id = intval($_POST['experience_id']);
        $check = $conn->prepare("SELECT 1 FROM user_experience WHERE experience_id = ? AND user_id = ?");
        $check->bind_param("ii", $experience_id, $user_id);
        $check->execute();
        $check->store_result();
        if ($check->num_rows === 1) {
            $title = trim($_POST['Title'] ?? '');
            $company = trim($_POST['Company'] ?? '');
            $exp_start_date = make_date($_POST['exp_month'] ?? '', $_POST['exp_start_year'] ?? '');
            $exp_end_date = make_date($_POST['exp_month_end'] ?? '', $_POST['exp_end_year'] ?? '');

            $stmt = $conn->prepare("UPDATE experience SET title = ?, company = ?, start_date = ?, end_date = ? WHERE experience_id = ?");
            $stmt->bind_param("ssssi", $title, $company, $exp_start_date, $exp_end_date, $experience_id);
            $stmt->execute();
            $stmt->close();
        }
        $check->close();
    }

    // Delete Experience
    if (isset($_POST['delete_experience'])) {
        $experience_id = intval($_POST['experience_id']);
        $stmt = $conn->prepare("DELETE ex FROM experience ex INNER JOIN user_experience ue ON ex.experience_id = ue.experience_id WHERE ex.experience_id = ? AND ue.user_id = ?");
        $stmt->bind_param("ii", $experience_id, $user_id);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $stmt2 = $conn->prepare("DELETE FROM user_experience WHERE experience_id = ? AND user_id = ?");
            $stmt2->bind_param("ii", $experience_id, $user_id);
            $stmt2->execute();
            $stmt2->close();
        }
        $stmt->close();
    }

    // Add Skills
    if (isset($_POST['add_skills'])) {
        $skills_raw = trim($_POST['skills'] ?? '');
        $skills_array = array_filter(array_map('trim', explode(',', $skills_raw)));

        foreach ($skills_array as $skill_title) {
            // Check if skill exists
            $check_stmt = $conn->prepare("SELECT skills_id FROM skills WHERE title = ?");
            $check_stmt->bind_param("s", $skill_title);
            $check_stmt->execute();
            $check_stmt->store_result();

            if ($check_stmt->num_rows > 0) {
                $check_stmt->bind_result($skill_id);
                $check_stmt->fetch();
            } else {
                $insert_stmt = $conn->prepare("INSERT INTO skills (title) VALUES (?)");
                $insert_stmt->bind_param("s", $skill_title);
                $insert_stmt->execute();
                $skill_id = $insert_stmt->insert_id;
                $insert_stmt->close();
            }
            $check_stmt->close();

            // Link skill to user if not linked
            $link_check = $conn->prepare("SELECT 1 FROM seller_skills WHERE seller_id = ? AND skills_id = ?");
            $link_check->bind_param("ii", $user_id, $skill_id);
            $link_check->execute();
            $link_check->store_result();

            if ($link_check->num_rows === 0) {
                $link_stmt = $conn->prepare("INSERT INTO seller_skills (seller_id, skills_id) VALUES (?, ?)");
                $link_stmt->bind_param("ii", $user_id, $skill_id);
                $link_stmt->execute();
                $link_stmt->close();
            }
            $link_check->close();
        }
    }

    // Delete Skill from user
    if (isset($_POST['delete_skill'])) {
        $skill_id = intval($_POST['skills_id']);
        $stmt = $conn->prepare("DELETE FROM seller_skills WHERE seller_id = ? AND skills_id = ?");
        $stmt->bind_param("ii", $user_id, $skill_id);
        $stmt->execute();
        $stmt->close();
    }

    echo "Profile updated successfully!";
    exit;
}

// Fetch About Me
$stmt = $conn->prepare("SELECT about_me FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($about_me);
$stmt->fetch();
$stmt->close();

// Fetch Education
$education = [];
$stmt = $conn->prepare("
    SELECT e.education_id, e.school, e.degree, e.field_of_study, e.start_date, e.end_date
    FROM education e
    JOIN user_education ue ON e.education_id = ue.education_id
    WHERE ue.user_id = ?
    ORDER BY e.start_date DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $education[] = $row;
}
$stmt->close();

// Fetch Experience
$experience = [];
$stmt = $conn->prepare("
    SELECT ex.experience_id, ex.title, ex.company, ex.start_date, ex.end_date
    FROM experience ex
    JOIN user_experience ue ON ex.experience_id = ue.experience_id
    WHERE ue.user_id = ?
    ORDER BY ex.start_date DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $experience[] = $row;
}
$stmt->close();

// Fetch Skills
$skills = [];
$stmt = $conn->prepare("
    SELECT s.skills_id, s.title
    FROM skills s
    JOIN seller_skills ss ON s.skills_id = ss.skills_id
    WHERE ss.seller_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $skills[] = $row;
}
$stmt->close();

$conn->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Edit Profile</title>
</head>
<body>
<h1>Edit Profile</h1>

<!-- About Me -->
<form method="POST" action="">
    <h2>About Me</h2>
    <textarea name="About" rows="4" cols="50"><?php echo htmlspecialchars($about_me); ?></textarea><br />
    <button type="submit" name="update_about">Update About Me</button>
</form>

<hr />

<!-- Education -->
<h2>Education</h2>
<?php foreach ($education as $edu): ?>
    <form method="POST" action="" style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
        <input type="hidden" name="education_id" value="<?php echo $edu['education_id']; ?>">
        <label>School: <input type="text" name="School" value="<?php echo htmlspecialchars($edu['school']); ?>" required></label><br />
        <label>Degree: <input type="text" name="Degree" value="<?php echo htmlspecialchars($edu['degree']); ?>" required></label><br />
        <label>Field of Study: <input type="text" name="field_of_study" value="<?php echo htmlspecialchars($edu['field_of_study']); ?>" required></label><br />
        <label>Start Date: <input type="month" name="edu_month" value="<?php echo date('Y-m', strtotime($edu['start_date'])); ?>"></label><br />
        <label>End Date: <input type="month" name="edu_month_end" value="<?php echo date('Y-m', strtotime($edu['end_date'])); ?>"></label><br />
        <label>Start Year (readonly): <input type="number" name="edu_start_year" value="<?php echo date('Y', strtotime($edu['start_date'])); ?>" readonly></label><br />
        <label>End Year (readonly): <input type="number" name="edu_end_year" value="<?php echo date('Y', strtotime($edu['end_date'])); ?>" readonly></label><br />
        <button type="submit" name="edit_education">Save</button>
        <button type="submit" name="delete_education" onclick="return confirm('Delete this education entry?')">Delete</button>
    </form>
<?php endforeach; ?>

<form method="POST" action="" style="border:1px solid #000; padding:10px; margin-top:20px;">
    <h3>Add New Education</h3>
    <label>School: <input type="text" name="School" required></label><br />
    <label>Degree: <input type="text" name="Degree" required></label><br />
    <label>Field of Study: <input type="text" name="field_of_study" required></label><br />
    <label>Start Month:
        <select name="edu_month" required>
            <option value="">Month</option>
            <?php foreach (['January','February','March','April','May','June','July','August','September','October','November','December'] as $m) {
                echo "<option value=\"$m\">$m</option>";
            } ?>
        </select>
    </label>
    <label>Start Year: <input type="number" name="edu_start_year" min="1900" max="<?php echo date('Y'); ?>" required></label><br />
    <label>End Month:
        <select name="edu_month_end" required>
            <option value="">Month</option>
            <?php foreach (['January','February','March','April','May','June','July','August','September','October','November','December'] as $m) {
                echo "<option value=\"$m\">$m</option>";
            } ?>
        </select>
    </label>
    <label>End Year: <input type="number" name="edu_end_year" min="1900" max="<?php echo date('Y'); ?>" required></label><br />
    <button type="submit" name="add_education">Add Education</button>
</form>

<hr />

<!-- Experience -->
<h2>Experience</h2>
<?php foreach ($experience as $exp): ?>
    <form method="POST" action="" style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
        <input type="hidden" name="experience_id" value="<?php echo $exp['experience_id']; ?>">
        <label>Title: <input type="text" name="Title" value="<?php echo htmlspecialchars($exp['title']); ?>" required></label><br />
        <label>Company: <input type="text" name="Company" value="<?php echo htmlspecialchars($exp['company']); ?>" required></label><br />
        <label>Start Date: <input type="month" name="exp_month" value="<?php echo date('Y-m', strtotime($exp['start_date'])); ?>"></label><br />
        <label>End Date: <input type="month" name="exp_month_end" value="<?php echo date('Y-m', strtotime($exp['end_date'])); ?>"></label><br />
        <label>Start Year (readonly): <input type="number" name="exp_start_year" value="<?php echo date('Y', strtotime($exp['start_date'])); ?>" readonly></label><br />
        <label>End Year (readonly): <input type="number" name="exp_end_year" value="<?php echo date('Y', strtotime($exp['end_date'])); ?>" readonly></label><br />
        <button type="submit" name="edit_experience">Save</button>
        <button type="submit" name="delete_experience" onclick="return confirm('Delete this experience entry?')">Delete</button>
    </form>
<?php endforeach; ?>

<form method="POST" action="" style="border:1px solid #000; padding:10px; margin-top:20px;">
    <h3>Add New Experience</h3>
    <label>Title: <input type="text" name="Title" required></label><br />
    <label>Company: <input type="text" name="Company" required></label><br />
    <label>Start Month:
        <select name="exp_month" required>
            <option value="">Month</option>
            <?php foreach (['January','February','March','April','May','June','July','August','September','October','November','December'] as $m) {
                echo "<option value=\"$m\">$m</option>";
            } ?>
        </select>
    </label>
    <label>Start Year: <input type="number" name="exp_start_year" min="1900" max="<?php echo date('Y'); ?>" required></label><br />
    <label>End Month:
        <select name="exp_month_end" required>
            <option value="">Month</option>
            <?php foreach (['January','February','March','April','May','June','July','August','September','October','November','December'] as $m) {
                echo "<option value=\"$m\">$m</option>";
            } ?>
        </select>
    </label>
    <label>End Year: <input type="number" name="exp_end_year" min="1900" max="<?php echo date('Y'); ?>" required></label><br />
    <button type="submit" name="add_experience">Add Experience</button>
</form>

<hr />

<!-- Skills -->
<h2>Skills</h2>
<ul>
<?php foreach ($skills as $skill): ?>
    <li>
        <?php echo htmlspecialchars($skill['title']); ?>
        <form method="POST" action="" style="display:inline;">
            <input type="hidden" name="skills_id" value="<?php echo $skill['skills_id']; ?>">
            <button type="submit" name="delete_skill" onclick="return confirm('Remove this skill?')">Remove</button>
        </form>
    </li>
<?php endforeach; ?>
</ul>

<form method="POST" action="">
    <h3>Add Skills (comma separated)</h3>
    <input type="text" name="skills" placeholder="e.g. PHP, JavaScript, HTML" required />
    <button type="submit" name="add_skills">Add Skills</button>
</form>

</body>
</html>