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
    <title>Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link
      href="https://i.pinimg.com/originals/75/db/80/75db80642e75acc0f8514572065964ac.png"
      rel="shortcut icon"
      type="image/x-icon"
    />
    <style>
        /* General Styles */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(rgba(255, 255, 255, 0.5), rgba(255, 255, 255, 0.5)), 
                url('https://media-cldnry.s-nbcnews.com/image/upload/rockcms/2023-02/230203-chatgpt-test-scanning-le-1436-230b9d.jpg') no-repeat center center/cover;
            color: #333;
        }

        /* Navbar Styles */
        header {
            background-color:#8c52a1;
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
            color: white;
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
        }

        /* Main Profile Container */
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 1000px;
            margin: 20px auto;
            overflow: hidden;
        }

        h2 {
            font-size: 32px;
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        /* Profile Card Styles */
        .profile-card {
            background:#8c52a1 ;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
            color: white;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .profile-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
        }

        .profile-card h3 {
            font-size: 24px;
            margin-bottom: 15px;
        }

        .profile-card p {
            font-size: 16px;
            margin: 10px 0;
            display: flex;
            align-items: center;
        }

        .profile-card i {
            margin-right: 10px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            .profile-card {
                padding: 15px;
            }
            
            .navbar a {
                padding: 10px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

<header>
        <div class="logo">
            <i class="fa fa-cloud" style="font-size: 40px; color: white;"></i> <!-- Cloud Icon -->
        </div>
        <div class="navbar">
            <h1>Cloud Based CodeReview</h1>
            <nav>
                <ul>
                    <li><a href="dashboard.php" class="logout-icon"><i class="fa fa-sign-out"></i> Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>
    =

    <!-- Main Content Container -->
    <div class="container">
        <h2>Your Profile</h2>

        <!-- Basic Information Card -->
        <div class="profile-card">
            <h3>Basic Information</h3>
            <p><i class="fa fa-user"></i> Name: <?php echo htmlspecialchars($user['first_name']) . ' ' . htmlspecialchars($user['last_name']); ?></p>
            <p><i class="fa fa-envelope"></i> Email: <?php echo htmlspecialchars($user['email']); ?></p>
            <p><i class="fa fa-calendar"></i> Date of Birth: <?php echo htmlspecialchars($user['date_of_birth']); ?></p>
            <p><i class="fa fa-venus-mars"></i> Gender: <?php echo htmlspecialchars($user['gender']); ?></p>
        </div>

        <!-- Contact Information Card -->
        <div class="profile-card">
            <h3>Contact Information</h3>
            <p><i class="fa fa-phone"></i> Phone: <?php echo htmlspecialchars($user['phone_number']); ?></p>
            <p><i class="fa fa-location-arrow"></i> Location: <?php echo htmlspecialchars($user['location']); ?></p>
            <p><i class="fa fa-link"></i> Website: <a href="<?php echo htmlspecialchars($user['website']); ?>" target="_blank" style="color: #f0f8ff;"><?php echo htmlspecialchars($user['website']); ?></a></p>
        </div>

        <!-- Additional Information Card -->
        <div class="profile-card">
            <h3>Additional Information</h3>
            <p><i class="fa fa-id-card"></i> User Role: <?php echo ($role == 1) ? 'Admin' : 'User'; ?></p>
            <p><i class="fa fa-file-text"></i> Description: <?php echo htmlspecialchars($user['description']); ?></p>
        </div>

    </div>
</body>
</html>
