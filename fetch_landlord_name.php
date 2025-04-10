<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = ""; // No password
$dbname = "bhouses_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch the landlord's name and permanent address
$sql = "SELECT full_name, permanent_address FROM landlord_log LIMIT 1"; // Adjust the query as needed
$result = $conn->query($sql);

$response = array();

if ($result->num_rows > 0) {
    // Fetch the result as an associative array
    while ($row = $result->fetch_assoc()) {
        $response[] = $row;
    }
} else {
    $response['error'] = "No landlord found.";
}

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);

// Close the connection
$conn->close();
?>