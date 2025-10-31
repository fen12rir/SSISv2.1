
CREATE TABLE IF NOT EXISTS `locker_files` (
  `Locker_File_Id` INT(11) NOT NULL AUTO_INCREMENT,
  `Staff_Id` INT(11) NOT NULL,
  `File_Name` VARCHAR(255) NOT NULL,
  `Original_File_Name` VARCHAR(255) NOT NULL,
  `File_Path` VARCHAR(500) NOT NULL,
  `File_Type` VARCHAR(50) NOT NULL,
  `File_Size` INT(11) NOT NULL,
  `Description` TEXT NULL,
  `Uploaded_At` DATETIME NOT NULL,
  PRIMARY KEY (`Locker_File_Id`),
  INDEX `idx_staff_id` (`Staff_Id`),
  INDEX `idx_uploaded_at` (`Uploaded_At`),
  CONSTRAINT `fk_locker_files_staff` 
    FOREIGN KEY (`Staff_Id`) 
    REFERENCES `staffs` (`Staff_Id`) 
    ON DELETE CASCADE 
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

