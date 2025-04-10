<?php
// reject_student.php

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
$student_id = $_POST['student_id'];

// Prepare and bind
$stmt = $conn->prepare("UPDATE student_reg SET student_status = 'rejected' WHERE id = ?");
$stmt->bind_param("i", $student_id);

// Execute the statement
if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Student rejected successfully."]);
} else {
    echo json_encode(["status" => "error", "message" => "Error rejecting student."]);
}

// Close connections
$stmt->close();
$conn->close();
?>