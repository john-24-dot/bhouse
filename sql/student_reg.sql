CREATE TABLE student_reg (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_fname VARCHAR(50) NOT NULL,
    student_mname CHAR(1),
    student_lname VARCHAR(50) NOT NULL,
    student_nextension VARCHAR(5),
    student_email VARCHAR(100) NOT NULL UNIQUE,
    student_password VARCHAR(255) NOT NULL, -- Store hashed passwords
    student_paddress VARCHAR(255) NOT NULL,
    student_gender ENUM('male', 'female', 'other') NOT NULL,
    student_type ENUM('incoming_freshman', 'old_student') NOT NULL,
    student_univ_id VARCHAR(50), -- Nullable for incoming freshmen
    proof_of_enrollment BLOB, -- For image uploads
    certificate_of_enrollment BLOB, -- For PDF uploads
    student_course VARCHAR(100) NOT NULL,
    student_college VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);