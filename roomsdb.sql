DROP TABLE IF EXISTS rooms;

CREATE TABLE rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    boarding_house_id INT NOT NULL,
    room_type ENUM('Room Space', 'Bed Space') DEFAULT NULL, -- Allow NULL
    room_price DECIMAL(10, 2) DEFAULT NULL, -- Allow NULL
    FOREIGN KEY (boarding_house_id) REFERENCES boarding_houses(id) ON DELETE CASCADE
);