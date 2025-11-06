<?php
declare(strict_types=1);
require_once __DIR__ . '/../../core/dbconnection.php';
require_once __DIR__ . '/../../Exceptions/DatabaseException.php';

class staffEnrolleesModel {
    protected $conn;
    public function __construct() {
        $db = new Connect();
        $this->conn = $db->getConnection();
    }
    //GETTERS
    public function getPendingEnrollees():array{
        try {
            $sql = "SELECT e.Enrollee_Id, e.Learner_Reference_Number,e.Student_First_Name, e.Student_Last_Name, e.Student_Middle_Name,
                e.Age, e.Sex, e.Birth_Date FROM enrollee e WHERE Enrollment_Status = 3 AND Is_Handled = 0";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }
        catch(PDOException $e){
            throw new DatabaseException('Failed to fetch pending enrollees');
        }
    }
    //HELPERS
    //OPERATIONS
}