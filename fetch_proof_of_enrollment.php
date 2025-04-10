<?php
// fetch_proof_of_enrollment.php

// Database connection
$servername = "localhost"; // Your server name
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "bhouses_db"; // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the student ID from the request
$student_id = $_GET['student_id'];

// Prepare the SQL statement to fetch the proof of enrollment
$stmt = $conn->prepare("SELECT proof_of_enrollment FROM student_reg WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch the proof of enrollment
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    // Construct the full path for the proof of enrollment file
    $proofOfEnrollmentPath = 'uploads/' . $row['proof_of_enrollment'];
    echo json_encode(["status" => "success", "proof_of_enrollment" => $proofOfEnrollmentPath]);
} else {
    echo json_encode(["status" => "error", "message" => "Proof of enrollment not found."]);
}

// Close connections
$stmt->close();
$conn->close();
?>