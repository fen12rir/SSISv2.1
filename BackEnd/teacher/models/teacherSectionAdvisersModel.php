<?php

declare(strict_types=1);
require_once __DIR__ . '/../../core/dbconnection.php';
require_once __DIR__ . '/../../Exceptions/DatabaseException.php';

class teacherSectionAdvisersModel {
    protected $conn;

    public function __construct() {
        $db = new Connect();
        $this->conn = $db->getConnection();
    }

    public function checkIfAdvisory(int $staffId, int $sectionId) : bool{
        try {
            $sql = "SELECT 1 FROM section_advisers WHERE Staff_Id = :id AND Section_Id = :sectionId";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $staffId);
            $stmt->bindParam(':sectionId', $sectionId);
            $stmt->execute();
            $result = $stmt->fetchColumn();

            return (bool)$result;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to check if advisory',0,$e);
        }
        
    }
    public function checkIfAdviser($id) {
        $sql = "SELECT Staff_Id, Section_Id FROM section_advisers WHERE Staff_Id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if($result) {
            return $result;
        }
        else {
            return false;
        }
    }
    public function getSectionName(int $sectionId) : array {
        try {
            $sql = "SELECT Section_Name, gl.Grade_Level FROM sections INNER JOIN grade_level AS gl ON sections.Grade_Level_Id = gl.Grade_Level_Id WHERE sections.Section_Id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $sectionId);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to fetch',0,$e);
        }
    }
    public function getSectionAdviserName(int $sectionId) : array {
        try {
            $sql = "SELECT staff.Staff_First_Name, staff.Staff_Last_Name, staff.Staff_Middle_Name FROM section_advisers AS sa
                LEFT JOIN staffs AS staff ON sa.Staff_Id = staff.Staff_Id WHERE sa.Section_Id = :sectionId";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':sectionId', $sectionId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to fetch adviser name',0,$e);
        }
    }
    public function getSectionStudents($sectionId) : array {
        try {
            $sql = "SELECT Student_Id, First_name, Last_Name, Middle_Name FROM students WHERE Section_Id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $sectionId);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to fetch section students',0,$e);
        } 
    }
    public function getSectionMaleStudents(int $sectionId): array {
        try {
            $sql = "SELECT s.Section_Id, st.Section_Id, e.Student_First_Name, e.Student_Middle_Name, e.Student_Last_Name FROM sections AS s 
                    LEFT JOIN students AS st ON s.Section_Id = st.Section_Id 
                    LEFT JOIN enrollee AS e ON st.Enrollee_Id = e.Enrollee_Id WHERE st.Section_Id = :sectionId AND st.Sex = 'Male'";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':sectionId', $sectionId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $result ?: [];
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to fetch male students', 0, $e);
        }
    }
    
    public function getSectionFemaleStudents(int $sectionId): array {
        try {
            $sql = "SELECT s.Section_Id, st.Section_Id, e.Student_First_Name, e.Student_Middle_Name, e.Student_Last_Name FROM sections AS s 
                    LEFT JOIN students AS st ON s.Section_Id = st.Section_Id 
                    LEFT JOIN enrollee AS e ON st.Enrollee_Id = e.Enrollee_Id WHERE st.Section_Id = :sectionId AND st.Sex = 'Female'";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':sectionId', $sectionId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $result ?: [];
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to fetch female students', 0, $e);
        }
    }
}