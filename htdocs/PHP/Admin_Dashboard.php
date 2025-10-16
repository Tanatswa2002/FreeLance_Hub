<?php
session_start();

// Protect this page for admins only
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: Login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Dashboard</title>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter&family=Poppins&family=Open+Sans&display=swap" rel="stylesheet" />

  <style>
    body {
      margin: 0;
      font-family: 'Poppins', 'Inter', 'Open Sans', sans-serif;
  background-image: url('../images/Admin_Background_4.avif'); /* Replace with your actual path */
  background-size: cover;         /* Ensures image covers entire background */
  background-position: center;    /* Center the image */
  background-repeat: no-repeat;   /* Prevents repeating */
  background-attachment: fixed;   /* Optional: keeps background fixed when scrolling */
  color: #333;                    /* Keep text readable */
  min-height: 100vh;
  padding: 40px 20px;
  display: flex;
  justify-content: center;

}

    .bg-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.2); /* dark overlay */
  z-index: -1;
}


    .dashboard {
      background:rgb(255,255,253,0);
      height:100%;
      width: 90%;
      padding: 30px 40px;
      border-radius: 10px;
      box-shadow: 0 6px 15px rgba(0,0,0,0.1);
      display: flex;
      left:165px;
      flex-direction: column;
      align-items: center;
      gap: 40px;
      position:relative;
    }

    .heading{
        padding:20px;
        border-radius:30px;
        background:rgb(255,255,255,0.5);
    }

    h1 {
      font-size: 2.3rem;
      color:rgb(14, 59, 101);
      margin: 0;
    }

    /* Navigation container */
    .NavBar nav ul {
      list-style: none;
      display: flex;
      gap: 40px;
      padding-left: 0;
      margin: 0;
      flex-wrap: wrap;
      justify-content: center;
      width: 100%;
      max-width: 700px;
    }

    /* Each nav card */
    .NavBar nav ul li {
      flex: 1 1 120px;
      max-width: 140px;
      text-align: center;
    }

    .NavBar nav ul li a {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 12px;
      padding: 20px;
      border-radius: 15px;
      background-color:rgb(255,255,255,0.8);
      color:rgb(14, 59, 101) ;
      font-weight: 600;
      text-decoration: none;
      transition: background-color 0.3s ease, box-shadow 0.3s ease;
      box-shadow: 0 4px 8px rgba(11, 62, 130, 0.3);
      user-select: none;
      height: 140px;
      justify-content: center;
    }

    .NavBar nav ul li a:hover,
    .NavBar nav ul li a:focus {
      background-color: rgb(14, 59, 101,0.4);
      box-shadow: 0 6px 14px rgba(22, 75, 175, 0.5);
      outline: none;
      color:#fff;
    }

    .nav-icon {
      width: 48px;
      height: 48px;
      object-fit: contain;
    }

      .NavBar nav ul li a img {
        width: 100px;   /* Change this value */
        height: 100px;  /* Change this value */
        object-fit: contain;
        margin-bottom: 10px;
        }

    /* Responsive */
    @media (max-width: 600px) {
      .NavBar nav ul {
        gap: 20px;
      }

      .NavBar nav ul li {
        flex: 1 1 100px;
        max-width: 100px;
      }
      
      .NavBar nav ul li a {
        height: 120px;
        padding: 16px;
      }

      .nav-icon {
        width: 36px;
        height: 36px;
      }
    }

     /* Logout Icon Container */
nav.top {
  display: flex;
  justify-content: flex-end;
  padding: 20px;
  position: absolute;
  left: 20px;
  top:20px;
  z-index: 1000;
  
}

/* Logout Image Style */
nav.top .logout {
  width: 40px;          /* Adjust size as needed */
  height: 40px;
  object-fit: contain;
  transition: transform 0.2s ease, filter 0.3s ease;
  cursor: pointer;
}

nav.top .logout:hover {
  transform: scale(1.1);
  filter: brightness(1.2);
}

nav.top .logout {
  border-radius: 50%;
  background-color: #f5f5f5;
  padding: 5px;
}


  </style>
</head>
<body>
<div class="bg-overlay"></div>
    <nav class = "top">
        <a href = "Logout.php">
            <img src = "../images/logout.png" alt = "logout" class = "logout"/>
            </a>
    
  <div class="dashboard">
  <div class = "heading">
    <h1>Welcome Admin <?= htmlspecialchars($_SESSION['user_name']) ?>!</h1>
    </div>

    <div class="NavBar">
      <nav>
        <ul>
          <li>
            <a href="Review_Verifications.php">
              <img src="../images/Reviews_3.jpg" alt="Review Icon" class="nav-icon" />
              Review
            </a>
          </li>
          <li>
            <a href="Generate_Reports.php">
              <img src="../images/Report_3.jpg" alt="Reports Icon" class="nav-icon" />
              Reports
            </a>
          </li>
          <li>
            <a href="Manage_Accounts.php">
              <img src="../images/Accounts_1.jpg" alt="Accounts Icon" class="nav-icon" />
              Accounts
            </a>
          </li>
          <li>
            <a href="Manage_Transactions.php">
              <img src="../images/transactions_1.jpg" alt="Transactions Icon" class="nav-icon" />
              Transactions
            </a>
          </li>
          <li>
            <a href="Support_Tickets.php">
              <img src="../images/Support.webp" alt="Tickets Icon" class="nav-icon" />
              Tickets
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </div>
</body>
</html>

