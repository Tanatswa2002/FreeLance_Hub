<?php
session_start();
require_once("../Configurations/Config_db.php");

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input
    $login_input = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($login_input) || empty($password)) {
        $error = "Please enter both username/email and password.";
    } else {
        // Prepare SQL statement securely
        $stmt = $conn->prepare("SELECT user_id, fname, email, user_password, role FROM users WHERE email = ? OR username = ?");
        if (!$stmt) {
            $error = "Database error: " . $conn->error;
        } else {
            $stmt->bind_param("ss", $login_input, $login_input);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                $error = "User not found.";
            } else {
                $user = $result->fetch_assoc();

                if (!password_verify($password, $user['user_password'])) {
                    $error = "Incorrect password.";
                } else {
                    // Login success: set session variables
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_name'] = $user['fname'];
                    $_SESSION['user_type'] = $user['role'];

                    // Redirect based on role
                    switch ($user['role']) {
                        case 'buyer':
                            header("Location: buyer_dashboard.php");
                            exit();
                        case 'seller':
                            header("Location: Seller_Dashboard.php");
                            exit();
                        case 'admin':
                            header("Location: Admin_Dashboard.php");
                            exit();
                        default:
                            $error = "Unauthorized role.";
                    }
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login</title>
  <link rel="stylesheet" href="../CSS/Login.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet" />

  <style>
  body{
        background-color:#f9f9f9;
  }
    .error-message {
      color: red;
      text-align: center;
      margin-bottom: 1em;
      font-weight: bold;
    }
    .password-toggle {
      cursor: pointer;
      user-select: none;
      font-size: 0.9em;
      color: #555;
      margin-left: 10px;
    }
    .login_form_right{
          background-color:#f9f9f9;
    }
  </style>
</head>
<body>
  <div class="login_form">
    <div class="login_form_left">
      <h2>Log In</h2>

      <?php if ($error): ?>
        <p class="error-message"><?= htmlspecialchars($error) ?></p>
      <?php endif; ?>

      <form action="Login.php" method="post" id="loginForm">
        <div class="login_group">
          <input type="text" id="username" name="username" placeholder="Enter email or username" required value="<?= isset($login_input) ? htmlspecialchars($login_input) : '' ?>" />
        </div>

        <br /><br /><br />

        <div class="login_group" style="position: relative;">
          <input type="password" id="password" name="password" placeholder="Enter password" required />
          <span id="togglePassword" class="password-toggle">Show</span>
        </div>

        <br />

        <button id="login_button" type="submit">Log In</button>
        <a id="forgot_password" href="#">forgot password?</a>

        <br /><br /><br />
        <p>
          Not a Member? 
          <a href="../Html/Register.html">Register</a>
        </p>
      </form>
    </div>

    <div class="login_form_right">
      <img src="../images/Login_1.jpg" alt="Login Image" />
    </div>
  </div>

  <script>
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    togglePassword.addEventListener('click', () => {
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
      togglePassword.textContent = type === 'password' ? 'Show' : 'Hide';
    });
  </script>
</body>
</html>
