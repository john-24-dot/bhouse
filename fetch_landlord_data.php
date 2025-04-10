<?php
session_start(); // Start the session

// Database connection parameters
$servername = "localhost"; // Database server
$username = "root"; // Database username
$password = ""; // Your database password
$dbname = "bhouses_db"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in
if (isset($_SESSION['landlord_id'])) { // Check if landlord_id is set in the session
    $landlordId = $_SESSION['landlord_id'];
    
    // Prepare the SQL statement to fetch landlord details from the landlord_log table
    $query = "SELECT full_name, permanent_address, name_extension, profile_pic FROM landlord_log WHERE landlord_id = ? ORDER BY login_time DESC LIMIT 1";
    $stmt = $conn->prepare($query);
    
    // Bind the landlord_id parameter to the SQL query
    $stmt->bind_param("i", $landlordId);
    
    // Execute the prepared statement
    $stmt->execute();
    
    // Get the result of the query
    $result = $stmt->get_result();

    // Check if landlord details were found
    if ($result->num_rows > 0) {
        $landlord = $result->fetch_assoc(); // Fetch the landlord details as an associative array
        
        // Combine the name parts into a full name
        $fullName = trim($landlord['full_name'] . ' ' . $landlord['name_extension']);
        
        // Return the landlord details as JSON
        echo json_encode([
            'fullName' => $fullName,
            'permanentAddress' => $landlord['permanent_address'],
            'profilePic' => $landlord['profile_pic'] // Include the profile picture
        ]);
    } else {
        echo json_encode(['error' => 'Landlord not found']); // Return error if no landlord found
    }
} else {
    echo json_encode(['error' => 'User  not logged in']); // Return error if user is not logged in
}

// Close the database connection
$conn->close();
?>