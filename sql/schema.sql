-- Switch to your Lost & Found database
USE utm_lostfound_db;

-- Create the reports table
CREATE TABLE IF NOT EXISTS `reports` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `item_name` VARCHAR(100) NOT NULL,
  `type` ENUM('lost','found') NOT NULL,
  `location` VARCHAR(100) NOT NULL,
  `description` TEXT,
  `reporter` VARCHAR(100),
  `date_reported` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` ENUM('pending','in_review','resolved') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
