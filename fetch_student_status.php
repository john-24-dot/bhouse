<?php
session_start();
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

// Check if the student ID is set
if (isset($_GET['id'])) {
    $student_id = $_GET['id'];

    // Prepare and bind
    $sql = "SELECT student_status FROM student_reg WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    
    // Execute the statement
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $status = $row['student_status'];

            // Prepare the response message based on the status
            switch ($status) {
                case 'approved':
                    $message = "Your account has been approved. You can now log in.";
                    $action = '<button onclick="window.location.href=\'login_form.html\'">Proceed to Login</button>';
                    break;
                case 'temporary approved':
                    $message = "Your account is temporarily approved and is still under review by the Office of Student Affairs and Services (OSAS). Please wait within 1 week.";
                    $action = ''; // No action needed
                    break;
                case 'rejected':
                    $message = "Your account has been rejected. Please contact support.
                    
                    If you have any questions about why you were rejected, please visit the OSAS on weekdays. Thank you.";
                    // Provide an exit button for account deletion
                    $action = '<button onclick="deleteAccount(' . $student_id . ')">Exit</button>';
                    break;
                case 'pending':
                default:
                    $message = "Your request is currently under review by the Office of Student Affairs and Services (OSAS). Please wait for approval.";
                    $action = ''; // No action needed
                    break;
            }

            // Return the response as JSON
            echo json_encode(['message' => $message, 'action' => $action]);
        } else {
            // If no student found, redirect to login form
            echo json_encode(['redirect' => 'login_form.html']);
        }
    } else {
        echo json_encode(['message' => 'Error executing query.']);
    }

    // Close the statement
    $stmt->close();
} else {
    echo json_encode(['message' => 'No student ID provided.']);
}

// Close the database connection
$conn->close();
?>