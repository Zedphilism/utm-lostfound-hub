-- Switch to your Lost & Found database
USE railway;

-- Create the reports table
CREATE TABLE IF NOT EXISTS `reports` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `item_name` VARCHAR(100) NOT NULL,
  `type` ENUM('lost','found') NOT NULL,
  `location` VARCHAR(100) NOT NULL,
  `description` TEXT,
  `reporter` VARCHAR(100),
  `date_reported` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` ENUM('pending','in_review','resolved') NOT NULL DEFAULT 'pending',
  `submitted_by` ENUM('admin','public') NOT NULL DEFAULT 'public',
  **`vision_labels` TEXT**
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
