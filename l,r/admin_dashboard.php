<?php
session_start();

// Check if user is logged in and if the user is an admin
if (!isset($_SESSION['email']) || $_SESSION['role'] != 1) {
    header('Location: index.php');
    exit();
}

// Database connection (assuming it's in db.connection.php)
include('db.connection.php');

// Fetch admin details from the database using the session email
$email = $_SESSION['email'];
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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

        .stats {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }

            /* Card Styles */
            .card {
    background-color: #ffffff; /* White background */
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2); /* Subtle shadow for depth */
    width: 30%;
    text-align: center;
    color: #333333; /* Text color for headings and paragraphs */
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-10px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.3); /* Slightly darker shadow on hover */
}

.card i {
    font-size: 40px;
    margin-bottom: 15px;
    color: #6e6273; /* Dark gray color for icons */
    transition: transform 0.3s ease, color 0.3s ease;
}


/* Icon Animation Styles */
.card:hover i {
    transform: scale(1.2) rotate(15deg); /* Scaling and slight rotation on hover */
}

/* Specific Icon Styles */
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

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
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
            <i class="fa fa-cloud" style="font-size: 40px; color:#6e6273;"></i> <!-- Cloud Icon -->
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

    <!-- Sidebar -->
    <div class="sidebar">
        <a href="dashboard.php" class="menu-item"><i class="fa fa-home"></i> Dashboard</a>
        <a href="profile.php" class="menu-item"><i class="fa fa-user"></i> Profile</a>
        <a href="manage-users.php" class="menu-item">
    <i class="fa fa-users"></i> Manage Users
</a>

       
        <a href="settings.php" class="menu-item"><i class="fa fa-cogs"></i> Settings</a>
        <!-- Help Section -->
        <a href="https://github.com/RoshansaiK" class="menu-item" target="_blank"><i class="fa fa-github"></i> GitHub</a>
        <a href="mailto:roshansaiketham67@gmail.com?subject=Cloud Based Code Review" class="menu-item"><i class="fa fa-question-circle"></i> Ask a Question</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <section class="content">
            <div class="container">
                <h2>Admin Dashboard</h2>
                <div class="greeting-card">
                <!-- Greeting -->
                <p class="greeting">
    <span class="greeting-icon">👋</span> 
    Hello, Admin <?php echo htmlspecialchars($admin['email']); ?>!
</p>
</div>


                <div class="stats">
                    
                    <div class="card">
                        <i class="fa fa-bar-chart"></i>
                        <h3>View Reports</h3>
                        <p>Analyze site performance and user activity.</p>
                    </div>
                    <div class="card">
                        <i class="fa fa-cogs"></i>
                        <h3>Site Settings</h3>
                        <p>Configure site-wide settings and preferences.</p>
                    </div>
                    <div class="card">
                        <i class="fa fa-bell"></i>
                        <h3>Notifications</h3>
                        <p>View recent notifications.</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</body>
</html>
