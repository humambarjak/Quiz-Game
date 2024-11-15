<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

require 'database_connection.php'; // Include your database connection here

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_SESSION['username'];
    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    // Fetch the current password from the database
    $stmt = $pdo->prepare("SELECT password FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    // Verify the current password
    if (password_verify($currentPassword, $user['password'])) {
        // Check if new password and confirm password match
        if ($newPassword === $confirmPassword) {
            // Hash the new password
            $newPasswordHashed = password_hash($newPassword, PASSWORD_DEFAULT);

            // Update the password in the database
            $updateStmt = $pdo->prepare("UPDATE users SET password = :newPassword WHERE username = :username");
            $updateStmt->execute(['newPassword' => $newPasswordHashed, 'username' => $username]);

            echo "Password changed successfully!";
        } else {
            echo "New passwords do not match!";
        }
    } else {
        echo "Current password is incorrect!";
    }
}
?>
