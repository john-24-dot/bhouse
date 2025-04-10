<?php
session_start();
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

// Check if the student ID is set
if (isset($_GET['id'])) {
    $student_id = $_GET['id'];

    // Prepare and bind
    $sql = "DELETE FROM student_reg WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    
    // Execute the statement
    if ($stmt->execute()) {
        // Optionally, you can clear the session or perform other cleanup
        session_destroy(); // Destroy the session if needed
        // Return a neutral response
        echo json_encode(['redirect' => 'login_form.html']);
    } else {
        // Return an error response without revealing too much information
        echo json_encode(['error' => 'An error occurred.']);
    }

    // Close the statement
    $stmt->close();
} else {
    echo json_encode(['error' => 'No student ID provided.']);
}

// Close the database connection
$conn->close();
?>