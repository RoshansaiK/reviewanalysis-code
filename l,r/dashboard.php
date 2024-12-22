<?php
session_start();

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION['email'])) {
    header('Location: index.php');
    exit();
}

// User's role (0 for user, 1 for admin)
$role = $_SESSION['role'];

// Database connection (assuming it's in db.connection.php)
include('db.connection.php');

// Fetch user details from the database using the session email
$email = $_SESSION['email'];
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link
      href="https://i.pinimg.com/originals/75/db/80/75db80642e75acc0f8514572065964ac.png"
      rel="shortcut icon"
      type="image/x-icon"
    />
    <style>
       /* General Styles */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
    height: 100vh;
    display: flex;
    flex-direction: column;
    position: relative;
    overflow: hidden; /* Ensure no scrollbars due to pseudo-element */
}

body::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: url('https://media-cldnry.s-nbcnews.com/image/upload/rockcms/2023-02/230203-chatgpt-test-scanning-le-1436-230b9d.jpg') no-repeat center center/cover;
    filter: blur(8px); /* Add the blur effect */
    z-index: -1; /* Ensure it stays behind the content */
    opacity: 0.8; /* Optional: Adjust visibility */
}



        /* Navbar Styles */
        header {
            background-color: white;
            color: #fff;
            padding: 10px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Logo and Name Styles */
        header img {
            height: 40px;
            margin-left: 20px;
        }

        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex: 1;
        }

        .navbar h1 {
    font-size: 24px;
    margin-left: 10px;
    font-weight: bold;
    color: white; /* Main text color */
    -webkit-text-stroke: 1px #6e6273; /* Purple stroke */
    text-shadow: 0 0 2px #8c52a1; /* Additional shadow for a glowing effect */
    font-family: 'Cloud', sans-serif; /* Custom font for "Cloud" look (use a font like 'Poppins', 'Roboto', or add your own) */
}


        .navbar nav ul {
            list-style: none;
            padding: 0;
            display: flex;
            align-items: center;
        }

        .navbar nav ul li {
            display: inline;
            margin-left: 20px;
        }

        .navbar nav ul li a {
            color: #fff;
            text-decoration: none;
            font-size: 16px;
        }

        .navbar nav ul li a:hover {
            text-decoration: underline;
        }

        .logout-icon {
    font-size: 20px;
    cursor: pointer;
    margin-left: 20px;
    color: #8c52a1;  /* Apply color to both the icon and text */
    text-decoration: none;  /* Optional: Remove underline from the link */
}

.logout-icon i {
    font-size: 30px;  /* Increase icon size */
    color: #8c52a1;  /* Ensure the icon gets the same color */
}





        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background-color: #8c52a1;
            color: #fff;
            position: fixed;
            top: 60px; 
            left: 0;
            height: calc(100% - 60px); 
            padding-top: 30px;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
        }

        .sidebar .menu-item {
            padding: 15px;
            display: flex;
            align-items: center;
            color: #fff;
            text-decoration: none;
            font-size: 18px;
            margin-bottom: 10px;
        }

        .sidebar .menu-item:hover {
            background-color: #D8BFD8;
            border-radius: 5px;
        }

        .sidebar .menu-item i {
            margin-right: 10px;
        }

        /* Main Content Styles */
        .main-content {
            margin-left: 250px;
            padding-top: 60px;
            padding: 20px;
            flex: 1;
            overflow-y: auto;
        }

        h2 {
            font-size: 28px;
            margin-bottom: 20px;
        }

      /* Overall Stats Layout */
.stats {
    display: flex;
    justify-content: center; /* Center the cards */
    gap: 20px; /* Space between cards */
    margin: 20px auto; /* Center the container vertically */
    max-width: 1200px; /* Restrict max width for layout */
}

/* Card Styles */
.card {
    background-color: #ffffff; /* White background */
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2); /* Subtle shadow for depth */
    width: 30%; /* Make all cards equal width */
    text-align: center;
    color: #333333; /* Text color for headings and paragraphs */
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    position: relative; /* To position the corner element */
    overflow: hidden; /* Ensure corner fold stays clean */
}

.card:hover {
    transform: translateY(-10px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.3); /* Slightly darker shadow on hover */
}

/* Corner Fold Style */
.corner-pin {
    position: absolute;
    top: 0;
    right: 0;
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #6e6273 50%, transparent 50%);
    clip-path: polygon(100% 0, 0 0, 100% 100%);
    z-index: 1; /* Ensure it overlays the card content */
}

.corner-pin i {
    position: absolute;
    top: 5px;
    right: 5px;
    font-size: 16px;
    color: #ffffff; /* White icon color */
}

/* Icon Styles for the Card */
.card i {
    font-size: 40px;
    margin-bottom: 15px;
    color: #6e6273; /* Dark gray color for icons */
    transition: transform 0.3s ease, color 0.3s ease;
}

.card:hover i {
    transform: scale(1.2) rotate(15deg); /* Scaling and slight rotation on hover */
}

/* Text Styling */
.card h3 {
    font-size: 20px;
    margin: 10px 0;
}

.card p {
    font-size: 14px;
    color: #666666;
}

/* Responsive Design */
@media (max-width: 768px) {
    .card {
        width: 90%; /* Full-width for smaller screens */
        margin: 10px auto;
    }

    .stats {
        flex-direction: column; /* Stack cards vertically */
        align-items: center;
    }
}


.card:nth-child(1):hover i {
    animation: bounce 0.6s infinite; /* Bouncing effect for the first icon */
}

.card:nth-child(2):hover i {
    animation: spin 0.6s linear infinite; /* Spinning effect for the second icon */
}

.card:nth-child(3):hover i {
    animation: pulse 0.8s infinite; /* Pulsing effect for the third icon */
}

/* Keyframes for Animations */
@keyframes bounce {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}

@keyframes glow {
    0% {
        text-shadow: 0 0 5px #ffd700, 0 0 10px #ffd700, 0 0 15px #ffd700;
        transform: scale(1);
    }
    50% {
        text-shadow: 0 0 10px #ffcc00, 0 0 20px #ffcc00, 0 0 30px #ffcc00;
        transform: scale(1.1);
    }
    100% {
        text-shadow: 0 0 5px #ffd700, 0 0 10px #ffd700, 0 0 15px #ffd700;
        transform: scale(1);
    }
}


@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
}




/* Modal Styles */
.modal {
    display: none; /* Hide the modal by default */
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7); /* Dark background */
    justify-content: center;
    align-items: center;
    animation: fadeIn 0.3s ease-in-out;
}

.modal-content {
    background-color: white;
    padding: 50px;
    border-radius: 8px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    width: 80%;
    max-width: 600px;
    transform: scale(0);
    animation: scaleUp 0.3s forwards;
}

.close {
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 30px;
    color: #333;
    cursor: pointer;
}

/* Modal fade-in animation */
@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

/* Modal scale-up animation */
@keyframes scaleUp {
    from {
        transform: scale(0);
    }
    to {
        transform: scale(1);
    }
}

/* Close Modal when clicked outside */
.modal:hover {
    display: flex;
}

@media (max-width: 768px) {
    .modal-content {
        width: 95%; /* Adjust width on smaller screens */
    }
}

/* Greeting Card */
.greeting-card {
    background-color: #F2F3F2; /* Light background */
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3); /* Darker shadow */
    width: 30%;
    margin: 20px auto 20px 829px; /* Add margin-left of 829px */
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    animation: slide-in 6s ease-out; /* Apply slide-in animation */
}

.greeting-card:hover {
    transform: translateY(-5px); /* Slight lift on hover */
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.4); /* Enhance shadow on hover */
}

.greeting {
    color: #77667d;
    font-size: 15px; /* Decreased text size */
    font-weight: bold;
    margin: 0;
}

.greeting-icon {
    font-size: 22px; /* Adjusted icon size for balance */
    margin-right: 10px; /* Space between icon and text */
}

/* Keyframes for animation */
@keyframes slide-in {
    0% {
        transform: translateX(-100%); /* Start off-screen to the left */
        opacity: 0; /* Invisible */
    }
    50% {
        transform: translateX(15%); /* Slight overshoot */
        opacity: 0.8; /* Gradually visible */
    }
    100% {
        transform: translateX(0); /* Final position */
        opacity: 1; /* Fully visible */
    }
}



    </style>
</head>
<body>
    <!-- Navbar (Fixed) -->
    <header>
        <div class="logo">
            <i class="fa fa-cloud" style="font-size: 40px; color: #6e6273;"></i> <!-- Cloud Icon -->
        </div>
        <div class="navbar">
            <h1>Cloud Based Code Review</h1>
            <nav>
                <ul>
                    <li><a href="logout.php" class="logout-icon"><i class="fa fa-sign-out"></i> Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>
   
<script>
    function openModal(modalId) {
    document.getElementById(modalId).style.display = 'flex';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}
</script>
    <!-- Sidebar -->
    <div class="sidebar">
        <a href="dashboard.php" class="menu-item"><i class="fa fa-home"></i> Dashboard</a>
        <a href="profile.php" class="menu-item"><i class="fa fa-user"></i> Profile</a>
        <a href="automated_code_review.php" class="menu-item"><i class="fa fa-cloud"></i> Automated Code Review</a>
        <a href="settings.php" class="menu-item"><i class="fa fa-cogs"></i> Settings</a>
        <!-- Help Section -->
        <a href="https://github.com/RoshansaiK" class="menu-item" target="_blank"><i class="fab fa-github"></i></i> GitHub</a>

        <a href="mailto:roshansaiketham67@gmail.com?subject=Cloud Based Code Review" class="menu-item"><i class="fa fa-question-circle"></i> Ask a Question</a>
    </div>
    

    <!-- Main Content -->
    <div class="main-content">
        <section class="content">
            <div class="container">
                <h2>Dashboard Overview</h2>
                <!-- Greeting -->
                <div class="greeting-card">
    <p class="greeting">
        <span class="greeting-icon">👋</span> 
        Hello, <?php echo htmlspecialchars($user['email']); ?>! Your role: <?php echo ($role == 1) ? 'Admin' : 'User'; ?>
    </p>
</div>

<div class="stats">
    <div class="card" onclick="openModal('activityModal')">
        <div class="corner-pin">
            <i class="fa fa-code"></i>
        </div>
        <i class="fa fa-tasks"></i>
        <h3>Your Activity</h3>
        <p>Here is your recent activity summary.</p>
    </div>
    <div class="card" onclick="openModal('cloudReviewModal')">
        <div class="corner-pin">
            <i class="fa fa-code"></i>
        </div>
        <i class="fa fa-cloud"></i>
        <h3>About Cloud Review</h3>
        <p>Get insights on automated code reviews, powered by the cloud. Manage your reviews with ease.</p>
    </div>
    <div class="card" onclick="openModal('notificationsModal')">
        <div class="corner-pin">
            <i class="fa fa-code"></i>
        </div>
        <i class="fa fa-bell"></i>
        <h3>Notifications</h3>
        <p>View recent notifications.</p>
    </div>
</div>

<!-- Modals -->
<div id="activityModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('activityModal')">&times;</span>
        <h3>Your Activity</h3>
        <p>Here is your detailed activity information...</p>
    </div>
</div>
<div id="cloudReviewModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('cloudReviewModal')">&times;</span>
        <h3>About Cloud Review</h3>
        <p>Details about automated code reviews...</p>
    </div>
</div>
<div id="notificationsModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('notificationsModal')">&times;</span>
        <h3>Notifications</h3>
        <p>Details about your notifications...</p>
    </div>
</div>



            </div>
            <div class="card-slider1">
  <div class="card11">
    <div class="image-box1">
      <img src="https://via.placeholder.com/150" alt="Sample Image" />
    </div>
    <div class="content1">
      <p class="date1">26 December 2023</p>
      <h3>GitHub Collaboration</h3>
      <p class="description1">
        Manage pull requests, collaborate on repositories, and push new changes for seamless teamwork.
      </p>
      <a href="https://github.com" class="read-more1">Go to Repository</a>
    </div>
  </div>
</div>



<style>
.card-slider1 {
  display: flex;
  justify-content: center;
  margin: 30px auto;
  font-family: Arial, sans-serif;
  position: relative;
}

.card11 {
  background: #fff;
  border-radius: 20px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  width: 600px;
  height: 250px;
  display: flex;
  position: relative;
  overflow: hidden;
}

.image-box1 {
  position: absolute;
  top: 50%;
  left: -70px;
  transform: translateY(-50%);
  width: 150px;
  height: 150px;
  background: #fff;
  border-radius: 20px;
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.image-box1 img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border: 3px solid rgba(0, 0, 0, 0.2);
}

.content1 {
  margin-left: 100px; /* Adjusted to account for the image overlap */
  padding: 20px;
}

.content1 .date1 {
  font-size: 14px;
  color: #777;
  margin-bottom: 10px;
}

.content1 h3 {
  font-size: 22px;
  color: #333;
  margin-bottom: 10px;
}

.content1 .description1 {
  font-size: 14px;
  color: #555;
  margin-bottom: 15px;
}

.content1 .read-more1 {
  background: #0366d6;
  color: #fff;
  border: none;
  border-radius: 20px;
  padding: 10px 20px;
  font-size: 14px;
  text-decoration: none;
  transition: background 0.3s;
}

.content1 .read-more1:hover {
  background: #024cad;
}

</style>

            
        </section>
    </div>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</body>
</html>
