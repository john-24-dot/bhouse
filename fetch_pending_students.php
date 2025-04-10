<?php
session_start(); // Start the session

// Database configuration
$servername = "localhost"; // Change if your database server is different
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "bhouses_db"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare the SQL statement to fetch pending students
$sql = "SELECT id, student_univ_id, student_fname, student_mname, student_lname, student_nextension, student_paddress, student_course, student_college, student_type, student_gender, proof_of_enrollment, certificate_of_enrollment, identification_pic FROM student_reg WHERE student_status = 'pending'";
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
            'course' => $row['student_course'],
            'college' => $row['student_college'],
            'type' => $row['student_type'],
            'gender' => $row['student_gender'],
            'proof_of_enrollment' => 'uploads/' . $row['proof_of_enrollment'], // Construct the full path
            'certificate_of_enrollment' => 'uploads/' . $row['certificate_of_enrollment'], // Construct the full path
            'identification_pic' => 'uploads/' . $row['identification_pic'] // Add identification_pic to the array
        ];
    }
}

// Return the data as JSON
header('Content-Type: application/json');
echo json_encode($students);

// Close the connection
$conn->close();
?>