<?php
session_start();
require_once '../Configurations/Config_db.php';  // Adjust path if needed

if (!isset($_SESSION['user_id'])) {
    echo "Unauthorized access";
    exit();
}

$user_id = $_SESSION['user_id'];

// Helper function to parse date to month & year
function parse_date($date_str) {
    if (!$date_str) return ['', ''];
    $dt = DateTime::createFromFormat('Y-m-d', $date_str);
    if (!$dt) return ['', ''];
    $month = $dt->format('F');  // Full month name
    $year = $dt->format('Y');
    return [$month, $year];
}

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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. About Me
    $about = trim($_POST['About'] ?? '');
    $stmt = $conn->prepare("UPDATE users SET about_me = ? WHERE user_id = ?");
    $stmt->bind_param("si", $about, $user_id);
    $stmt->execute();
    $stmt->close();

    // 2. Education
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

    // 3. Experience
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

    // 4. Skills
    if (!empty($_POST['skills'])) {
        $skills_raw = trim($_POST['skills']);
        $skills_array = array_map('trim', explode(',', $skills_raw));

        foreach ($skills_array as $skill_title) {
            if ($skill_title !== '') {
                // Check if skill exists
                $check_stmt = $conn->prepare("SELECT skills_id FROM skills WHERE title = ?");
                $check_stmt->bind_param("s", $skill_title);
                $check_stmt->execute();
                $check_stmt->store_result();

                if ($check_stmt->num_rows > 0) {
                    $check_stmt->bind_result($skill_id);
                    $check_stmt->fetch();
                } else {
                    // Insert skill since it doesn't exist
                    $insert_stmt = $conn->prepare("INSERT INTO skills (title) VALUES (?)");
                    $insert_stmt->bind_param("s", $skill_title);
                    $insert_stmt->execute();
                    $skill_id = $insert_stmt->insert_id;
                    $insert_stmt->close();
                }
                $check_stmt->close();

                // Link skill to user (seller)
                $link_stmt = $conn->prepare("INSERT INTO seller_skills (seller_id, skills_id) VALUES (?, ?)");
                $link_stmt->bind_param("ii", $user_id, $skill_id);
                $link_stmt->execute();
                $link_stmt->close();
            }
        }
    }

    // 5. Profile Picture Upload
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_pic']['tmp_name'];
        $fileName = $_FILES['profile_pic']['name'];
        $fileSize = $_FILES['profile_pic']['size'];
        $fileType = $_FILES['profile_pic']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileExtension, $allowedfileExtensions) && $fileSize < 2 * 1024 * 1024) { // max 2MB
            $newFileName = $user_id . '.' . $fileExtension;
            $uploadFileDir = '../uploads/profile_pics/';
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            $dest_path = $uploadFileDir . $newFileName;

            if(move_uploaded_file($fileTmpPath, $dest_path)) {
                $stmt = $conn->prepare("UPDATE users SET profile_pic_path = ? WHERE user_id = ?");
                $stmt->bind_param("si", $newFileName, $user_id);
                $stmt->execute();
                $stmt->close();
            } else {
                echo "Error moving the uploaded file.";
            }
        } else {
            echo "Upload failed. Allowed types: jpg, jpeg, png, gif. Max size: 2MB.";
        }
    }

    echo "Profile updated successfully!";
    exit();
}

// Load current data for form pre-fill

// About Me
$stmt = $conn->prepare("SELECT about_me, profile_pic_path FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($about_me, $profile_pic_path);
$stmt->fetch();
$stmt->close();

// Latest Education
$stmt = $conn->prepare("SELECT e.school, e.degree, e.field_of_study, e.start_date, e.end_date
                        FROM education e
                        JOIN user_education ue ON e.education_id = ue.education_id
                        WHERE ue.user_id = ?
                        ORDER BY e.end_date DESC LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($school, $degree, $field_of_study, $edu_start_date, $edu_end_date);
$stmt->fetch();
$stmt->close();

list($edu_month, $edu_year) = parse_date($edu_start_date);
list($edu_month_end, $edu_year_end) = parse_date($edu_end_date);

// Latest Experience
$stmt = $conn->prepare("SELECT ex.title, ex.company, ex.start_date, ex.end_date
                        FROM experience ex
                        JOIN user_experience ue ON ex.experience_id = ue.experience_id
                        WHERE ue.user_id = ?
                        ORDER BY ex.end_date DESC LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($title, $company, $exp_start_date, $exp_end_date);
$stmt->fetch();
$stmt->close();

list($exp_month, $exp_year) = parse_date($exp_start_date);
list($exp_month_end, $exp_year_end) = parse_date($exp_end_date);

// Skills
$stmt = $conn->prepare("SELECT s.title
                        FROM skills s
                        JOIN seller_skills ss ON s.skills_id = ss.skills_id
                        WHERE ss.seller_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$skills = [];
while ($row = $result->fetch_assoc()) {
    $skills[] = $row['title'];
}
$stmt->close();
$skills_str = implode(', ', $skills);

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

<form action="edit_profile.php" method="POST" enctype="multipart/form-data">
    <label for="About">About Me:</label><br />
    <textarea id="About" name="About" rows="5" cols="40" placeholder="Tell everyone about yourself"><?php echo htmlspecialchars($about_me); ?></textarea><br /><br />

    <label for="profile_pic">Profile Picture:</label><br />
    <?php if ($profile_pic_path && file_exists("../uploads/profile_pics/" . $profile_pic_path)) : ?>
        <img src="<?php echo "../uploads/profile_pics/" . htmlspecialchars($profile_pic_path); ?>" alt="Profile Picture" width="150" /><br />
    <?php else: ?>
        <p>No profile picture uploaded.</p>
    <?php endif; ?>
    <input type="file" id="profile_pic" name="profile_pic" accept="image/*" /><br /><br />

    <fieldset>
        <legend>Education</legend>
        <label for="School">School:</label><br />
        <input type="text" id="School" name="School" value="<?php echo htmlspecialchars($school); ?>" required /><br /><br />

        <label for="Degree">Degree:</label><br />
        <input type="text" id="Degree" name="Degree" value="<?php echo htmlspecialchars($degree); ?>" required /><br /><br />

        <label for="field_of_study">Field of Study:</label><br />
        <input type="text" id="field_of_study" name="field_of_study" value="<?php echo htmlspecialchars($field_of_study); ?>" required /><br /><br />

        <label>Start Date:</label><br />
        <select name="edu_month" required>
            <option value="">Month</option>
            <?php
            $months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
            foreach($months as $month) {
                $selected = ($month === $edu_month) ? 'selected' : '';
                echo "<option value=\"$month\" $selected>$month</option>";
            }
            ?>
        </select>

        <select name="edu_start_year" required>
            <option value="">Year</option>
            <?php
            for ($y = date('Y'); $y >= 1950; $y--) {
                $selected = ($y == $edu_year) ? 'selected' : '';
                echo "<option value=\"$y\" $selected>$y</option>";
            }
            ?>
        </select><br /><br />
