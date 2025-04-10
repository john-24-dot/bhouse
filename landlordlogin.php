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
    $stmt = $conn->prepare("SELECT id, first_name, middle_initial, last_name, name_extension, permanent_address, password, landlord_status, landlord_identification_pic FROM landlord_registration WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Invalid email
        echo "<script>alert('Invalid email.');</script>";
        echo "<script>window.location.href='../html/mainBH.html'</script>"; // Redirect back to login page
        exit();
    } else {
        $landlord = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $landlord['password'])) {
            // Successful login
            $_SESSION['landlord_id'] = $landlord['id']; // Store landlord_id in session
            $_SESSION['email'] = $email; // Store email in session
            $_SESSION['full_name'] = trim($landlord['first_name'] . ' ' . $landlord['middle_initial'] . ' ' . $landlord['last_name'] . ' ' . $landlord['name_extension']); // Store full name in session
            $_SESSION['permanent_address'] = $landlord['permanent_address']; // Store permanent address in session
            $landlord_status = $landlord['landlord_status']; // Fetch landlord status
            $landlord_identification_pic = $landlord['landlord_identification_pic']; // Fetch landlord identification picture

            // Insert log into landlord_log table
            $landlord_id = $landlord['id']; // Assuming 'id' is the primary key in landlord_registration
            $full_name = $_SESSION['full_name'];
            $permanent_address = $landlord['permanent_address'];
            $name_extension = $landlord['name_extension'];

            // Change landlord_identification_pic to profile_pic in the insert statement
            $log_stmt = $conn->prepare("INSERT INTO landlord_log (landlord_id, full_name, permanent_address, name_extension, landlord_status, profile_pic) VALUES (?, ?, ?, ?, ?, ?)");
            $log_stmt->bind_param("isssss", $landlord_id, $full_name, $permanent_address, $name_extension, $landlord_status, $landlord_identification_pic);
            $log_stmt->execute();
            $log_stmt->close();

            // Display welcome message
            echo "<div id='welcomeModal' style='position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); padding: 40px; background-color: #ECF0F6; color: #142F48; height: 200px; width: 300px; display: flex; flex-direction: column; justify-content: center; align-items: center; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);'>
                    <h1 style='font-size: 20px;'>Welcome, Landlord " . htmlspecialchars($landlord['first_name']) . "!</h1>
                    <p style='text-align: center;'>Thank you for being a valued landlord. Your identification picture has been recorded.</p>
                </div>";

            // Redirect to the landlord dashboard after a short delay
            echo "<script>
                    setTimeout(function() {
                        window.location.href = 'lDashboard.html';
                    }, 3000); // Redirect after 3 seconds
                  </script>";

            exit();
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