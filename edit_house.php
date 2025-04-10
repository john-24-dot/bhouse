<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = "localhost"; // Change if needed
$username = "root"; 
$password = ""; 
$dbname = "bhouses_db"; 

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]));
}

// Get the house ID from the form data
$houseId = isset($_POST['houseId']) ? intval($_POST['houseId']) : 0;

// Fetch existing house details to retain images if not updated
$existingHouse = $conn->prepare("SELECT main_image, mini_image1, mini_image2, mini_image3 FROM boarding_houses WHERE id = ?");
$existingHouse->bind_param("i", $houseId);
$existingHouse->execute();
$existingHouseResult = $existingHouse->get_result();
$existingImages = $existingHouseResult->fetch_assoc();
$existingHouse->close();

// Initialize image variables
$mainImage = $existingImages['main_image'];
$miniImage1 = $existingImages['mini_image1'];
$miniImage2 = $existingImages['mini_image2'];
$miniImage3 = $existingImages['mini_image3'];

// Handle file uploads
if (isset($_FILES['editMainImage']) && $_FILES['editMainImage']['error'] == UPLOAD_ERR_OK) {
    $mainImage = $_FILES['editMainImage']['name'];
    move_uploaded_file($_FILES['editMainImage']['tmp_name'], 'uploads/' . $mainImage);
}

if (isset($_FILES['editMiniImage1']) && $_FILES['editMiniImage1']['error'] == UPLOAD_ERR_OK) {
    $miniImage1 = $_FILES['editMiniImage1']['name'];
    move_uploaded_file($_FILES['editMiniImage1']['tmp_name'], 'uploads/' . $miniImage1);
}

if (isset($_FILES['editMiniImage2']) && $_FILES['editMiniImage2']['error'] == UPLOAD_ERR_OK) {
    $miniImage2 = $_FILES['editMiniImage2']['name'];
    move_uploaded_file($_FILES['editMiniImage2']['tmp_name'], 'uploads/' . $miniImage2);
}

if (isset($_FILES['editMiniImage3']) && $_FILES['editMiniImage3']['error'] == UPLOAD_ERR_OK) {
    $miniImage3 = $_FILES['editMiniImage3']['name'];
    move_uploaded_file($_FILES['editMiniImage3']['tmp_name'], 'uploads/' . $miniImage3);
}

// Get room count and set default to 0 if not provided
$roomCount = isset($_POST['roomCount']) && is_numeric($_POST['roomCount']) ? intval($_POST['roomCount']) : 0;

// Get latitude and longitude, defaulting to 0 if not provided
$latitude = isset($_POST['latitude']) && is_numeric($_POST['latitude']) ? floatval($_POST['latitude']) : 0.0;
$longitude = isset($_POST['longitude']) && is_numeric($_POST['longitude']) ? floatval($_POST['longitude']) : 0.0;

if ($houseId > 0) {
    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("UPDATE boarding_houses SET 
        boarding_house_name = ?, 
        address = ?, 
        main_image = ?, 
        price = ?, 
        description = ?, 
        landlord_name = ?, 
        landlord_contact_number = ?, 
        landlord_permanent_address = ?, 
        mini_image1 = ?, 
        mini_image2 = ?, 
        mini_image3 = ?, 
        room_count = ?, 
        gender_requirements = ?, 
        distance_from_university = ?, 
        rental_price = ?, 
        accommodation_type = ?, 
        kitchen_type = ?, 
        toilet_type = ?, 
        landlord_quarters = ?, 
        pet_policy = ?, 
        visitor_policy = ?, 
        noise_policy = ?, 
        curfew = ?, 
        fire_extinguisher = ?, 
        surveillance_camera = ?, 
        fence_material = ?, 
        building_material = ?, 
        wifi_availability = ?, 
        wifi_billing = ?, 
        electricity_included = ?, 
        electricity_billing = ?, 
        water_included = ?, 
        water_billing = ?, 
        latitude = ?, 
        longitude = ? 
        WHERE id = ?");

    if ($stmt) {
        // Bind parameters
        $stmt->bind_param("ssssssssssssssssssssssssssssssssssdd", 
            $_POST['boardingHouseName'],
            $_POST['address'],
            $mainImage, // Use the uploaded main image or existing one
            $_POST['price'],
            $_POST['description'],
            $_POST['landlordName'],
            $_POST['landlordContact'],
            $_POST['landlordAddress'],
            $miniImage1, // Use the uploaded mini image 1 or existing one
            $miniImage2, // Use the uploaded mini image 2 or existing one
            $miniImage3, // Use the uploaded mini image 3 or existing one
            $roomCount, // Use the room count, defaulting to 0 if not provided
            $_POST['genderRequirements'],
            $_POST['distanceFromUniversity'],
            $_POST['rentalPrice'],
            $_POST['accommodationType'],
            $_POST['kitchenType'],
            $_POST['toiletType'],
            $_POST['landlordQuarters'],
            $_POST['petPolicy'],
            $_POST['visitorPolicy'],
            $_POST['noisePolicy'],
            $_POST['curfew'],
            $_POST['fireExtinguisher'],
            $_POST['surveillanceCamera'],
            $_POST['fenceMaterial'],
            $_POST['buildingMaterial'],
            $_POST['wifiAvailability'],
            $_POST['wifiBilling'], // New field for Wi-Fi billing
            $_POST['electricityIncluded'],
            $_POST['electricityBilling'], // New field for electricity billing
            $_POST['waterIncluded'],
            $_POST['waterBilling'], // New field for water billing
            $latitude, // Latitude
            $longitude, // Longitude
            $houseId // The ID of the house to update
        );

        // Execute the statement
        if ($stmt->execute()) {
            // Now handle the room updates
            // First, delete existing rooms for this boarding house
            $delete_stmt = $conn->prepare("DELETE FROM rooms WHERE boarding_house_id = ?");
            $delete_stmt->bind_param("i", $houseId);
            $delete_stmt->execute();
            $delete_stmt->close();

            // Insert new room details
            for ($i = 1; $i <= $roomCount; $i++) {
                $roomType = isset($_POST["roomType$i"]) ? $_POST["roomType$i"] : '';
                $roomPrice = isset($_POST["roomPrice$i"]) ? $_POST["roomPrice$i"] : 0;

                // Insert each room into the rooms table
                $insert_stmt = $conn->prepare("INSERT INTO rooms (boarding_house_id, room_type, room_price) VALUES (?, ?, ?)");
                $insert_stmt->bind_param("isi", $houseId, $roomType, $roomPrice);
                $insert_stmt->execute();
                $insert_stmt->close();
            }

            echo json_encode(['success' => 'House updated successfully.']);
        } else {
            echo json_encode(['error' => 'Failed to update house: ' . $stmt->error]);
        }
        $stmt->close(); // Close the statement
    } else {
        echo json_encode(['error' => 'Failed to prepare the SQL statement.']);
    }
} else {
    echo json_encode(['error' => 'Invalid house ID.']);
}

$conn->close();
?>