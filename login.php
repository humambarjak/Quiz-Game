<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'crypto-wallet');

// Check if the form is submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);  // Trim any leading or trailing spaces
    $password = $_POST['password'];

    // Escape strings to prevent SQL injection
    $username = $conn->real_escape_string($username);

    // Query the database for the user
    $query = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        // Fetch user data
        $user = $result->fetch_assoc();

        // Verify the password entered by the user
        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $username;
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Invalid password!";
        }
    } else {
        echo "Username not found!";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body class="dark-theme">
    <div class="center-wrapper">
        <div class="login-container futuristic-card">
            <h2 class="form-title">Login to Your Account</h2>
            <form action="login.php" method="POST">
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <input class="futuristic-button" type="submit" value="Login">
            </form>
            <p class="form-footer">Don't have an account? <a href="register.php">Register</a></p>
        </div>
    </div>
</body>
</html>
