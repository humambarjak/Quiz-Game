<?php
// Connect to the MySQL database
$conn = new mysqli('localhost', 'root', '', 'crypto-wallet');

// Check the database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and capture the input
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Check for empty fields (basic validation)
    if (empty($username) || empty($password)) {
        echo "Please fill in both username and password!";
        exit();
    }

    // Hash the password before storing it
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Check if the username already exists
    $query = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        echo "Username already exists!";
    } else {
        // Insert the new user into the database
        $query = "INSERT INTO users (username, password) VALUES ('$username', '$hashed_password')";

        if ($conn->query($query) === TRUE) {
            echo "Registration successful!";  // Debugging success message
            // Redirect to login page after registration
            header("Location: login.php");
            exit();
        } else {
            // Debugging any error during insertion
            echo "Error inserting user: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body class="dark-theme">
    <div class="center-wrapper">
        <div class="register-container futuristic-card">
            <h2 class="form-title">Create Your Account</h2>
            <form action="register.php" method="post">
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <input class="futuristic-button" type="submit" value="Register">
            </form>
            <p class="form-footer">Already have an account? <a href="login.php">Login</a></p>
        </div>
    </div>
</body>
</html>
