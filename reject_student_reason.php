<?php
// reject_student_reason.php

// Database connection
$servername = "localhost"; // Your server name
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "bhouses_db"; // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}

// Get the student ID and reason from the request
$student_id = isset($_POST['student_id']) ? trim($_POST['student_id']) : null; // Trim to remove spaces
$reason = isset($_POST['reason']) ? $_POST['reason'] : null;

// Debugging: Log the received values
error_log("Received student_id: " . var_export($student_id, true));
error_log("Received reason: " . var_export($reason, true));

// Check if student_id and reason are set and not empty
if (empty($student_id)) {
    error_log("Error: student_id is empty.");
    echo json_encode(["status" => "error", "message" => "Student ID is missing."]);
    exit;
}

if (empty($reason)) {
    error_log("Error: reason is empty.");
    echo json_encode(["status" => "error", "message" => "Reason is missing."]);
    exit;
}

// Step 1: Fetch the student status
$stmt = $conn->prepare("SELECT id, student_status FROM student_reg WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "No student found with the provided ID."]);
    $stmt->close();
    $conn->close();
    exit;
}

// Fetch the student data
$student = $result->fetch_assoc();
$current_status = $student['student_status'];

// Step 2: Update the reason
$stmt->close(); // Close the previous statement

$stmt = $conn->prepare("UPDATE student_reg SET reason = ? WHERE id = ?");
$stmt->bind_param("si", $reason, $student_id);

// Execute the update statement
if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(["status" => "success", "message" => "Reason updated successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "No rows updated."]);
    }
} else {
    error_log("Execute failed: " . $stmt->error);
    echo json_encode(["status" => "error", "message" => "Error updating reason: " . $stmt->error]);
}

// Close connections
$stmt->close();
$conn->close();
?>