CREATE TABLE `student_reg` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `student_fname` VARCHAR(50) NOT NULL,
  `student_mname` CHAR(1) DEFAULT NULL,
  `student_lname` VARCHAR(50) NOT NULL,
  `student_nextension` VARCHAR(5) DEFAULT NULL,
  `student_email` VARCHAR(100) NOT NULL,
  `student_password` VARCHAR(255) NOT NULL,
  `student_paddress` VARCHAR(255) NOT NULL,
  `student_gender` ENUM('male','female','other') NOT NULL,
  `student_type` VARCHAR(50) DEFAULT NULL,
  `student_univ_id` VARCHAR(50) DEFAULT NULL,
  `proof_of_enrollment` VARCHAR(255) DEFAULT NULL,  -- Store file name
  `certificate_of_enrollment` VARCHAR(255) DEFAULT NULL, -- Store file name
  `student_course` VARCHAR(100) NOT NULL,
  `student_college` VARCHAR(100) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `student_status` ENUM('pending','approved','rejected','temporary approved') DEFAULT 'pending',
  PRIMARY KEY (`id`)  -- Set id as the primary key
) ENGINE=MyISAM DEFAULT CHARSET=utf8;