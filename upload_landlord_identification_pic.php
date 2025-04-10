<?php
session_start(); // Start the session

// Database configuration
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "bhouses_db"; 

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
        mkdir($uploadDir, 0755, true);
    }

    // Initialize error message variable
    $errorMessage = '';

    // Check if the landlord ID is set in the session
    if (!isset($_SESSION['landlord_id'])) {
        $errorMessage .= "Landlord ID is not set in the session. ";
    } else {
        $landlord_id = $_SESSION['landlord_id'];
    }

    // Check if the identification picture is uploaded
    if (!isset($_FILES['landlord_identification_pic'])) {
        $errorMessage .= "No file uploaded. ";
    } elseif ($_FILES['landlord_identification_pic']['error'] !== UPLOAD_ERR_OK) {
        $errorMessage .= "Error uploading the identification picture. ";
    }

    // If there are any error messages, display them
    if (!empty($errorMessage)) {
        echo $errorMessage;
        exit; // Stop further execution
    }

    // Proceed with file upload and database update
    $fileName = basename($_FILES['landlord_identification_pic']['name']);
    $filePath = $uploadDir . $fileName;

    // Move the uploaded file to the designated directory
    if (move_uploaded_file($_FILES['landlord_identification_pic']['tmp_name'], $filePath)) {
        // Update the landlord_identification_pic in the database
        $stmt = $conn->prepare("UPDATE landlord_registration SET landlord_identification_pic = ? WHERE id = ?");
        $stmt->bind_param("si", $fileName, $landlord_id);

        if ($stmt->execute()) {
            echo "Identification picture uploaded successfully.";
        } else {
            echo "Error updating record: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error moving the uploaded file.";
    }
}

// Close the database connection
$conn->close();
?>