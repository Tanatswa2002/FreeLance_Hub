<?php
session_start();
require_once("Configurations/Config_db.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);

$top_graduates_query = $conn->query("
    SELECT u.fname, u.lname, u.about_me, u.profile_image, e.field_of_study
    FROM users u
    JOIN user_education ue ON u.user_id = ue.user_id
    JOIN education e ON ue.education_id = e.education_id
    WHERE u.role = 'seller'
    ORDER BY e.end_date DESC
    LIMIT 3
");

// 2. Get Testimonials
$testimonials_query = $conn->query("
    SELECT t.message, u.fname, u.lname
    FROM testimonials t
    JOIN users u ON t.user_id = u.user_id
    ORDER BY t.created_at DESC
    LIMIT 3
");

// 3. Check Login Status
$is_logged_in = false;
$user_name = "Guest";
$role = null;

if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    $user_id = intval($_SESSION['user_id']);
    if ($user_id > 0) {
        $stmt = $conn->prepare("SELECT fname, role FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $user_name = $user['fname'];
            $role = $user['role'];
            $is_logged_in = true;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Graduate Marketplace - Home</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
  body {
    background-color: rgb(236, 235, 230);
    background-repeat: repeat;
    background-size: auto;
    margin: 0;
    padding: 0;
    border-left:10px solid #c30d14;
  }

  /* Updated hero container with flex for horizontal images */
  .hero-image-container {
    position: relative;
    width: 100%;
    height: 45vh;
    background-color:#ecebe6;
    display: flex;
    align-items: center;
    padding: 20px 100px 0 20px;
    box-sizing: border-box;
  }

  /* Left image container: half width and crop overflow */
  .hero-Hero_Image_2 {
    width: 50%;
    height: 100%;
    overflow: hidden;
    margin-right: 10px;
    display: flex;
    align-items: center;
  }

  /* Left image shifted left to crop half */
  .hero-Hero_Image_2 img {
    height: 100%;
    object-fit: cover;
    object-position: left center;
    transform: translateX(-20%);
  }

  /* Right image container: half width, full image visible */
  .hero-image {
    width: 50%;
    height: 100%;
    display: flex;
    align-items: center;
  }

  .hero-image img {
    height: 100%;
    object-fit: contain;
    object-position: right top;
    margin-left: auto;
  }

  .hero-image-container button {
    font-weight: 100px;  
    border-radius: 30px;
    border:none;
    background-color:#0e3b65;
    color:#fff;
    padding: 0.8rem;
    width: 200px;
    opacity:1;
    margin:20px 20px;
    text-decoration:none;

  }
  .hero-image-container button:hover{
      border-color:#665c50;
      background-color:#f7ae21;
      color:#fff;
      opacity: 0.5;
  }

  .top-nav {
   display:flex;
   position: absolute;
   top: 5px;
   right: 30px;
   z-index: 1;
   gap: 690px;
   font-weight: 600;
   font-family: Arial, sans-serif;
   padding-bottom:15px;
  }

  .top-nav .options {
  display: flex;
  gap: 20px;
  white-space: nowrap;
  }

  .top-nav .name{
      margin-left:10px;
      color: #fc4047;
      font-size:25px;
      font-family: 'Trebuchet MS', sans-serif;
  }

  .top-nav a,
  .top-nav span {
    color:#057eeb;
    text-decoration: none;
    padding: 8px 12px;
    border-radius: 4px;
    transition: background-color 0.3s ease;
  }

  .top-nav a:hover {
    background-color: rgba(247, 212, 108, 0.2);
    border-radius:30px;
  }

  .hero-text-container {
    position: absolute;
    top: 10%;
   margin-left:300px;
    color:#fff;
    max-width: 500px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    z-index: 10;
    text-align:left;
    align-tems:center;
  }

  .hero-image-container p {
    font-family: 'Poppins', sans-serif;
    font-weight: 300;
    font-style: italic;
    font-size: 30px;

  }

  @keyframes slideInFromLeft {
    0% {
      opacity: 0;
      transform: translateX(-100px);
    }
    100% {
      opacity: 1;
      transform: translateX(0);
    }
  }

  .slide-in {
    animation: slideInFromLeft 1s ease forwards;
  }

  #testimonial{
      text-align:center;
  }

  #testimonials p {
    font-weight: 100;
    text-align: center;
    margin-bottom: 30px;
    font-size: 30px;
    font-style:italic;
    text-decoration: underline;
    text-decoration-thickness:0.5px;
  }

  #testimonials ul {
    display: flex;
    flex-wrap: nowrap;
    gap: 20px;
    overflow-x: auto;
    padding: 0;
    margin: 0 auto;
    max-width: 90%;
    list-style: none;
  }

  #testimonials li {
    flex: 0 0 auto;
    width: 300px;
    padding: 25px 30px;
    text-align: center;
    background-color: rgb(102, 92, 80, 0.1);
    box-shadow: 0 0 15px rgba(255, 255, 255, 0.05);
    border-radius:10px;
  }

  #testimonials li.visible {
    opacity: 1;
    transform: translateY(0);
  }

  #how-it-works {
  text-align: center;
  padding: 40px 20px;
  
  }

  #how-it-works h2 {
  margin-top:-30px;
  color:#0e3b65;
  font-size: 2rem;
  margin-bottom: 30px;
  }

  .steps {
  display: flex;
  justify-content: center;
  gap: 100px;
  color:#fff;
  }

  .step {
  display: flex;
  flex-direction: column;
  align-items: center;
  max-width: 250px;
  }

  .step .icon {
  width: 200px;
  height: 200px;
  border-radius: 50%;
  overflow: hidden;
  display: flex;
  justify-content: center;
  align-items: center;
  margin-bottom: 12px;
  background-color:rgb(247, 174, 33,0.2);
  }

  .step .icon img {
  width: 130px;
  height: 130px;
  object-fit: cover;
  }

  .step .text {
      border-radius:30px;
   padding:0.8rem;
   background-color:rgb(247, 174, 33,0.2);
  font-weight: 100;
  font-size: 1.1rem;
  }

  .iframe-cropper {
  width: 345px;
  height: 359px;
  overflow: hidden;
  position: relative;
  }

  .iframe-cropper iframe {
  position: relative;
  left: -80px;
  top: -50px;
  width: 500px;
  height: 500px;
  border: none;
  }
 
    #top-graduates {
  padding: 40px 20px;
  text-align: center;
}

.graduate-cards {
  display: flex;
  justify-content: center;
  flex-wrap: wrap;
  gap: 30px;
  margin-top: 20px;
}

.graduate-card {
  background-color: #ffffff;
  border: 1px solid #ddd;
  border-radius: 12px;
  padding: 20px;
  width: 280px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.05);
  text-align: center;
  font-family: 'Poppins', sans-serif;
}

.grad-img img {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  object-fit: cover;
  margin-bottom: 15px;
  border: 3px solid #f7ae21;
}

.graduate-card h3 {
  font-size: 1.2rem;
  color: #0e3b65;
  margin: 10px 0 5px;
}

.graduate-card .field {
  color: #fc4047;
  font-style: italic;
  font-size: 0.95rem;
}

.graduate-card .about {
  font-size: 0.85rem;
  color: #444;
  margin: 10px 0;
}

.view-profile {
  display: inline-block;
  margin-top: 10px;
  padding: 8px 16px;
  background-color: #057eeb;
  color: #fff;
  border-radius: 20px;
  text-decoration: none;
  font-size: 0.9rem;
  transition: background-color 0.3s ease;
}

.view-profile:hover {
  background-color: #0e3b65;
}

footer{
    background-color:rgb(195, 13, 20,0.3);
    text-align:center;
    padding:1rem;
    margin-top:25px;
}

  </style>

</head>
<body>
    <div class="hero-image-container">
        <div class="hero-Hero_Image_2">
            <img src="images/Hero_Image_2.jpg" alt="Freelancer Working" class="hero-img" />
        </div>
        <div class="hero-image">
            <img src="images/Hero_Image_2.jpg" alt="Freelancer Working" class="hero-img" />
        </div>

        <nav class="top-nav">
            <div class="name">
                FreeLanceHub
            </div>
            <div class="options">
                <a href="PHP/Homepage.php">Home</a>
                <?php if ($is_logged_in): ?>
                    <span>Welcome, <?= htmlspecialchars($user_name) ?></span>
                <?php else: ?>
                    <a href="PHP/Login.php">Login</a>
                    <a href="Html/Register.html">Register</a>
                <?php endif; ?>
                <!-- Explicit logout button always visible -->
                <a href="PHP/Logout.php">Logout</a>
            </div>
        </nav>

        <div class="hero-text-container slide-in">
            <p>Earn on Your Own Terms — Turn Your Skills into Income</p>
            <a href="<?= $is_logged_in
                ? ($role === 'admin' ? 'PHP/admin_dashboard.php'
                    : ($role === 'buyer' ? 'PHP/buyer_dashboard.php'
                        : 'PHP/Seller_Dashboard.php'))
                : 'Html/Register.html' ?>">
                <button>Get Started</button>
            </a>
        </div>
    </div>


    <main>

                <section id="top-graduates">
        <h2>Top Graduates</h2>
        <div class="graduate-cards">
            <?php if ($top_graduates_query): ?>
                <?php while ($grad = $top_graduates_query->fetch_assoc()): ?>
                    <div class="graduate-card">
                        <div class="grad-img">
                            <img src="<?= htmlspecialchars($grad['profile_image'] ?: 'images/default-avatar.png') ?>" alt="Profile Image" />
                        </div>
                        <h3><?= htmlspecialchars($grad['fname'] . ' ' . $grad['lname']) ?></h3>
                        <p class="field"><?= htmlspecialchars($grad['field_of_study']) ?></p>
                        <p class="about"><?= htmlspecialchars(mb_strimwidth($grad['about_me'], 0, 100, '...')) ?></p>
                        <a href="#" class="view-profile">View Profile</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No graduates found.</p>
            <?php endif; ?>
        </div>
        </section>

    
            <section id="how-it-works">
                <h2>How It Works</h2>
                <div class="steps">
                    <div class="step">
                    <div class="icon">
                        <img src="images/Sign_Up_2.jpg" alt="Sign Up" width="48" height="48" />
                    </div>
                    <div class="text">Sign Up</div>
                    </div>
                    <div class="step">
                    <div class="icon">
                        <img src="images/Services_3.jpg" alt="Sign Up" width="48" height="48" />
                    </div>
                    <div class="text">Offer/Request Services</div>
                    </div>
                    <div class="step">
                    <div class="icon">
                        <img src="images/earn.jpg" alt="Sign Up" width="48" height="48" />
                    </div>
                    <div class="text">Earn or Hire</div>
                    </div>
                </div>
                </section> 

        <section id="testimonials">
            <p>What are users saying</p>
            <ul>
                <?php if ($testimonials_query): ?>
                    <?php while ($testimonial = $testimonials_query->fetch_assoc()): ?>
                        <li>
                            "<?= htmlspecialchars($testimonial['message']) ?>"<br>
                            <em>- <?= htmlspecialchars($testimonial['fname'] . ' ' . $testimonial['lname']) ?></em>
                        </li>
                    <?php endwhile; ?>
                <?php else: ?>
                    <li>No testimonials available.</li>
                <?php endif; ?>
            </ul>
        </section>


    <footer>
        <p>&copy; <?= date('Y') ?> Graduate Marketplace. All rights reserved.</p>
    </footer>

    <script>
  document.addEventListener("DOMContentLoaded", function () {
    const testimonials = document.querySelectorAll("#testimonials li");

    function revealTestimonials() {
      const windowHeight = window.innerHeight;
      testimonials.forEach(testimonial => {
        const top = testimonial.getBoundingClientRect().top;

        if (top < windowHeight - 50) { // 50px before it enters viewport
          testimonial.classList.add("visible");
        }
      });
    }

    window.addEventListener("scroll", revealTestimonials);
    revealTestimonials();  // initial check
  });
</script>

</body>
</html>


