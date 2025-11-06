
CREATE TABLE IF NOT EXISTS `announcements` (
  `Announcement_Id` INT(11) NOT NULL AUTO_INCREMENT,
  `Title` VARCHAR(255) NOT NULL,
  `Text` TEXT NOT NULL,
  `Image_Path` VARCHAR(500) NULL,
  `Date_Publication` DATE NOT NULL,
  `Created_At` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Updated_At` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`Announcement_Id`),
  INDEX `idx_date_publication` (`Date_Publication`),
  INDEX `idx_created_at` (`Created_At`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

