<?php
// fetch_rejected_students.php

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

// Prepare the SQL statement to fetch rejected students
$sql = "SELECT id, student_univ_id, student_fname, student_mname, student_lname, student_nextension, student_paddress, identification_pic FROM student_reg WHERE student_status = 'rejected'"; // Added identification_pic
$result = $conn->query($sql);

// Initialize an array to hold the student data
$students = [];

// Check if there are results and fetch data
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $students[] = [
            'id' => $row['id'],
            'student_id' => $row['student_univ_id'],
            'fname' => $row['student_fname'],
            'mname' => $row['student_mname'],
            'lname' => $row['student_lname'],
            'nextension' => $row['student_nextension'],
            'paddress' => $row['student_paddress'],
            'identification_pic' => 'uploads/' . $row['identification_pic'] // Construct the full path for identification_pic
        ];
    }
}

// Return the data as JSON
header('Content-Type: application/json');
echo json_encode($students);

// Close the database connection
$conn->close();
?>