<?php
$servername = "localhost";
$username = "root";
$password = ""; // No password
$dbname = "bhouses_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]));
}

// Get the input data
$data = json_decode(file_get_contents('php://input'), true);
$landlordId = $data['id'];

// Prepare and bind the SQL statement
$sql = "UPDATE landlord_registration SET landlord_status = 'approved' WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $landlordId); // "i" indicates the type is integer

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error updating status: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>