<?php
declare(strict_types=1);
require_once __DIR__ . '/../../core/dbconnection.php';
require_once __DIR__ . '/../../Exceptions/DatabaseException.php';

class teacherDashboardModel {
    protected $conn;

    public function __construct() {
        $db = new Connect();
        $this->conn = $db->getConnection();
    }

    public function getSubjectsCount(int $staffId): int {
        try {
            $sql = "SELECT COUNT(DISTINCT ss.Section_Subjects_Id) AS count 
                    FROM section_subjects AS ss 
                    WHERE ss.Staff_Id = :staffId";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':staffId', $staffId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($result['count'] ?? 0);
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to fetch subjects count', 0, $e);
        }
    }

    public function getTotalStudentsToGrade(int $staffId): int {
        try {
            $sql = "SELECT COUNT(DISTINCT st.Student_Id) AS count 
                    FROM section_subjects AS ss 
                    LEFT JOIN students AS st ON st.Section_Id = ss.Section_Id 
                    WHERE ss.Staff_Id = :staffId";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':staffId', $staffId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($result['count'] ?? 0);
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to fetch students count', 0, $e);
        }
    }

    public function getLockerFilesCount(int $staffId): int {
        try {
            $sql = "SELECT COUNT(*) AS count 
                    FROM locker_files 
                    WHERE Staff_Id = :staffId";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':staffId', $staffId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($result['count'] ?? 0);
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to fetch locker files count', 0, $e);
        }
    }

    public function isAdviser(int $staffId): bool {
        try {
            $sql = "SELECT 1 FROM section_advisers WHERE Staff_Id = :staffId LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':staffId', $staffId, PDO::PARAM_INT);
            $stmt->execute();
            return (bool)$stmt->fetchColumn();
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to check if adviser', 0, $e);
        }
    }
}

