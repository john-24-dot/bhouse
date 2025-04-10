<?php
// Database connection parameters
$servername = "localhost"; // Change if your server is different
$username = "root"; // Your MySQL username
$password = ""; // Your MySQL password
$dbname = "bhouses_db"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get filter values from POST request
$data = json_decode(file_get_contents("php://input"), true); // Decode JSON input
$genderRequirements = $data['genderRequirements'];
$distanceFromUniversity = $data['distanceFromUniversity'];
$rentalPrice = $data['rentalPrice'];
$accommodationType = $data['accommodationType'];
$kitchenType = $data['kitchenType'];
$toiletType = $data['toiletType'];
$landlordQuarters = $data['landlordQuarters'];
$petPolicy = $data['petPolicy'];
$visitorPolicy = $data['visitorPolicy'];
$noisePolicy = $data['noisePolicy'];
$curfewPolicy = $data['curfewPolicy'];
$fireExtinguisher = $data['fireExtinguisher'];
$surveillanceCamera = $data['surveillanceCamera'];
$fenceMaterial = $data['fenceMaterial'];
$buildingMaterial = $data['buildingMaterial'];
$wifiAvailability = $data['wifiAvailability'];
$electricityIncluded = $data['electricityIncluded'];
$waterIncluded = $data['waterIncluded'];

// Build the SQL query with filters
$sql = "SELECT * FROM boarding_houses WHERE 1=1"; // Start with a base query

if ($genderRequirements != 'Default') {
    $sql .= " AND gender_requirements = '$genderRequirements'";
}
if ($distanceFromUniversity != 'Default') {
    $sql .= " AND distance_from_university = '$distanceFromUniversity'";
}
if ($rentalPrice != 'Default') {
    $sql .= " AND rental_price = '$rentalPrice'";
}
if ($accommodationType != 'Default') {
    $sql .= " AND accommodation_type = '$accommodationType'";
}
if ($kitchenType != 'Default') {
    $sql .= " AND kitchen_type = '$kitchenType'";
}
if ($toiletType != 'Default') {
    $sql .= " AND toilet_type = '$toiletType'";
}
if ($landlordQuarters != 'Default') {
    $sql .= " AND landlord_quarters = '$landlordQuarters'";
}
if ($petPolicy != 'Default') {
    $sql .= " AND pet_policy = '$petPolicy'";
}
if ($visitorPolicy != 'Default') {
    $sql .= " AND visitor_policy = '$visitorPolicy'";
}
if ($noisePolicy != 'Default') {
    $sql .= " AND noise_policy = '$noisePolicy'";
}
if ($curfewPolicy != 'Default') {
    $sql .= " AND curfew_policy = '$curfewPolicy'";
}
if ($fireExtinguisher != 'Default') {
    $sql .= " AND fire_extinguisher = '$fireExtinguisher'";
}
if ($surveillanceCamera != 'Default') {
    $sql .= " AND surveillance_camera = '$surveillanceCamera'";
}
if ($fenceMaterial != 'Default') {
    $sql .= " AND fence_material = '$fenceMaterial'";
}
if ($buildingMaterial != 'Default') {
    $sql .= " AND building_material = '$buildingMaterial'";
}
if ($wifiAvailability != 'Default') {
    $sql .= " AND wifi_availability = '$wifiAvailability'";
}
if ($electricityIncluded != 'Default') {
    $sql .= " AND electricity_included = '$electricityIncluded'";
}
if ($waterIncluded != 'Default') {
    $sql .= " AND water_included = '$waterIncluded'";
}

// Execute the query
$result = $conn->query($sql);

$houses = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $houses[] = $row;
    }
}

// Return JSON response
echo json_encode($houses);

// Close connection
$conn->close();
?>