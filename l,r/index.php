<?php
// index.php

session_start();
include('db.connection.php');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        // Login logic
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Prepare SQL query to prevent SQL injection
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // User exists, check password
            $user = $result->fetch_assoc();
            
            // Assuming password is stored in plain text, use password_verify if hashed
            if ($password === $user['password']) {
                // Set session variables
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role']; // Assuming 'role' column exists

                // Redirect based on role
                if ($user['role'] == 0) {
                    header('Location: dashboard.php'); // User dashboard
                } elseif ($user['role'] == 1) {
                    header('Location: admin_dashboard.php'); // Admin dashboard
                }
                exit();
            } else {
                echo "<script>alert('Invalid email or password');</script>";
            }
        } else {
            echo "<script>alert('No user found with that email');</script>";
        }
    } elseif (isset($_POST['register'])) {
        // Register logic
        $user_id = $_POST['user_id'];  // Get user ID
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Check if email is already registered
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Email already exists, show an alert
            echo "<script>alert('This email is already registered. Please use a different email.');</script>";
        } else {
            // If email is not taken, proceed with registration
            $sql = "INSERT INTO users (user_id, email, password) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('sss', $user_id, $email, $password);

            if ($stmt->execute()) {
                echo "<script>alert('Registration Successful');</script>";
            } else {
                echo "<script>alert('Error: Unable to register');</script>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login and Registration Form</title>
    <link rel="stylesheet" href="style.css" />
    <link
      href="https://i.pinimg.com/originals/75/db/80/75db80642e75acc0f8514572065964ac.png"
      rel="shortcut icon"
      type="image/x-icon"
    />
</head>
<body>
    <div class="hero">
        <div class="form-box">
            <div class="button-box">
                <div id="btn"></div>
                <button type="button" class="toggle-btn" onclick="login()">Login</button>
                <button type="button" class="toggle-btn" onclick="register()">Register</button>
            </div>

            <div class="social-icons">
                <a href="https://www.facebook.com/roshansai.ketham.7/" target="_blank" rel="noopener noreferrer">
                    <img src="./images/facebook.png" alt="Facebook Logo" />
                </a>
                <a href="https://github.com/RoshansaiK" target="_blank" rel="noopener noreferrer">
                    <img src="./images/github.png" alt="GitHub Logo" />
                </a>
                <a href="https://www.linkedin.com/in/ketam-roshan-sai-0603b7230/" target="_blank" rel="noopener noreferrer">
                    <img src="./images/linkedin.png" alt="LinkedIn Logo" />
                </a>
            </div>

            <style>
                .social-icons {
                    display: flex;
                    justify-content: center;
                    gap: 15px;
                    padding: 10px;
                }

                .social-icons a {
                    display: inline-block;
                }

                .social-icons img {
                    width: 40px;
                    height: auto;
                }

                .social-icons a:hover img {
                    filter: brightness(1.2);
                }
            </style>

            <!-- Login form -->
            <form method="POST" class="input-group" id="login">
                <input
                    type="email"
                    class="input-field"
                    name="email"
                    placeholder="Email ID"
                    required
                />
                <input
                    type="password"
                    class="input-field"
                    name="password"
                    placeholder="Enter Password"
                    required
                />
                <input type="checkbox" class="check-box" /><span>Remember Password</span>
                <button type="submit" name="login" class="submit-btn">Login</button>
            </form>
            
            <!-- Registration form -->
            <form method="POST" class="input-group" id="register">
                <input
                    type="text"
                    class="input-field"
                    name="user_id"
                    placeholder="User ID"
                    required
                />
                <input
                    type="email"
                    class="input-field"
                    name="email"
                    placeholder="Email ID"
                    required
                />
                <input
                    type="password"
                    class="input-field"
                    name="password"
                    placeholder="Enter Password"
                    required
                />
                <input type="checkbox" class="check-box" /><span>I agree to the terms & conditions</span>
                <button type="submit" name="register" class="submit-btn">Register</button>
            </form>
        </div>
    </div>

    <script>
        var x = document.getElementById("login");
        var y = document.getElementById("register");
        var z = document.getElementById("btn");

        function register() {
            x.style.left = "-400px";
            y.style.left = "50px";
            z.style.left = "110px";
        }

        function login() {
            x.style.left = "50px";
            y.style.left = "450px";
            z.style.left = "0";
        }
    </script>
</body>
</html>
<style>
  * {
  margin: 0;
  padding: 0;
  font-family: sans-serif;
}

.hero {
  height: 100%;
  width: 100%;
  background-image: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)),
    url("https://media-cldnry.s-nbcnews.com/image/upload/rockcms/2023-02/230203-chatgpt-test-scanning-le-1436-230b9d.jpg");
  background-position: center;
  background-size: cover;
  position: absolute;
}

.form-box {
  width: 380px;
  height: 480px;
  position: relative;
  margin: 6% auto;
  background: #fff;
  padding: 5px;
  overflow: hidden;
}

.button-box {
  width: 220px;
  margin: 35px auto;
  position: relative;
  box-shadow: 0 0 20px 9px #b176c9; /* Changed to purple */
  border-radius: 30px;
}

.toggle-btn {
  padding: 10px 30px;
  cursor: pointer;
  background: transparent;
  border: none;
  outline: none;
  position: relative;
}

#btn {
  top: 0;
  left: 0;
  position: absolute;
  width: 110px;
  height: 100%;
  background: #8c52a1; /* Gradient from purple to light blue */
  border-radius: 30px;
  transition: 0.5s;
  color: white; /* White text for button */
  font-weight: bold; /* Bold text */
}

.social-icons {
  margin: 30px auto;
  text-align: center;
}

.social-icons img {
  width: 30px;
  margin: 0 12px;
  box-shadow: 0 0 20px 0 #b176c9; /* Changed to purple */
  cursor: pointer;
  border-radius: 50%;
}

.input-group {
  top: 180px;
  position: absolute;
  width: 280px;
  transition: 0.5s;
}

.input-field {
  width: 100%;
  padding: 10px 0;
  margin: 5px 0;
  border-left: 0;
  border-top: 0;
  border-right: 0;
  border-bottom: 1px solid #8c52a1; /* Changed to new purple */
  outline: none;
  background: transparent;
}

.submit-btn {
  width: 85%;
  padding: 10px 30px;
  cursor: pointer;
  display: block;
  margin: auto;
  background: #8c52a1; /* Gradient from purple to light blue */
  border: none;
  outline: none;
  border-radius: 30px;
  color: white; /* White text for button */
  font-weight: bold; /* Bold text */
}

.check-box {
  margin: 30px 10px 30px 0;
}

span {
  color: #b176c9; /* Changed to purple */
  font-size: 12px;
  bottom: 68px;
  position: absolute;
}

#login {
  left: 50px;
}

#register {
  left: 450px;
}

</style>
