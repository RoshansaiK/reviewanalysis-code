<?php
session_start();


if (!isset($_SESSION['email'])) {
    header('Location: index.php');
    exit();
}


$role = $_SESSION['role'];


include('db.connection.php');


$email = $_SESSION['email'];
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Update user details in the database when the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $phone_number = $_POST['phone_number'];
    $location = $_POST['location'];
    $website = $_POST['website'];
    $description = $_POST['description'];

    $update_sql = "UPDATE users SET first_name = ?, last_name = ?, date_of_birth = ?, gender = ?, phone_number = ?, location = ?, website = ?, description = ? WHERE email = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param('sssssssss', $first_name, $last_name, $date_of_birth, $gender, $phone_number, $location, $website, $description, $email);
    
    if ($update_stmt->execute()) {
        $success_message = "Profile updated successfully!";
    } else {
        $error_message = "There was an error updating the profile.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
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
            background-color: #8c52a1;
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

        /* Main Settings Container */
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 1000px;
            margin: 20px auto;
        }

        h2 {
            font-size: 32px;
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        /* Form Styles */
        form {
            display: flex;
            flex-direction: column;
        }

        label {
            font-size: 16px;
            margin-bottom: 8px;
            color: #333;
        }

        input[type="text"], input[type="email"], input[type="date"], input[type="tel"], input[type="url"], textarea {
            padding: 10px;
            margin-bottom: 20px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        textarea {
            resize: vertical;
            height: 100px;
        }

        button {
            padding: 10px 20px;
            background-color: #8c52a1;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #3b4a6c;
        }

        .message {
            text-align: center;
            color: green;
            font-weight: bold;
        }

        .error-message {
            text-align: center;
            color: red;
            font-weight: bold;
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
                <li><a href="profile.php">Profile</a></li>
                <li><a href="dashboard.php" class="logout-icon"><i class="fa fa-sign-out"></i> Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="container">
    <h2>Update Your Profile</h2>

    <?php if (isset($success_message)) { ?>
        <p class="message"><?php echo $success_message; ?></p>
    <?php } elseif (isset($error_message)) { ?>
        <p class="error-message"><?php echo $error_message; ?></p>
    <?php } ?>

    <form method="POST" action="settings.php">
        <label for="first_name">First Name:</label>
        <input type="text" name="first_name" id="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>

        <label for="last_name">Last Name:</label>
        <input type="text" name="last_name" id="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>

        <label for="date_of_birth">Date of Birth:</label>
        <input type="date" name="date_of_birth" id="date_of_birth" value="<?php echo htmlspecialchars($user['date_of_birth']); ?>" required>

        <label for="gender">Gender:</label>
        <input type="text" name="gender" id="gender" value="<?php echo htmlspecialchars($user['gender']); ?>" required>

        <label for="phone_number">Phone Number:</label>
        <input type="tel" name="phone_number" id="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>

        <label for="location">Location:</label>
        <input type="text" name="location" id="location" value="<?php echo htmlspecialchars($user['location']); ?>" required>

        <label for="website">Website:</label>
        <input type="url" name="website" id="website" value="<?php echo htmlspecialchars($user['website']); ?>">

        <label for="description">Description:</label>
        <textarea name="description" id="description" required><?php echo htmlspecialchars($user['description']); ?></textarea>

        <button type="submit">Update Profile</button>
    </form>
</div>

</body>
</html>

