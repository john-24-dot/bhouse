<?php
session_start(); // Start the session

// Database configuration
$servername = "localhost"; // Change if your database server is different
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "bhouses_db"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Define the upload directory
    $uploadDir = 'uploads/';

    // Ensure the upload directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true); // Create the directory if it doesn't exist
    }

    // Check if a file was uploaded
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == UPLOAD_ERR_OK) {
        $profile_picture = basename($_FILES['profile_pic']['name']); // Get the file name
        $profile_picture_path = $uploadDir . $profile_picture; // Full path to save the file

        // Move the uploaded file to the designated directory
        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $profile_picture_path)) {
            // Get the student ID from the session
            $student_id = $_SESSION['student_id']; // Assuming you store the student ID in the session

            // Prepare and bind the SQL statement to update the identification picture
            $stmt = $conn->prepare("UPDATE student_reg SET identification_pic = ? WHERE id = ?");
            $stmt->bind_param("si", $profile_picture, $student_id);

            // Execute the statement
            if ($stmt->execute()) {
                echo "Profile picture updated successfully.";
            } else {
                echo "Error updating profile picture: " . $stmt->error;
            }

            // Close the statement
            $stmt->close();
        } else {
            echo "Error moving uploaded file.";
        }
    } else {
        echo "No file uploaded or there was an upload error.";
    }
}

// Close the database connection
$conn->close();
?>