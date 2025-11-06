<?php
declare(strict_types=1);
require_once __DIR__ . '/../../core/dbconnection.php';
require_once __DIR__ . '/../../Exceptions/DatabaseException.php';
date_default_timezone_set('Asia/Manila');

class adminAnnouncementsModel {
    protected $conn;

    public function __construct() {
        $db = new Connect();
        $this->conn = $db->getConnection();
    }

    public function getAllAnnouncements() : array {
        try {
            $sql = "SELECT * FROM announcements 
                    ORDER BY Date_Publication DESC, Created_At DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to fetch announcements', 0, $e);
        }
    }

    public function getAnnouncementById(int $announcementId) : ?array {
        try {
            $sql = "SELECT * FROM announcements 
                    WHERE Announcement_Id = :announcementId";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':announcementId', $announcementId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to fetch announcement', 0, $e);
        }
    }

    public function insertAnnouncement(string $title, string $text, ?string $imagePath, string $datePublication) : int {
        try {
            $sql = "INSERT INTO announcements 
                    (Title, Text, Image_Path, Date_Publication, Created_At) 
                    VALUES (:title, :text, :imagePath, :datePublication, NOW())";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':text', $text);
            $stmt->bindParam(':imagePath', $imagePath);
            $stmt->bindParam(':datePublication', $datePublication);
            $stmt->execute();
            return (int)$this->conn->lastInsertId();
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to insert announcement', 0, $e);
        }
    }

    public function updateAnnouncement(int $announcementId, string $title, string $text, ?string $imagePath, string $datePublication) : bool {
        try {
            $sql = "UPDATE announcements 
                    SET Title = :title, Text = :text, Image_Path = :imagePath, Date_Publication = :datePublication, Updated_At = NOW()
                    WHERE Announcement_Id = :announcementId";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':announcementId', $announcementId, PDO::PARAM_INT);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':text', $text);
            $stmt->bindParam(':imagePath', $imagePath);
            $stmt->bindParam(':datePublication', $datePublication);
            return $stmt->execute();
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to update announcement', 0, $e);
        }
    }

    public function deleteAnnouncement(int $announcementId) : bool {
        try {
            // First get image path to delete physical file
            $announcement = $this->getAnnouncementById($announcementId);
            if (!$announcement) {
                return false;
            }

            // Delete database record
            $sql = "DELETE FROM announcements 
                    WHERE Announcement_Id = :announcementId";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':announcementId', $announcementId, PDO::PARAM_INT);
            $result = $stmt->execute();

            // Delete physical image file if exists
            if ($result && isset($announcement['Image_Path']) && !empty($announcement['Image_Path'])) {
                $imagePath = __DIR__ . '/../../../' . $announcement['Image_Path'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            return $result;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to delete announcement', 0, $e);
        }
    }

    public function getPublicAnnouncements(int $limit = 10) : array {
        try {
            $sql = "SELECT * FROM announcements 
                    WHERE Date_Publication <= CURDATE()
                    ORDER BY Date_Publication DESC, Created_At DESC
                    LIMIT :limit";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to fetch public announcements', 0, $e);
        }
    }
}

