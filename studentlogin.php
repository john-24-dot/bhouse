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

// Handle login request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']); // Trim to remove any leading/trailing spaces
    $password = trim($_POST['password']); // Trim to remove any leading/trailing spaces

    // Prepare and bind
    $stmt = $conn->prepare("SELECT * FROM student_reg WHERE student_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Invalid email
        echo "<script>alert('Invalid email.');</script>";
        echo "<script>window.location.href='../html/mainBH.html'</script>"; // Redirect back to login page
        exit();
    } else {
        $student = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $student['student_password'])) {
            // Successful login
            $_SESSION['student_id'] = $student['id']; // Store student_id in session
            $_SESSION['email'] = $student['student_email']; // Store email in session
            $_SESSION['student_fname'] = $student['student_fname']; // Store first name in session

            // Insert log into student_log table
            $log_stmt = $conn->prepare("INSERT INTO student_log (student_id, email_address, first_name, middle_name, last_name, permanent_address, college, course, student_status, profile_pic) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $log_stmt->bind_param("isssssssss", 
                $student['id'], 
                $email, 
                $student['student_fname'], 
                $student['student_mname'], 
                $student['student_lname'], 
                $student['student_paddress'], 
                $student['student_college'], 
                $student['student_course'], 
                $student['student_status'],
                $student['identification_pic'] // Use identification_pic from student_reg
            );
            $log_stmt->execute();
            $log_stmt->close();

            // Display welcome message
            echo "<div style='position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); padding: 40px; background-color: #ECF0F6; color: #142F48; height: 200px; width: 300px; display: flex; flex-direction: column; justify-content: center; align-items: center; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);'>";
            echo "<h1 style='font-size: 20px;'>Welcome " . htmlspecialchars($student['student_fname']) . "!</h1>";
            echo "<p style='text-align: center;'>We are glad to have you here. Enjoy your time!</p>";
            echo "</div>";

            // Redirect to the dashboard or welcome page after a short delay
            echo "<script>
                setTimeout(function() {
                    window.location.href = 'stDashboard.html'; // Redirect to the dashboard
                }, 3000); // Redirect after 3 seconds
            </script>";
        } else {
            // Invalid password
            echo "<script>alert('Invalid password.');</script>";
            echo "<script>window.location.href='login_form.html'</script>"; // Redirect back to login page
            exit();
        }
    }

    $stmt->close();
}

$conn->close();
?>