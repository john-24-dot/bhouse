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

// Get the student ID from the query parameter
$student_id = $_GET['id'];

// Prepare the SQL statement to fetch the certificate of enrollment
$sql = "SELECT certificate_of_enrollment FROM student_reg WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id); // Use "i" for integer binding
$stmt->execute();
$result = $stmt->get_result();

// Initialize response array
$response = ['status' => 'error', 'certificate_of_enrollment' => ''];

// Check if there are results and fetch data
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $certificateFileName = $row['certificate_of_enrollment']; // Get the certificate file name

    // Check if the certificate file name is null or empty
    if (empty($certificateFileName)) {
        $response['status'] = 'error';
        $response['message'] = 'Certificate of Enrollment is empty.';
    } else {
        $certificatePath = 'uploads/' . $certificateFileName; // Construct the full path

        // Check if the file exists and is not empty
        if (file_exists($certificatePath) && filesize($certificatePath) > 0) {
            $response['status'] = 'success';
            $response['certificate_of_enrollment'] = $certificatePath; // Return the path
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Certificate of Enrollment not found or is empty.';
        }
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'No student found with the provided ID.';
}

// Return the data as JSON
header('Content-Type: application/json');
echo json_encode($response);

// Close the connection
$conn->close();
?>