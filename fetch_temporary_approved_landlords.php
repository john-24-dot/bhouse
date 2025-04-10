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

// Query to fetch temporary approved landlords, including landlord_identification_pic
$sql = "SELECT id, first_name, middle_initial, last_name, name_extension, permanent_address, gender, barangay_permit, boarding_house_photo, landlord_identification_pic 
        FROM landlord_registration 
        WHERE landlord_status = 'temporary approved'"; // Adjusted condition to use landlord_status
$result = $conn->query($sql);

$landlords = [];
if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        // Prepend the uploads directory to the barangay_permit, boarding_house_photo, and landlord_identification_pic
        $row['barangay_permit'] = 'uploads/' . $row['barangay_permit'];
        $row['boarding_house_photo'] = 'uploads/' . $row['boarding_house_photo'];
        $row['landlord_identification_pic'] = 'uploads/' . $row['landlord_identification_pic']; // Add this line
        
        $landlords[] = $row; // Add the row to the landlords array
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($landlords);

$conn->close();
?>