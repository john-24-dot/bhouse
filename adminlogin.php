<?php
// Database connection
$servername = "localhost"; // Change this to your server name
$username = "root"; // Change this to your database username
$password = ""; // Change this to your database password
$dbname = "bhouses_db"; // Change this to your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize message variables
$message = '';
$messageType = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username']; // Get the username from the form
    $password = $_POST['password']; // Get the password from the form

    // Prepare and bind
    $stmt = $conn->prepare("SELECT password, role FROM admin_users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    // Check if username exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($storedPassword, $role);
        $stmt->fetch();

        // Verify the password
        if ($password === $storedPassword) {
            // Successful login
            session_start();
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;
            $message = "Login successful! Welcome, " . $role; // Success message
            $messageType = 'success';

            // Show loading animation for 4 seconds before redirecting
            echo "<script>
                setTimeout(function() {
                    document.getElementById('loadingAnimation').style.display = 'flex';
                    setTimeout(function() {
                        window.location.href = 'adDashboard.html';
                    }, 4000); // Redirect after 4 seconds
                }, 3000); // Show loading after 3 seconds
            </script>";
        } else {
            $message = "Invalid password.";
            $messageType = 'error';
            header("refresh:3;url=Adminlogin.html"); // Redirect after 3 seconds
            echo "<script>setTimeout(function() { window.location.href = 'Adminlogin.html'; }, 3000);</script>";
        }
    } else {
        $message = "Username not found.";
        $messageType = 'error';
        header("refresh:3;url=Adminlogin.html"); // Redirect after 3 seconds
        echo "<script>setTimeout(function() { window.location.href = 'Adminlogin.html'; }, 3000);</script>";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"> <!-- Font Awesome -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet"> <!-- Google Material Icons -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #ECF0F6; /* Lightest shade */
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #FFFFFF; /* White background for the form */
            padding: 30px; /* Increased padding */
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 400px; /* Increased width */
        }
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            display: flex;
            align-items: center;
            font-weight: bold;
            animation: popIn 0.5s ease; /* Pop-in animation */
        }
        .success {
            background-color: #D4EDDA; /* Light green for success */
            color: #155724; /* Dark green text */
        }
        .error {
            background-color: #F8D7DA; /* Light red for error */
            color: #721C24; /* Dark red text */
        }
        @keyframes popIn {
            0% {
                transform: scale(0.5);
                opacity: 0;
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }
        .loading {
            display: none;
            margin-top: 20px;
            text-align: center;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(255, 255, 255, 0.8); /* Semi-transparent background */
            z-index: 1000; /* Ensure it is on top */
            transition: opacity 0.5s ease; /* Smooth fade-in effect */
        }
        .loader {
            border: 8px solid #f3f3f3; /* Light grey */
            border-top: 8px solid #3498db; /* Blue */
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>

    <div class="container">
        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <i class="material-icons"><?php echo $messageType === 'success' ? 'check_circle' : 'error'; ?></i>
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="loading" id="loadingAnimation">
            <div class="loader"></div>
            <p>Loading...</p>
        </div>
    </div>

    <script>
        const loadingAnimation = document.getElementById('loadingAnimation');

        // Show loading animation if it is set to display
        if (loadingAnimation.style.display === 'flex') {
            loadingAnimation.style.opacity = '1'; // Fade in effect
        }
    </script>
</body>
</html>