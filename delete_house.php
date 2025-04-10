<?php
// delete_house.php

// Database connection
$host = 'localhost'; // Your database host
$db = 'bhouses_db'; // Your database name
$user = 'root'; // Your database username
$pass = ''; // Your database password

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed.']));
}

// Get the house ID from the query string
$houseId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($houseId > 0) {
    // Prepare the DELETE statement
    $stmt = $conn->prepare("DELETE FROM boarding_houses WHERE id = ?");
    $stmt->bind_param("i", $houseId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting house.']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid house ID.']);
}

$conn->close();
?>