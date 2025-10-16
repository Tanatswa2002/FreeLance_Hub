<?php
session_start();
require_once("../Configurations/Config_db.php");

// Set upload directory (ensure this folder exists and is writable)
$uploadDir = "../uploads/profile_images/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

function sanitize($data) {
    return htmlspecialchars(trim($data));
}

// Initialize error array
$errors = [];

// Check if POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sanitize inputs
    $fname = sanitize($_POST['Fname'] ?? '');
    $lname = sanitize($_POST['Lname'] ?? '');
    $username = sanitize($_POST['username'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $c_password = $_POST['C_password'] ?? '';
    $role = sanitize($_POST['role'] ?? '');

    // Validate required fields
    if (!$fname || !$lname || !$username || !$email || !$phone || !$password || !$c_password || !$role) {
        $errors[] = "Please fill in all required fields.";
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // Validate phone: digits only, 10-15 digits
    if (!preg_match('/^\d{10,15}$/', $phone)) {
        $errors[] = "Phone number must be 10 to 15 digits.";
    }

    // Validate password strength
    $strongPass = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@#$%^&+=!]).{8,}$/';
    if (!preg_match($strongPass, $password)) {
        $errors[] = "Password must include uppercase, lowercase, number, symbol, and be at least 8 characters.";
    }

    // Confirm password match
    if ($password !== $c_password) {
        $errors[] = "Passwords do not match.";
    }

    // Validate role
    if (!in_array($role, ['seller', 'buyer'])) {
        $errors[] = "Invalid role selected.";
    }

    // Handle image upload if any
    $profileImagePath = null;
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['profile_image'];

        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "Error uploading image.";
        } else {
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!in_array($file['type'], $allowedTypes)) {
                $errors[] = "Profile image must be JPEG, PNG, or GIF.";
            }

            if ($file['size'] > 2 * 1024 * 1024) {
                $errors[] = "Profile image must be less than 2MB.";
            }
        }
    }

    // If no errors so far, proceed
    if (empty($errors)) {
        // Check if username or email already exists
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors[] = "Username or email already taken.";
        }
        $stmt->close();
    }

    if (empty($errors)) {
        // Hash the password
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Handle file upload
        if (isset($file) && $file['error'] === UPLOAD_ERR_OK) {
            // Generate unique filename
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $newFileName = uniqid('profile_', true) . '.' . $ext;
            $destination = $uploadDir . $newFileName;

            if (!move_uploaded_file($file['tmp_name'], $destination)) {
                $errors[] = "Failed to save uploaded image.";
            } else {
                // Store relative path for DB
                $profileImagePath = 'uploads/profile_images/' . $newFileName;
            }
        }

        // Insert user into DB if still no errors
        if (empty($errors)) {
            $stmt = $conn->prepare("INSERT INTO users (fname, lname, username, email, phone_num, user_password, role, profile_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $fname, $lname, $username, $email, $phone, $passwordHash, $role, $profileImagePath);
            $executeOk = $stmt->execute();
            $stmt->close();

           if ($executeOk) {
    $new_user_id = $conn->insert_id;

    if ($role === 'seller') {
        // Insert into seller_details
        $stmt = $conn->prepare("INSERT INTO seller_details (user_id) VALUES (?)");
        $stmt->bind_param("i", $new_user_id);
        if (!$stmt->execute()) {
            $errors[] = "Error creating seller profile: " . $stmt->error;
        }
        $stmt->close();
    }

    // Set session and redirect
    $_SESSION['user_id'] = $new_user_id;
    $_SESSION['user_type'] = $role;

    if ($role === 'seller') {
        header("Location: Seller_Dashboard.php");
    } else {
        header("Location: buyer_dashboard.php");
    }
    exit();
}

        }
    }
} else {
    $errors[] = "Invalid request method.";
}

// If errors, display them (or you can redirect back with errors)
if (!empty($errors)) {
    echo "<h2>Registration Errors:</h2>";
    echo "<ul style='color:red;'>";
    foreach ($errors as $error) {
        echo "<li>" . htmlspecialchars($error) . "</li>";
    }
    echo "</ul>";
    echo '<p><a href="../Html/Register.html">Go Back to Register</a></p>';
}
?>

