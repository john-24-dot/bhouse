<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start the session to access session variables
session_start();

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

// Get the house ID from the request
$houseId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($houseId > 0) {
    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT boarding_house_name, address, main_image, price, description, 
                                    landlord_name, landlord_contact_number, landlord_permanent_address,
                                    mini_image1, mini_image2, mini_image3, room_count, gender_requirements,
                                    distance_from_university, rental_price, accommodation_type, kitchen_type,
                                    toilet_type, landlord_quarters, pet_policy, visitor_policy, noise_policy,
                                    curfew, fire_extinguisher, surveillance_camera, fence_material, 
                                    building_material, wifi_availability, wifi_billing, 
                                    electricity_included, electricity_billing, 
                                    water_included, water_billing, landlord_id
                             FROM boarding_houses 
                             WHERE id = ?"); // Added landlord_id to the SELECT statement
    if ($stmt) {
        $stmt->bind_param("i", $houseId); // Bind the house ID as an integer
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $house = $result->fetch_assoc();

            // Handle address safely
            $address = isset($house['address']) ? $house['address'] : ''; 
            $address_parts = !empty($address) ? explode(',', $address) : [];

            // Ensure there are enough parts in the address
            $barangay = $address_parts[0] ?? ''; 
            $municipality = $address_parts[1] ?? ''; 
            $province = $address_parts[2] ?? ''; 

            // Construct the image path safely
            $imagePath = !empty($house['main_image']) ? 'uploads/' . $house['main_image'] : 'uploads/default.jpg';

            // Prepare the response including the description and landlord details
            $response = [
                'boarding_house_name' => $house['boarding_house_name'] ?? 'N/A',
                'address' => trim($house['address']),
                'barangay' => trim($barangay),
                'municipality' => trim($municipality),
                'province' => trim($province),
                'main_image' => $imagePath,
                'price' => $house['price'] ?? 'N/A',
                'description' => $house['description'] ?? 'No description available.',
                'landlord_name' => $house['landlord_name'] ?? 'N/A',
                'landlord_contact_number' => $house['landlord_contact_number'] ?? 'N/A',
                'landlord_permanent_address' => $house['landlord_permanent_address'] ?? 'N/A',
                'mini_image1' => !empty($house['mini_image1']) ? 'uploads/' . $house['mini_image1'] : 'uploads/default.jpg',
                'mini_image2' => !empty($house['mini_image2']) ? 'uploads/' . $house['mini_image2'] : 'uploads/default.jpg',
                'mini_image3' => !empty($house['mini_image3']) ? 'uploads/' . $house['mini_image3'] : 'uploads/default.jpg',
                'room_count' => $house['room_count'] ?? 0,
                'gender_requirements' => $house['gender_requirements'] ?? 'N/A',
                'distance_from_university' => $house['distance_from_university'] ?? 'N/A',
                'rental_price' => $house['rental_price'] ?? 'N/A',
                'accommodation_type' => $house['accommodation_type'] ?? 'N/A',
                'kitchen_type' => $house['kitchen_type'] ?? 'N/A',
                'toilet_type' => $house['toilet_type'] ?? 'N/A',
                'landlord_quarters' => $house['landlord_quarters'] ?? 'N/A',
                'pet_policy' => $house['pet_policy'] ?? 'N/A',
                'visitor_policy' => $house['visitor_policy'] ?? 'N/A',
                'noise_policy' => $house['noise_policy'] ?? 'N/A',
                'curfew' => $house['curfew'] ?? 'N/A',
                'fire_extinguisher' => $house['fire_extinguisher'] ?? 'N/A',
                'surveillance_camera' => $house['surveillance_camera'] ?? 'N/A',
                'fence_material' => $house['fence_material'] ?? 'N/A',
                'building_material' => $house['building_material'] ?? 'N/A',
                'wifi_availability' => $house['wifi_availability'] ?? 'N/A',
                'wifi_billing' => $house['wifi_billing'] ?? 'N/A',
                'electricity_included' => $house['electricity_included'] ?? 'N/A',
                'electricity_billing' => $house['electricity_billing'] ?? 'N/A',
                'water_included' => $house['water_included'] ?? 'N/A',
                'water_billing' => $house['water_billing'] ?? 'N/A'
            ];

            // Fetch landlord identification picture
            $landlord_id = $house['landlord_id']; // Get the landlord_id from the house data
            $landlord_stmt = $conn->prepare("SELECT profile_pic FROM landlord_log WHERE landlord_id = ?");
            if ($landlord_stmt) {
                $landlord_stmt->bind_param("i", $landlord_id);
                $landlord_stmt->execute();
                $landlord_stmt->bind_result($landlord_identification_pic);
                $landlord_stmt->fetch();
                $landlord_stmt->close();

                // Construct the landlord identification picture path
                $response['landlord_identification_pic'] = !empty($landlord_identification_pic) ? 'uploads/' . $landlord_identification_pic : 'uploads/default.jpg';
            }

            // Fetch room details
            $room_stmt = $conn->prepare("SELECT room_type, room_price FROM rooms WHERE boarding_house_id = ?");
            if ($room_stmt) {
                $room_stmt->bind_param("i", $houseId); // Bind the house ID as an integer
                $room_stmt->execute();
                $room_result = $room_stmt->get_result();

                $rooms = [];
                while ($room = $room_result->fetch_assoc()) {
                    $rooms[] = [
                        'room_type' => $room['room_type'],
                        'room_price' => $room['room_price']
                    ];
                }
                $response['rooms'] = $rooms; // Add rooms to the response
                $room_stmt->close(); // Close the room statement
            }
        } else {
            $response = ['error' => 'No house found with the provided ID.'];
        }
        $stmt->close(); // Close the statement
    } else {
        $response = ['error' => 'Failed to prepare the SQL statement.'];
    }
} else {
    $response = ['error' => 'Invalid house ID.'];
}

header('Content-Type: application/json');
echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

$conn->close();
?>