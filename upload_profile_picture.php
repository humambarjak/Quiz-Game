<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profilePicture'])) {
    $username = $_SESSION['username'];
    $uploadDir = 'uploads/';
    $fileName = $username . "_profile.jpg";
    $targetFile = $uploadDir . $fileName;

    // Ensure the uploads directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Check if the uploaded file is an image
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $check = getimagesize($_FILES['profilePicture']['tmp_name']);
    if ($check !== false && in_array($fileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        // Move the uploaded file to the uploads directory
        if (move_uploaded_file($_FILES['profilePicture']['tmp_name'], $targetFile)) {
            echo "Profile picture uploaded successfully!";
            header("Location: profile.php");
            exit();
        } else {
            echo "Error uploading the profile picture.";
        }
    } else {
        echo "Please upload a valid image file.";
    }
} else {
    echo "No file uploaded.";
}
?>
