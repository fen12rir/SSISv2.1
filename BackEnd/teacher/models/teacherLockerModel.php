<?php
declare(strict_types=1);
require_once __DIR__ . '/../../core/dbconnection.php';
require_once __DIR__ . '/../../Exceptions/DatabaseException.php';
date_default_timezone_set('Asia/Manila');

class teacherLockerModel {
    protected $conn;

    public function __construct() {
        $db = new Connect();
        $this->conn = $db->getConnection();
    }

    public function getAllFiles(int $staffId) : array {
        try {
            $sql = "SELECT * FROM locker_files 
                    WHERE Staff_Id = :staffId 
                    ORDER BY Uploaded_At DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':staffId', $staffId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to fetch locker files', 0, $e);
        }
    }

    public function getFileById(int $fileId, int $staffId) : ?array {
        try {
            $sql = "SELECT * FROM locker_files 
                    WHERE Locker_File_Id = :fileId AND Staff_Id = :staffId";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':fileId', $fileId, PDO::PARAM_INT);
            $stmt->bindParam(':staffId', $staffId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to fetch file', 0, $e);
        }
    }

    public function insertFile(int $staffId, string $fileName, string $originalFileName, string $filePath, string $fileType, int $fileSize, ?string $description) : int {
        try {
            $sql = "INSERT INTO locker_files 
                    (Staff_Id, File_Name, Original_File_Name, File_Path, File_Type, File_Size, Description, Uploaded_At) 
                    VALUES (:staffId, :fileName, :originalFileName, :filePath, :fileType, :fileSize, :description, NOW())";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':staffId', $staffId, PDO::PARAM_INT);
            $stmt->bindParam(':fileName', $fileName);
            $stmt->bindParam(':originalFileName', $originalFileName);
            $stmt->bindParam(':filePath', $filePath);
            $stmt->bindParam(':fileType', $fileType);
            $stmt->bindParam(':fileSize', $fileSize, PDO::PARAM_INT);
            $stmt->bindParam(':description', $description);
            $stmt->execute();
            return (int)$this->conn->lastInsertId();
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to insert file', 0, $e);
        }
    }

    public function deleteFile(int $fileId, int $staffId) : bool {
        try {
            // First get file path to delete physical file
            $file = $this->getFileById($fileId, $staffId);
            if (!$file) {
                return false;
            }

            // Delete database record
            $sql = "DELETE FROM locker_files 
                    WHERE Locker_File_Id = :fileId AND Staff_Id = :staffId";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':fileId', $fileId, PDO::PARAM_INT);
            $stmt->bindParam(':staffId', $staffId, PDO::PARAM_INT);
            $result = $stmt->execute();

            // Delete physical file
            if ($result && isset($file['File_Path']) && file_exists($file['File_Path'])) {
                unlink($file['File_Path']);
            }

            return $result;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to delete file', 0, $e);
        }
    }

    public function updateFileDescription(int $fileId, int $staffId, ?string $description) : bool {
        try {
            $sql = "UPDATE locker_files 
                    SET Description = :description 
                    WHERE Locker_File_Id = :fileId AND Staff_Id = :staffId";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':fileId', $fileId, PDO::PARAM_INT);
            $stmt->bindParam(':staffId', $staffId, PDO::PARAM_INT);
            $stmt->bindParam(':description', $description);
            return $stmt->execute();
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to update file description', 0, $e);
        }
    }
}
