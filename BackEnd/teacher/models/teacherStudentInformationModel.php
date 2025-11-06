<?php
declare(strict_types=1);
require_once __DIR__ . '/../../core/dbconnection.php';
require_once __DIR__ . '/../../Exceptions/DatabaseException.php';
class teacherStudentInformationModel {
    protected $conn;
    public function __construct() {
        $db = new Connect();
        $this->conn = $db->getConnection();
    }
    public function getStudentInformation(int $studentId):array {
        try {
            $sql = "SELECT *, gl.Grade_Level, se.Section_Name FROM students AS s 
                INNER JOIN grade_level AS gl ON s.Grade_Level_Id = gl.Grade_Level_Id 
                INNER JOIN sections AS se ON s.Section_Id = se.Section_Id
                WHERE Student_Id = :studentId";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':studentId', $studentId);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to fetch student information',0,$e);
        }
    }
    public function getStudentgrades(int $studentId):array {
        try {
            $sql = "SELECT s.Subject_Name, 
                MAX(CASE WHEN sg.Quarter = 1 THEN sg.Grade_Value END) AS 1st,
                MAX(CASE WHEN sg.Quarter = 2 THEN sg.Grade_Value END) AS 2nd,
                MAX(CASE WHEN sg.Quarter = 3 THEN sg.Grade_Value END) AS 3rd,
                MAX(CASE WHEN sg.Quarter = 4 THEN sg.Grade_Value END) AS 4th
                FROM section_subjects AS ss
                INNER JOIN subjects AS s ON s.Subject_Id = ss.Subject_Id
                LEFT JOIN student_grades AS sg ON sg.Section_Subjects_Id = ss.Section_Subjects_Id AND sg.Student_Id = :studentId 
                GROUP BY s.Subject_Name
                ORDER BY s.Subject_Name";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':studentId'=> $studentId]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to fetch student grades',0,$e);
        }
    }
    public function getStudentAddress($studentId) {
        $sql = "SELECT ea.* FROM students AS s
                INNER JOIN enrollee AS e ON s.Enrollee_Id = e.Enrollee_Id
                INNER JOIN enrollee_address AS ea ON e.Enrollee_Address_Id = ea.Enrollee_Address_Id WHERE Student_Id = :studentId";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':studentId', $studentId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if($result) {
            return $result;
        } else {
            return false;
        }
    }
    public function getEducationalInformation($studentId) {
        $sql = "SELECT ei.* FROM students AS s 
                INNER JOIN enrollee AS e ON s.Enrollee_Id = e.Enrollee_Id
                INNER JOIN educational_information AS ei ON e.Educational_Information_Id = ei.Educational_Information_Id WHERE Student_Id = :studentId";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':studentId', $studentId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if($result) {
            return $result;
        } else {
            return false;
        }
    }
    
    public function getStudentParents($studentId) {
        $sql = "SELECT pi.* FROM students AS s 
                INNER JOIN enrollee AS e ON s.Enrollee_Id = e.Enrollee_Id
                INNER JOIN enrollee_parents AS ep ON e.Enrollee_Id = ep.Enrollee_Id 
                INNER JOIN parent_information AS pi ON ep.Parent_Id = pi.Parent_Id
                WHERE Student_Id = :studentId";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':studentId', $studentId);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if($result) {
            return $result;
        } else {
            return false;
        }
    }
}