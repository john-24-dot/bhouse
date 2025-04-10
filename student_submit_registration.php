<?php
session_start(); // Start the session

// Database configuration
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "bhouses_db"; 

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Define the upload directory
    $uploadDir = 'uploads/';

    // Ensure the upload directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Get form data
    $student_fname = $_POST['student_fname'];
    $student_mname = $_POST['student_mname'];
    $student_lname = $_POST['student_lname'];
    $student_nextension = $_POST['student_nextension'];
    $student_email = $_POST['student_email'];
    $student_password = password_hash($_POST['student_password'], PASSWORD_DEFAULT);
    $student_paddress = $_POST['student_paddress'];
    $student_gender = $_POST['student_gender'];
    $student_type = $_POST['student_type'];
    $student_univ_id = isset($_POST['student_univ_id']) ? $_POST['student_univ_id'] : null;
    $student_course = $_POST['student_course'];
    $student_college = $_POST['student_college'];

    // Handle file uploads
    $proof_of_enrollment = null;
    $certificate_of_enrollment = null;

    // Upload proof of enrollment
    if (isset($_FILES['proof_of_enrollment']) && $_FILES['proof_of_enrollment']['error'] == UPLOAD_ERR_OK) {
        $proof_of_enrollment = basename($_FILES['proof_of_enrollment']['name']);
        $proof_of_enrollment_path = $uploadDir . $proof_of_enrollment;
        if (!move_uploaded_file($_FILES['proof_of_enrollment']['tmp_name'], $proof_of_enrollment_path)) {
            echo "Error uploading proof of enrollment.";
            exit;
        }
    }

    // Upload certificate of enrollment
    if (isset($_FILES['certificate_of_enrollment']) && $_FILES['certificate_of_enrollment']['error'] == UPLOAD_ERR_OK) {
        $certificate_of_enrollment = basename($_FILES['certificate_of_enrollment']['name']);
        $certificate_of_enrollment_path = $uploadDir . $certificate_of_enrollment;
        if (!move_uploaded_file($_FILES['certificate_of_enrollment']['tmp_name'], $certificate_of_enrollment_path)) {
            echo "Error uploading certificate of enrollment.";
            exit;
        }
    }

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO student_reg (student_fname, student_mname, student_lname, student_nextension, student_email, student_password, student_paddress, student_gender, student_type, student_univ_id, proof_of_enrollment, certificate_of_enrollment, student_course, student_college) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssssssss", $student_fname, $student_mname, $student_lname, $student_nextension, $student_email, $student_password, $student_paddress, $student_gender, $student_type, $student_univ_id, $proof_of_enrollment, $certificate_of_enrollment, $student_course, $student_college);

    // Execute the statement
    if ($stmt->execute()) {
        $last_id = $stmt->insert_id; // Get the last inserted ID
        $_SESSION['student_id'] = $last_id; // Set the student ID in the session

        // Fetch the student status and first name
        $sql = "SELECT student_fname, student_status FROM student_reg WHERE id = ?";
        $stmt2 = $conn->prepare($sql);
        $stmt2->bind_param("i", $last_id);
        $stmt2->execute();
        $result = $stmt2->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $_SESSION['student_fname'] = $row['student_fname']; 
            $_SESSION['approval_status'] = $row['student_status'];
        }

        // Set success message
        $_SESSION['success_message'] = "Hello $student_fname, you are successfully registered!";
    } else {
        echo "Error: " . $stmt->error; // Display error message
    }

    // Close connections
    $stmt->close();
    $stmt2->close();
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Success</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fa;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .container {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            text-align: center;
            max-width: 600px;
            width: 100%;
            min-height: 400px;
        }
        .message {
            font-size: 1.8em;
            color: #47647F;
            margin-bottom: 20px;
            font-family: 'Comic Sans MS', cursive;
        }
        .greeting {
            font-size: 1.2em;
            margin: 20px 0;
            font-family: 'Verdana', sans-serif;
        }
        .footer {
            margin-top: 30px;
            font-size: 0.9em;
            color: #777;
        }
        .upload-container {
            margin-top: 20px;
            text-align: center;
        }
        .profile-pic-container {
            width: 100px;
            height: 100px;
            border: 1px solid #8199B1;
            border-radius: 5px;
            overflow: hidden;
            cursor: pointer;
            display: inline-block;
        }
        .profile-pic {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .upload-container input[type="file"] {
            display: none;
        }
        .upload-container button {
            background-color: #47647F;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        .upload-container button:hover {
            background-color: #3b4d5a;
        }
        .instructions {
            font-size: 0.9em;
            color: #555;
            margin: 10px 0;
        }
        .modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.4);
}
        
        .modal-content {
    background-color: #fefefe;
    margin: auto; /* Center horizontally */
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 600px; /* Optional: Set a max width */
    height: 300px; /* Set a fixed height */
    position: relative; /* Position relative for centering */
    top: 50%; /* Move down 50% */
    transform: translateY(-50%); /* Adjust to center vertically */
    display: flex; /* Use flexbox for centering */
    flex-direction: column; /* Stack children vertically */
    justify-content: center; /* Center children vertically */
    align-items: center; /* Center children horizontally */
    text-align: center; /* Center text */
}

.close {
    color: #aaa;
    position: absolute; /* Position absolute to place it in the corner */
    top: 10px; /* Distance from the top */
    right: 20px; /* Distance from the right */
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
}
    </style>
</head>
<body>

<div class="container">
    <div class="message">
        <?php echo isset($_SESSION['success_message']) ? $_SESSION['success_message'] : "No message available."; ?>
    </div>

    <div class="greeting">
        <span style="font-family: 'Comic Sans MS', cursive;">Dear <?php echo htmlspecialchars($_SESSION['student_fname']); ?>,</span><br><br>
        Thank you for registering! Your account has been created successfully.<br><br>
    </div>

    <div class="upload-container">
        <div class="instructions">
            Please upload a profile picture with a white background in formal attire.
        </div>
        <div class="profile-pic-container" onclick="document.getElementById ('profilePicInput').click();">
            <input type="file" id="profilePicInput" name="profile_picture" accept="image/*" required onchange="loadProfilePic(event)">
            <img id="profilePic" src="https://via.placeholder.com/100" alt="Profile Picture" class="profile-pic">
        </div>
        <button id="saveButton" class="save-button">Save</button>
    </div>

    <div class="footer">
        &copy; <?php echo date("Y"); ?> Your Institution Name. All rights reserved.
    </div>
</div>

<!-- Modal for Approval Status -->
<div id="approvalModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Approval Status</h2>
        <p id="approvalMessage">Your request is currently under review by the Office of Student Affairs and Services (OSAS). Please wait for approval.</p>
        <div id="actionContainer"></div> <!-- Container for action buttons -->
    </div>
</div>

<script>
    function loadProfilePic(event) {
        const profilePic = document.getElementById('profilePic');
        profilePic.src = URL.createObjectURL(event.target.files[0]);
    }

    function uploadProfilePicture() {
        const profilePicInput = document.getElementById('profilePicInput');
        if (profilePicInput.files.length === 0) {
            alert('Please upload a profile picture.');
            return;
        }

        const formData = new FormData();
        formData.append('profile_pic', profilePicInput.files[0]);

        document.getElementById('approvalModal').style.display = 'block';

        fetch('upload_identification_pic.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            alert(data);
            // Fetch the student status after the upload
            fetch('fetch_student_status.php?id=' + <?php echo $_SESSION['student_id']; ?>)
                .then(response => response.json())
                .then(statusData => {
                    const approvalMessage = document.getElementById('approvalMessage');
                    approvalMessage.innerText = statusData.message;

                    // Clear previous action buttons
                    const actionContainer = document.getElementById('actionContainer');
                    actionContainer.innerHTML = '';

                    // Check if a redirect is needed
                    if (statusData.redirect) {
                        // Display the rejection message and start countdown
                        approvalMessage.innerText = "You have been rejected. Redirecting in 3 seconds...";
                        
                        // Countdown for 3 seconds
                        let countdown = 3;
                        const countdownInterval = setInterval(() => {
                            countdown--;
                            approvalMessage.innerText = `You have been rejected. Redirecting in ${countdown} seconds...`;
                            if (countdown <= 0) {
                                clearInterval(countdownInterval);
                                window.location.href = statusData.redirect; // Redirect to login_form.html
                            }
                        }, 1000); // Update every second
                    } else if (statusData.action) {
                        actionContainer.innerHTML = statusData.action; // Add action button if available
                    }

                    // Show the modal
                    document.getElementById('approvalModal').style.display = 'block';
                });
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    function deleteAccount(studentId) {
    // Directly call the delete endpoint without confirmation
    fetch('delete_student.php?id=' + studentId, {
        method: 'DELETE'
    })
    .then(response => response.json())
    .then(data => {
        // Redirect to login_form.html after deletion
        if (data.redirect) {
            window.location.href = data.redirect;
        } else {
            console.error(data.error); // Log error if any
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

    document.getElementById('saveButton').addEventListener('click', uploadProfilePicture);

    function closeModal() {
        document.getElementById('approvalModal').style.display = 'none';
    }

    window.onclick = function(event) {
        const modal = document.getElementById('approvalModal');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
    
    // Live refresh for the approval modal every 2 seconds
    setInterval(() => {
        fetch('fetch_student_status.php?id=' + <?php echo $_SESSION['student_id']; ?>)
            .then(response => response.json())
            .then(statusData => {
                document.getElementById('approvalMessage').innerText = statusData.message;
                document.getElementById('actionContainer').innerHTML = statusData.action; // Update action container
            });
    }, 2000);

    document.getElementById('saveButton').addEventListener('click', uploadProfilePicture);

    function closeModal() {
        document.getElementById('approvalModal').style.display = 'none';
    }

    window.onclick = function(event) {
        const modal = document.getElementById('approvalModal');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
    
    // Live refresh for the approval modal every 2 seconds
    setInterval(() => {
        fetch('fetch_student_status.php?id=' + <?php echo $_SESSION['student_id']; ?>)
            .then(response => response.json())
            .then(statusData => {
                document.getElementById('approvalMessage').innerText = statusData.message;
                document.getElementById('actionContainer').innerHTML = statusData.action; // Update action container
            });
    }, 2000);
</script>

</body>
</html>