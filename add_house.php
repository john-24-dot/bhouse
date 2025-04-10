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
    die("Connection failed: " . $conn->connect_error);
}

// Check if the landlord is logged in
if (!isset($_SESSION['landlord_id'])) {
    die("You must be logged in to add a boarding house.");
}

// Get the landlord_id from the session
$landlord_id = $_SESSION['landlord_id'];

// Fetch the landlord identification picture from the landlord_log table
$landlord_stmt = $conn->prepare("SELECT profile_pic FROM landlord_log WHERE landlord_id = ?");
$landlord_stmt->bind_param("i", $landlord_id);
$landlord_stmt->execute();
$landlord_stmt->bind_result($landlord_identification_pic);
$landlord_stmt->fetch();
$landlord_stmt->close();

// Define the upload directory
$uploadDir = 'uploads/';

// Prepare the SQL statement for boarding_houses
$stmt = $conn->prepare("INSERT INTO boarding_houses (
    boarding_house_name, 
    address, 
    main_image, 
    mini_image1, 
    mini_image2, 
    mini_image3, 
    price, 
    gender_requirements, 
    distance_from_university, 
    rental_price, 
    accommodation_type, 
    kitchen_type, 
    toilet_type, 
    landlord_quarters, 
    pet_policy, 
    visitor_policy, 
    noise_policy, 
    curfew, 
    fire_extinguisher, 
    surveillance_camera, 
    fence_material, 
    building_material, 
    wifi_availability, 
    wifi_billing, 
    electricity_included, 
    electricity_billing, 
    water_included, 
    water_billing, 
    description, 
    landlord_name, 
    landlord_permanent_address, 
    landlord_contact_number, 
    room_count,
    latitude,
    longitude,
    landlord_id,
    landlord_identification_pic
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

// Example values from the form
$name = $_POST['boardingHouseName'] ?: NULL; // Allow NULL
$address = $_POST['address'] ?: NULL; // Allow NULL

// Handle file uploads
$main_image = $_FILES['mainImage']['name'] ?: NULL;
$mini_image1 = $_FILES['miniImage1']['name'] ?: NULL;
$mini_image2 = $_FILES['miniImage2']['name'] ?: NULL;
$mini_image3 = $_FILES['miniImage3']['name'] ?: NULL;

// Upload images
if ($main_image) {
    move_uploaded_file($_FILES['mainImage']['tmp_name'], $uploadDir . basename($main_image));
}
if ($mini_image1) {
    move_uploaded_file($_FILES['miniImage1']['tmp_name'], $uploadDir . basename($mini_image1));
}
if ($mini_image2) {
    move_uploaded_file($_FILES['miniImage2']['tmp_name'], $uploadDir . basename($mini_image2));
}
if ($mini_image3) {
    move_uploaded_file($_FILES['miniImage3']['tmp_name'], $uploadDir . basename($mini_image3));
}

$price = $_POST['price'] !== '' ? $_POST['price'] : '0.00'; // Set to '0.00' if empty

// Validate ENUM fields
$validGenders = ['Both', 'Male', 'Female'];
$gender_requirements = in_array($_POST['genderRequirements'], $validGenders) ? $_POST['genderRequirements'] : NULL;

$validDistances = ['Near', 'Moderate', 'Far'];
$distance_from_university = in_array($_POST['distanceFromUniversity'], $validDistances) ? $_POST['distanceFromUniversity'] : NULL;

$validRentalPrices = ['Cheap', 'Moderate', 'Expensive'];
$rental_price = in_array($_POST['rentalPrice'], $validRentalPrices) ? $_POST['rentalPrice'] : NULL;

$validAccommodationTypes = ['Room Space', 'Bed Space'];
$accommodation_type = in_array($_POST['accommodationType'], $validAccommodationTypes) ? $_POST['accommodationType'] : NULL;

$validKitchenTypes = ['Shared', 'Exclusive'];
$kitchen_type = in_array($_POST['kitchenType'], $validKitchenTypes) ? $_POST['kitchenType'] : NULL;

$validToiletTypes = ['Shared', 'Exclusive'];
$toilet_type = in_array($_POST['toiletType'], $validToiletTypes) ? $_POST['toiletType'] : NULL;

$validLandlordQuarters = ['Same Building', 'Nearby', 'Far from boarding house'];
$landlord_quarters = in_array($_POST['landlordQuarters'], $validLandlordQuarters) ? $_POST['landlordQuarters'] : NULL;

$validPetPolicies = ['Allowed', 'Not Allowed'];
$pet_policy = in_array($_POST['petPolicy'], $validPetPolicies) ? $_POST['petPolicy'] : NULL;

$validVisitorPolicies = ['Limited', 'Not Allowed'];
$visitor_policy = in_array($_POST['visitorPolicy'], $validVisitorPolicies) ? $_POST['visitorPolicy'] : NULL;

$validNoisePolicies = ['Strict', 'Moderate', 'Low'];
$noise_policy = in_array($_POST['noisePolicy'], $validNoisePolicies) ? $_POST['noisePolicy'] : NULL;

$validCurfews = ['Yes', 'No'];
$curfew = in_array($_POST['curfew'], $validCurfews) ? $_POST['curfew'] : NULL;

$validFireExtinguishers = ['Yes', 'None'];
$fire_extinguisher = in_array($_POST['fireExtinguisher'], $validFireExtinguishers) ? $_POST['fireExtinguisher'] : NULL;

$validSurveillanceCameras = ['Yes', 'None'];
$surveillance_camera = in_array($_POST['surveillanceCamera'], $validSurveillanceCameras) ? $_POST['surveillanceCamera'] : NULL;

$validFenceMaterials = ['Metal', 'Wood', 'Concrete', 'None'];
$fence_material = in_array($_POST['fenceMaterial'], $validFenceMaterials) ? $_POST['fenceMaterial'] : NULL;

$validBuildingMaterials = ['Wood', 'Concrete', 'Mixed'];
$building_material = in_array($_POST['buildingMaterial'], $validBuildingMaterials) ? $_POST['buildingMaterial'] : NULL;

$validWifiAvailabilities = ['Yes', 'No'];
$wifi_availability = in_array($_POST['wifiAvailability'], $validWifiAvailabilities) ? $_POST['wifiAvailability'] : NULL;

$validWifiBillings = ['Free', 'Included In Rent', 'Separated Bill'];
$wifi_billing = in_array($_POST['wifiBilling'], $validWifiBillings) ? $_POST['wifiBilling'] : NULL;

$validElectricityIncluded = ['Yes', 'No'];
$electricity_included = in_array($_POST['electricityIncluded'], $validElectricityIncluded) ? $_POST['electricityIncluded'] : NULL;

$validElectricityBillings = ['Metered', 'Fixed'];
$electricity_billing = in_array($_POST['electricityBilling'], $validElectricityBillings) ? $_POST['electricityBilling'] : NULL;

$validWaterIncluded = ['Yes', 'No'];
$water_included = in_array($_POST['waterIncluded'], $validWaterIncluded) ? $_POST['waterIncluded'] : NULL;

$validWaterBillings = ['Metered', 'Fixed'];
$water_billing = in_array($_POST['waterBilling'], $validWaterBillings) ? $_POST['waterBilling'] : NULL;

$description = $_POST['description'] ?: NULL; // Allow NULL
$landlord_name = $_POST['landlordName'] ?: NULL; // Allow NULL
$landlord_address = $_POST['landlordAddress'] ?: NULL; // Allow NULL
$landlord_contact = $_POST['landlordContact'] ?: NULL; // Allow NULL
$room_count = $_POST['roomCount'] ?: NULL; // Allow NULL

// Get latitude and longitude from the form (assuming you have these inputs)
$latitude = $_POST['latitude'] ?: NULL; // Allow NULL
$longitude = $_POST['longitude'] ?: NULL; // Allow NULL

// Bind parameters
$stmt->bind_param("sssssssssssssssssssssssssssssssssddis", 
    $name, 
    $address, 
    $main_image, 
    $mini_image1, 
    $mini_image2, 
    $mini_image3, 
    $price, // Now set to '0.00' if empty
    $gender_requirements, 
    $distance_from_university, 
    $rental_price, 
    $accommodation_type, 
    $kitchen_type, 
    $toilet_type, 
    $landlord_quarters, 
    $pet_policy, 
    $visitor_policy, 
    $noise_policy, 
    $curfew, 
    $fire_extinguisher, 
    $surveillance_camera, 
    $fence_material, 
    $building_material, 
    $wifi_availability, 
    $wifi_billing, 
    $electricity_included, 
    $electricity_billing, 
    $water_included, 
    $water_billing, 
    $description, 
    $landlord_name, 
    $landlord_address, 
    $landlord_contact, 
    $room_count,
    $latitude,
    $longitude,
    $landlord_id, // Include landlord_id in the binding
    $landlord_identification_pic // Include landlord_identification_pic in the binding
);

// Execute the statement
$stmt->execute();

// Check for errors
if ($stmt->error) {
    echo "Error: " . $stmt->error;
} else {
    // Get the last inserted ID for the boarding house
    $boarding_house_id = $conn->insert_id;

    // Prepare the SQL statement for rooms
    $room_stmt = $conn->prepare("INSERT INTO rooms (boarding_house_id, room_type, room_price) VALUES (?, ?, ?)");

    // Loop through the room data
    $room_count = $_POST['roomCount'];
    for ($i = 1; $i <= $room_count; $i++) {
        $room_type = $_POST["roomType{$i}"];
        $room_price = $_POST["roomPrice{$i}"] !== '' ? $_POST["roomPrice{$i}"] : '0.00'; // Set to '0.00' if empty

        // Bind parameters for room insertion
        $room_stmt->bind_param("isd", $boarding_house_id, $room_type, $room_price); // Assuming room_price is a decimal
        $room_stmt->execute();
    }

    // Close the room statement
    $room_stmt->close();

    echo "New boarding house and rooms added successfully!";
}

// Close the boarding house statement
$stmt->close();
$conn->close();
?>