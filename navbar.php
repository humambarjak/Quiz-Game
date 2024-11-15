<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$username = $_SESSION['username'] ?? '';

// Check if a profile picture exists for the logged-in user
$profilePic = "uploads/" . $username . "_profile.jpg";
if (!file_exists($profilePic)) {
    $profilePic = "default_profile.png"; // Path to a default profile image if no profile picture is uploaded
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<nav class="futuristic-navbar">
    <ul>
        <!-- Fancy Dashboard Button -->
        <li>
            <a href="dashboard.php">
                <button class="button" data-text="Dashboard">
                    <span class="actual-text">&nbsp;Dashboard&nbsp;</span>
                    <span aria-hidden="true" class="hover-text">&nbsp;Dashboard&nbsp;</span>
                </button>
            </a>
        </li>
        <!-- Regular Links for Wallet and Profile -->
        <li><a href="Home.php">Home</a></li>
        <li><a href="wallet.php">Crypto Wallet</a></li>
        <li><a href="profile.php">Profile</a></li>
        <li><a href="exchanges.php">Exchanges</a></li>
        <li><a href="history.php">History</a></li>
    </ul>

    <!-- Profile Picture and Logout -->
    <div class="profile-menu">
        <img src="<?php echo $profilePic; ?>" alt="Profile Picture" class="navbar-profile-pic" id="navbar-profile-pic">
        <a href="logout.php">Logout</a>
    </div>
</nav>

<!-- Modal for Enlarged Profile Picture -->
<div id="profileModal" class="profile-modal">
    <span class="close-modal">&times;</span>
    <img class="modal-content" id="profileModalImage">
</div>

<script>
    // Get elements
    var modal = document.getElementById('profileModal');
    var modalImg = document.getElementById('profileModalImage');
    var profilePic = document.getElementById('navbar-profile-pic');
    var closeModal = document.getElementsByClassName('close-modal')[0];

    // When the profile pic is clicked, show the modal
    profilePic.onclick = function() {
        modal.style.display = "block";
        modalImg.src = this.src;
    }

    // When the user clicks on the close button, close the modal
    closeModal.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the image, close the modal
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

</body>
</html>