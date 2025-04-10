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

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['profilePic']) && $_FILES['profilePic']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profilePic']['tmp_name'];
        $fileName = $_FILES['profilePic']['name'];
        $uploadFileDir = './uploads/'; // Directory where the file will be uploaded
        $dest_path = $uploadFileDir . basename($fileName); // Destination path

        // Move the file to the specified directory
        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            // Check if student_id is set
            if (isset($_POST['student_id'])) {
                $studentId = $_POST['student_id']; // Get the student ID from the POST request

                // Update the profile_pic field in the student_log table
                $sql = "UPDATE student_log SET profile_pic = ? WHERE student_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $fileName, $studentId); // Bind parameters (file name and student ID)

                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Profile picture uploaded and updated successfully.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error updating profile picture in the database: ' . $stmt->error]);
                }

                $stmt->close();
            } else {
                echo json_encode(['success' => false, 'message' => 'Student ID is not set.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'There was an error moving the uploaded file.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No file uploaded or there was an upload error.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

$conn->close();
?>