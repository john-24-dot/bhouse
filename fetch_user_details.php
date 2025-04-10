<?php
session_start(); // Start the session

// Database connection parameters
$servername = "localhost"; // Database server
$username = "root"; // Database username
$password = ""; // Database password (none in this case)
$dbname = "bhouses_db"; // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in
if (isset($_SESSION['student_id'])) { // Check if student_id is set in the session
    $studentId = $_SESSION['student_id'];
    
    // Prepare the SQL statement to fetch user details
    $query = "SELECT email_address, first_name, middle_name, last_name, permanent_address, college, course, profile_pic FROM student_log WHERE student_id = ?";
    $stmt = $conn->prepare($query);
    
    // Check if the statement was prepared successfully
    if (!$stmt) {
        echo json_encode(['error' => 'SQL statement preparation failed: ' . $conn->error]);
        exit();
    }

    // Bind the student_id parameter to the SQL query
    $stmt->bind_param("i", $studentId);
    
    // Execute the prepared statement
    if (!$stmt->execute()) {
        echo json_encode(['error' => 'SQL execution failed: ' . $stmt->error]);
        exit();
    }
    
    // Get the result of the query
    $result = $stmt->get_result();

    // Check if user details were found
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc(); // Fetch the user details as an associative array
        echo json_encode($user); // Return user details as JSON
    } else {
        echo json_encode(['error' => 'User  not found']); // Return error if no user found
    }
} else {
    echo json_encode(['error' => 'User  not logged in']); // Return error if user is not logged in
}

// Close the database connection
$conn->close();
?>