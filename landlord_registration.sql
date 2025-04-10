CREATE TABLE landlord_registration (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    middle_initial CHAR(1),
    last_name VARCHAR(50) NOT NULL,
    name_extension VARCHAR(5),
    email VARCHAR(100) NOT NULL UNIQUE,
    gender ENUM('male', 'female', 'other') NOT NULL,
    password VARCHAR(255) NOT NULL, -- Store hashed passwords
    permanent_address VARCHAR(255) NOT NULL,
    barangay_permit VARCHAR(255) NOT NULL, -- For storing the file name of the uploaded barangay permit
    boarding_house_photo VARCHAR(255) NOT NULL, -- For storing the file name of the uploaded boarding house photo
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);