<?php
$servername = "localhost";
$username = "root";
$password = ""; // No password
$dbname = "bhouses_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch approved landlords, including landlord_identification_pic
$sql = "SELECT id, first_name, middle_initial, last_name, name_extension, permanent_address, gender, landlord_identification_pic 
        FROM landlord_registration 
        WHERE landlord_status = 'approved'"; // Adjusted condition to use landlord_status
$result = $conn->query($sql);

$landlords = [];
if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        // Prepend the uploads directory to the landlord_identification_pic
        $row['landlord_identification_pic'] = 'uploads/' . $row['landlord_identification_pic']; // Adjust the path as necessary
        
        $landlords[] = $row; // Add the row to the landlords array
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($landlords);

$conn->close();
?>