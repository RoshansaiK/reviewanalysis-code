<?php
session_start();

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION['email'])) {
    header('Location: index.php');
    exit();
}

// Check if user is admin
if ($_SESSION['role'] != 1) {
    echo "Access Denied";
    exit();
}

// Database connection (assuming it's in db.connection.php)
include('db.connection.php');

// Fetch all users
$sql = "SELECT * FROM users";
$result = $conn->query($sql);

// Handle Add User
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $location = $_POST['location'];
    $phone_number = $_POST['phone_number'];
    $website = $_POST['website'];
    $description = $_POST['description'];

    $sql = "INSERT INTO users (email, password, role, first_name, last_name, date_of_birth, gender, location, phone_number, website, description) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssissssssss', $email, $password, $role, $first_name, $last_name, $date_of_birth, $gender, $location, $phone_number, $website, $description);
    $stmt->execute();

    header("Location: manage-users.php");
    exit();
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link
      href="https://i.pinimg.com/originals/75/db/80/75db80642e75acc0f8514572065964ac.png"
      rel="shortcut icon"
      type="image/x-icon"
    />
    <style>
       body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(rgba(255, 255, 255, 0.5), rgba(255, 255, 255, 0.5)), 
                url('https://media-cldnry.s-nbcnews.com/image/upload/rockcms/2023-02/230203-chatgpt-test-scanning-le-1436-230b9d.jpg') no-repeat center center/cover;
            color: #333;
        }

        header {
            background-color: #8c52a1;
            color: white;
            padding: 10px;
            text-align: center;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #8c52a1;
            color: white;
        }

        .btn {
            padding: 8px 15px;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-add {
            background-color: #28a745;
        }

        .btn-edit {
            background-color: #007bff;
        }

        .btn-delete {
            background-color: #dc3545;
        }

        .btn:hover {
            opacity: 0.8;
        }

        .popup {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .popup-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            width: 400px;
        }

        .popup-content h3 {
            margin-top: 0;
        }

        .popup-content form {
            display: flex;
            flex-direction: column;
        }

        .popup-content form input, .popup-content form select, .popup-content form button {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        
    </style>
</head>
<body>
    <header>
        <h1>Manage Users</h1>
        
    </header>
   
    <div class="container">
        <h2>User Management</h2>
        <button class="btn btn-add" onclick="openPopup()">Add User</button>

        <div class="table-wrapper">
        <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Email</th>
            <th>Password</th>
            <th>Role</th>
            <th>Description</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Date of Birth</th>
            <th>Gender</th>
            <th>Location</th>
            <th>Phone Number</th>
            <th>Website</th>
           
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['user_id']; ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['password']); ?></td>
                <td><?php echo $row['role'] == 1 ? 'Admin' : 'User'; ?></td>
                <td><?php echo htmlspecialchars($row['description']); ?></td>
                <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                <td><?php echo htmlspecialchars($row['date_of_birth']); ?></td>
                <td><?php echo htmlspecialchars($row['gender']); ?></td>
                <td><?php echo htmlspecialchars($row['location']); ?></td>
                <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                <td><?php echo htmlspecialchars($row['website']); ?></td>
                
            </tr>
        <?php } ?>
    </tbody>
</table>

        </div>
    </div>

    <div class="popup" id="popup">
        <div class="popup-content">
            <h3>Add User</h3>
            <form method="POST">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <select name="role">
                    <option value="0">User</option>
                    <option value="1">Admin</option>
                </select>
                <input type="text" name="first_name" placeholder="First Name" required>
                <input type="text" name="last_name" placeholder="Last Name" required>
                <input type="date" name="date_of_birth" required>
                <input type="text" name="gender" placeholder="Gender" required>
                <input type="text" name="location" placeholder="Location" required>
                <input type="text" name="phone_number" placeholder="Phone Number" required>
                <input type="text" name="website" placeholder="Website">
                <textarea name="description" placeholder="Description"></textarea>
                <button type="submit" name="add_user" class="btn btn-add">Add User</button>
            </form>
        </div>
    </div>
    

    <script>
        function openPopup() {
            document.getElementById('popup').style.display = 'flex';
        }

        function closePopup() {
            document.getElementById('popup').style.display = 'none';
        }

        // Close popup when clicking outside the content
        window.onclick = function(event) {
            const popup = document.getElementById('popup');
            if (event.target === popup) {
                closePopup();
            }
        };
    </script>
</body>
</html>
