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
    $first_name = $_POST['landlord_fname'];
    $middle_initial = $_POST['landlord_mname'];
    $last_name = $_POST['landlord_lname'];
    $name_extension = $_POST['landlord_nextension'];
    $email = $_POST['landlord_email'];
    $password = password_hash($_POST['landlord_password'], PASSWORD_DEFAULT);
    $permanent_address = $_POST['landlord_paddress'];
    $gender = $_POST['landlord_gender'];

    // Handle file uploads
    $barangay_permit = uploadFile('landlord_barangay_permit', $uploadDir);
    $boarding_house_photo = uploadFile('landlord_boarding_house_photo', $uploadDir);
    $landlord_identification_pic = null;

    // Check if the identification picture is uploaded
    if (isset($_FILES['landlord_identification_pic']) && $_FILES['landlord_identification_pic']['error'] == UPLOAD_ERR_OK) {
        $landlord_identification_pic = basename($_FILES['landlord_identification_pic']['name']);
        $landlord_identification_pic_path = $uploadDir . $landlord_identification_pic;
        if (!move_uploaded_file($_FILES['landlord_identification_pic']['tmp_name'], $landlord_identification_pic_path)) {
            echo "Error uploading identification picture.";
            exit;
        }
    }

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO landlord_registration (first_name, middle_initial, last_name, name_extension, email, gender, password, permanent_address, barangay_permit, boarding_house_photo, landlord_identification_pic) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssss", $first_name, $middle_initial, $last_name, $name_extension, $email, $gender, $password, $permanent_address, $barangay_permit, $boarding_house_photo, $landlord_identification_pic);

    // Execute the statement
    if ($stmt->execute()) {
        $last_id = $stmt->insert_id; // Get the last inserted ID
        $_SESSION['landlord_id'] = $last_id; // Set the landlord ID in the session

        // Fetch the landlord status and first name
        $sql = "SELECT first_name, landlord_status FROM landlord_registration WHERE id = ?";
        $stmt2 = $conn->prepare($sql);
        $stmt2->bind_param("i", $last_id);
        $stmt2->execute();
        $result = $stmt2->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $_SESSION['landlord_fname'] = $row['first_name']; 
            $_SESSION['approval_status'] = $row['landlord_status'];
        }

        // Set success message
        $_SESSION['success_message'] = "Hello $first_name, you are successfully registered!";
    } else {
        echo "Error: " . $stmt->error; // Display error message
    }

    // Close connections
    $stmt->close();
    $stmt2->close();
}

// Function to handle file uploads
function uploadFile($fileInputName, $uploadDir) {
    if (isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] == UPLOAD_ERR_OK) {
        $fileName = basename($_FILES[$fileInputName]['name']);
        $filePath = $uploadDir . $fileName;
        if (move_uploaded_file($_FILES[$fileInputName]['tmp_name'], $filePath)) {
            return $fileName; // Return the file name if upload is successful
        } else {
            echo "Error uploading $fileInputName.";
            exit;
        }
    }
    return null; // Return null if no file was uploaded
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landlord Registration Success</title>
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
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            height: 300px;
            position: relative;
            top: 50%;
            transform: translateY(-50%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
        .close {
            color: #aaa;
            position: absolute;
            top: 10px;
            right: 20px;
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
        <span style="font-family: 'Comic Sans MS', cursive;">Dear <?php echo htmlspecialchars($_SESSION['landlord_fname']); ?>,</span><br><br>
        Thank you for registering! Your account has been created successfully.<br><br>
    </div>

    <div class="upload-container">
        <div class="profile-pic-container" onclick="document.getElementById('landlordIdentificationInput').click();">
            <input type="file" id="landlordIdentificationInput" name="landlord_identification_pic" accept="image/*" required onchange="loadLandlordIdentificationPic(event)">
            <img id="landlordIdentificationPic" src ="https://via.placeholder.com/100" alt="Landlord Identification Picture" class="profile-pic">
        </div>
        <button id="saveLandlordButton" class="save-button">Save</button>
        <div id="errorMessage" style="color: red; margin-top: 10px;"></div> <!-- Error message container -->
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
    function loadLandlordIdentificationPic(event) {
        const landlordIdentificationPic = document.getElementById('landlordIdentificationPic');
        landlordIdentificationPic.src = URL.createObjectURL(event.target.files[0]);
    }

    function uploadLandlordIdentificationPicture() {
        const landlordIdentificationInput = document.getElementById('landlordIdentificationInput');
        const errorMessage = document.getElementById('errorMessage');
        errorMessage.textContent = ''; // Clear previous error messages

        if (landlordIdentificationInput.files.length === 0) {
            errorMessage.textContent = 'Please upload an identification picture.';
            return;
        }

        const formData = new FormData();
        formData.append('landlord_identification_pic', landlordIdentificationInput.files[0]);

        document.getElementById('approvalModal').style.display = 'block';

        fetch('upload_landlord_identification_pic.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            alert(data); // Show the response message
            // Fetch the landlord status after the upload
            fetch('fetch_landlord_status.php?id=' + <?php echo $_SESSION['landlord_id']; ?>)
                .then(response => response.json())
                .then(statusData => {
                    const approvalMessage = document.getElementById('approvalMessage');
                    approvalMessage.innerText = statusData.message;

                    // Clear previous action buttons
                    const actionContainer = document.getElementById('actionContainer');
                    actionContainer.innerHTML = '';

                    // Check if a redirect is needed
                    if (statusData.redirect) {
                        approvalMessage.innerText = "You have been rejected. Redirecting in 3 seconds...";
                        let countdown = 3;
                        const countdownInterval = setInterval(() => {
                            countdown--;
                            approvalMessage.innerText = `You have been rejected. Redirecting in ${countdown} seconds...`;
                            if (countdown <= 0) {
                                clearInterval(countdownInterval);
                                window.location.href = statusData.redirect; // Redirect to login_form.html
                            }
                        }, 1000);
                    } else if (statusData.action) {
                        actionContainer.innerHTML = statusData.action; // Add action button if available
                    }

                    // Show the modal
                    document.getElementById('approvalModal').style.display = 'block';
                });
        })
        .catch(error => {
            console.error('Error:', error);
            errorMessage.textContent = 'An error occurred while uploading the picture. Please try again.';
        });
    }

    document.getElementById('saveLandlordButton').addEventListener('click', uploadLandlordIdentificationPicture);
    
    function deleteAccount(landlordId) {
    fetch('delete_landlord.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ id: landlordId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.redirect) {
            window.location.href = data.redirect; // Redirect to login form
        } else if (data.error) {
            console.error(data.error); // Log error if any
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

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
        fetch('fetch_landlord_status.php?id=' + <?php echo $_SESSION['landlord_id']; ?>)
            .then(response => response.json())
            .then(statusData => {
                document.getElementById('approvalMessage').innerText = statusData.message;
                document.getElementById('actionContainer').innerHTML = statusData.action; // Update action container
            });
    }, 2000);
</script>

</body>
</html>