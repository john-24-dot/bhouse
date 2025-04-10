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

// Check if the landlord ID is set in the session
if (isset($_SESSION['landlord_id'])) {
    $landlord_id = $_SESSION['landlord_id'];

    // Prepare and execute the delete statement
    $stmt = $conn->prepare("DELETE FROM landlord_registration WHERE id = ?");
    $stmt->bind_param("i", $landlord_id);

    if ($stmt->execute()) {
        // Clear the session data
        session_destroy(); // Destroy the session
        echo json_encode(['redirect' => 'login_form.html']); // Redirect to login form
    } else {
        echo json_encode(['error' => 'Error deleting account: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['error' => 'No landlord ID provided.']);
}

// Close the database connection
$conn->close();
?>