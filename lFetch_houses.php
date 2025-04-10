<?php
session_start(); // Start the session to access session variables

// Database connection
$servername = "localhost"; // Change if your database server is different
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "bhouses_db"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}

// Initialize an array to hold the houses
$houses = [];

// Check if the user is a landlord
if (isset($_SESSION['landlord_id'])) {
    // If the landlord is logged in, fetch only their houses
    $landlord_id = $_SESSION['landlord_id'];
    
    // Fetch boarding houses for the logged-in landlord
    $sql = "SELECT id, boarding_house_name, address, main_image, price, latitude, longitude 
            FROM boarding_houses 
            WHERE landlord_id = ?"; // Filter by landlord_id

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $landlord_id); // Bind the landlord ID as an integer
} else {
    // If no landlord is logged in, fetch all boarding houses
    $sql = "SELECT id, boarding_house_name, address, main_image, price, latitude, longitude 
            FROM boarding_houses"; // No filter

    $stmt = $conn->prepare($sql);
}

// Execute the query
$stmt->execute();
$result = $stmt->get_result();

if ($result) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Extract Barangay, Municipality, and Province from the address
            $address_parts = explode(',', $row['address']);
            
            // Initialize variables
            $barangay = '';
            $municipality = '';
            $province = '';

            // Check if we have enough parts
            if (count($address_parts) >= 3) {
                $barangay = trim($address_parts[0]);
                $municipality = trim($address_parts[1]);
                $province = trim($address_parts[2]);
            }

            // Construct the full path for the main image
            $imagePath = 'uploads/' . $row['main_image']; // Assuming images are stored in the 'uploads' directory

            // Add the extracted values to the house array
            $houses[] = [
                'id' => $row['id'], // Include ID for later use
                'boarding_house_name' => $row['boarding_house_name'],
                'barangay' => $barangay,
                'municipality' => $municipality,
                'province' => $province,
                'main_image' => $imagePath, // Use the constructed image path
                'price' => $row['price'],
                'latitude' => $row['latitude'], // Include latitude
                'longitude' => $row['longitude'] // Include longitude
            ]; // Store each house in an array
        }
    } else {
        // No houses found
        echo json_encode(['message' => 'No boarding houses found.']);
    }
} else {
    // SQL query error
    echo json_encode(['error' => 'SQL query failed: ' . $conn->error]);
}

// Close the connection
$stmt->close();
$conn->close();

// Return the houses as JSON
header('Content-Type: application/json');
echo json_encode($houses);
?>