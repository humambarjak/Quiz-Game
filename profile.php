<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}
?>
<?php include 'navbar.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="styles.css"> 
</head>
<body class="dark-theme">
    <div class="profile-container futuristic-card">
        <h1 class="dashboard-title">Profile Page for <?php echo $_SESSION['username']; ?></h1>

        <!-- Display Profile Picture -->
        <div>
            <?php
            $profilePic = "uploads/" . $_SESSION['username'] . "_profile.jpg";
            if (file_exists($profilePic)) {
                echo "<img src='$profilePic' alt='Profile Picture' class='profile-picture'>";
            } else {
                echo "<img src='default_profile.png' alt='Default Profile Picture' class='profile-picture'>";
            }
            ?>
        </div>

        <!-- Upload Form for Profile Picture -->
        <form action="upload_profile_picture.php" method="post" enctype="multipart/form-data" class="upload-form">
            <label for="profilePicture" class="form-label">Upload a new profile picture:</label>
            <input type="file" name="profilePicture" id="profilePicture" accept="image/*" required class="futuristic-input">
            <button type="submit" class="futuristic-button">Upload</button>
        </form>

        <!-- Password Change Form -->
        <form action="change_password.php" method="post" class="change-password-form">
            <h2 class="form-title">Change Password</h2>
            <div class="input-group">
                <label for="currentPassword">Current Password:</label>
                <input type="password" name="currentPassword" id="currentPassword" required class="futuristic-input">
            </div>
            <div class="input-group">
                <label for="newPassword">New Password:</label>
                <input type="password" name="newPassword" id="newPassword" required class="futuristic-input">
            </div>
            <div class="input-group">
                <label for="confirmPassword">Confirm New Password:</label>
                <input type="password" name="confirmPassword" id="confirmPassword" required class="futuristic-input">
            </div>
            <button type="submit" class="futuristic-button">Change Password</button>
        </form>
    </div>
</body>
</html>
