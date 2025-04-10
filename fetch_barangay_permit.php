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

// Get the landlord ID from the query string
$landlordId = isset($_GET['id']) ? intval($_GET['id']) : 0; // Ensure it's an integer

// Query to fetch the Barangay Certificate image URL
$sql = "SELECT barangay_permit FROM landlord_registration WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $landlordId); // "i" indicates the type is integer
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $imageUrl = 'uploads/' . $row['barangay_permit']; // Assuming the image is stored in the uploads directory
    echo json_encode(['success' => true, 'image_url' => $imageUrl]);
} else {
    echo json_encode(['success' => false, 'message' => 'No Barangay Certificate found.']);
}

$stmt->close();
$conn->close();
?>