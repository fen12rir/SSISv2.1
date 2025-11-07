<?php
declare(strict_types=1);
require_once __DIR__ . '/../../core/dbconnection.php';
require_once __DIR__ . '/../../Exceptions/DatabaseException.php';

class teacherSubjectsModel {
    protected $conn;

    public function __construct() {
        $db = new Connect();
        $this->conn = $db->getConnection();
    }

    public function getTeacherSubjectsHandled(int $staffId) : array {
        try {
            $sql = "SELECT ss.Section_Subjects_Id, su.Subject_Name, s.Section_Name , ssc.Schedule_Day,
                DATE_FORMAT(ssc.Time_Start, '%H:%i') AS Time_Start, 
                DATE_FORMAT(ssc.Time_End, '%H:%i') AS Time_End 
                FROM section_subjects AS ss
                LEFT JOIN section_schedules AS ssc ON ssc.Section_Subjects_Id = ss.Section_Subjects_Id
                LEFT JOIN sections AS s ON ss.Section_Id = s.Section_Id
                LEFT JOIN subjects AS su ON ss.Subject_Id = su.Subject_Id WHERE ss.Staff_Id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $staffId);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to fetch subjects handled',0,$e);
        }
    }

    public function getSectionSubjects(int $sectionId) : array {
        try {
            $sql = "SELECT su.Subject_Name, s.Section_Name, ss.*, ssc.* FROM section_subjects AS ss
                LEFT JOIN section_schedules AS ssc ON ss.Section_Subjects_Id = ssc.Section_Subjects_Id
                LEFT JOIN sections AS s ON ss.Section_Id = s.Section_Id
                JOIN subjects AS su ON ss.Subject_Id = su.Subject_Id WHERE ss.Section_Id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $sectionId);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to fetch section subjects',0,$e);
        }
    }
}