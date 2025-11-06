<?php
declare(strict_types=1);
require_once __DIR__ . '/../../core/dbconnection.php';
require_once __DIR__ . '/../../Exceptions/DatabaseException.php';

class adminEnrolleesModel {
    protected $conn;
    public function __construct() {
        $db = new Connect();
        $this->conn = $db->getConnection();
    }
    //GETTERS
    public function getEnrolleePersonalInformation(int $enrolleeId):array {
        try {
            $sql = "SELECT enrollee.* FROM enrollee WHERE Enrollee_Id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id'=>$enrolleeId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: [];
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to fetch Enrollee personal information',0,$e);
        }
    }
    public function getEnrolleeEducationalInformation(int $enrolleeId):array {
        try {
            $sql = "SELECT ei.*, egl.Grade_Level AS E_Grade_Level, lgl.Grade_Level AS L_Grade_Level FROM enrollee AS e 
                INNER JOIN educational_information AS ei ON ei.Educational_Information_Id = e.Educational_Information_Id 
                INNER JOIN grade_level AS egl ON egl.Grade_Level_Id = ei.Enrolling_Grade_Level
                LEFT JOIN grade_level AS lgl ON lgl.Grade_Level_Id = ei.Last_Grade_Level 
                WHERE Enrollee_Id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id'=>$enrolleeId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: [];
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to fetch Enrollee personal information',0,$e);
        }
    }
    public function getEnrolleeEducationalBackground(int $enrolleeId):array {
        try {
            $sql = "SELECT eb.* FROM enrollee AS e 
                INNER JOIN educational_background AS eb ON eb.Educational_Background_Id = e.Educational_Background_Id  
                WHERE Enrollee_Id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id'=>$enrolleeId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: [];
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to fetch Enrollee personal information',0,$e);
        }
    }
    public function getEnrolleeDisabilityInformation(int $enrolleeId):array {
        try {
            $sql = "SELECT ds.* FROM enrollee AS e 
                    INNER JOIN disabled_student AS ds ON ds.Disabled_Student_Id = e.Disabled_Student_Id
                    WHERE Enrollee_Id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id'=>$enrolleeId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: [];
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to fetch Enrollee personal information',0,$e);
        }
    }
    public function getEnrolleeAddress(int $enrolleeId):array {
        try {
            $sql = "SELECT ea.* FROM enrollee AS e 
                    INNER JOIN enrollee_address AS ea ON ea.Enrollee_Address_Id = e.Enrollee_Address_Id
                    WHERE Enrollee_Id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id'=>$enrolleeId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: [];
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to fetch Enrollee personal information',0,$e);
        }
    }
    public function getEnrolleeParentInformation(int $enrolleeId):array {
        try {
            $sql = "SELECT pi.* FROM enrollee_parents AS ep 
                    INNER JOIN parent_information AS pi ON pi.Parent_Id = ep.Parent_Id
                    INNER JOIN enrollee AS e ON e.Enrollee_Id = ep.Enrollee_id
                    WHERE e.Enrollee_Id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id'=>$enrolleeId]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result ?: [];
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to fetch Enrollee personal information',0,$e);
        }
    }
    public function getEnrollmentInformation($id) : array {
        try {
            $sql = "SELECT enrollee_parents.*,
                        enrollee.*,
                        educational_information.*,
                        educational_background.*,
                        enrollee_address.*,
                        disabled_student.*,
                        parent_information.*,
                        enrolling_level.Grade_Level AS E_Grade_Level,
                        last_level.Grade_Level AS L_Grade_Level
                FROM enrollee_parents
                INNER JOIN enrollee ON enrollee_parents.Enrollee_Id = enrollee.Enrollee_Id
                INNER JOIN educational_information ON  enrollee.Educational_Information_Id = educational_information.Educational_Information_Id
                INNER JOIN grade_level AS enrolling_level ON enrolling_level.Grade_Level_Id = educational_information.Enrolling_Grade_Level
                INNER JOIN grade_level AS last_level ON last_level.Grade_Level_Id = educational_information.Last_Grade_Level 
                INNER JOIN educational_background ON enrollee.Educational_Background_Id = educational_background.Educational_Background_Id
                INNER JOIN enrollee_address ON enrollee.Enrollee_Address_Id = enrollee_address.Enrollee_Address_Id
                INNER JOIN disabled_student ON enrollee.Disabled_Student_Id = disabled_student.Disabled_Student_Id
                INNER JOIN parent_information ON enrollee_parents.Parent_Id = parent_information.Parent_Id 
                WHERE enrollee_parents.Enrollee_Id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to fetch enrollment information', 0 ,$e);
        }
    }
    public function countEnrollees() : int {
        try {
            $sql = "SELECT COUNT(*) AS total FROM enrollee WHERE Enrollment_Status = 3";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return (int)$result['total'];   
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to count enrollee', 0,$e);
        }
    }
    public function getPsaImg($id) : ?string {
        try {
            $sql = " SELECT Psa_directory.directory FROM enrollee 
                INNER JOIN Psa_directory ON enrollee.Psa_Image_Id = Psa_directory.Psa_Image_Id
                WHERE Enrollee_Id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['id' => $id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return (string)$result['directory'] ?: null;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to fetch PSA directory', 0 ,$e);
        }
    }
    public function getAllPartialEnrollees() : array{
        try {
            $sql = "SELECT Learner_Reference_Number,
                        Student_First_Name,
                        Student_Last_Name,
                        Student_Middle_Name,
                        Enrollment_Status   
                FROM enrollee";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to fetch enrollee partial information', 0,$e);
        }
    }
    public function getEnrolled() : array {
        try {
            $sql = "SELECT * FROM enrollee_parents
                INNER JOIN enrollee ON enrollee_parents.Enrollee_Id = enrollee.Enrollee_Id
                INNER JOIN educational_information ON  enrollee.Educational_Information_Id = educational_information.Educational_Information_Id 
                INNER JOIN grade_level AS enrolling_level ON enrolling_level.Grade_Level_Id = educational_information.Enrolling_Grade_Level
                INNER JOIN grade_level AS last_level ON last_level.Grade_Level_Id = educational_information.Last_Grade_Level
                INNER JOIN parent_information ON enrollee_parents.Parent_Id = parent_information.Parent_Id 
                WHERE parent_information.Parent_Type = 'Guardian' AND Enrollment_Status = 1;";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to fetch the enrolled students',0,$e);
        }
    }
    public function countEnrolled() : int {
        try {
            $sql = "SELECT COUNT(*) AS total FROM enrollee WHERE Enrollment_Status = 1;";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$result['total'];
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to count the enrolled',0,$e);
        }
    }
    public function searchEnrollees($query) : array {
        try {
            $query = "%$query%";
            $sql = "SELECT * FROM enrollee
                    WHERE Enrollment_Status = 3 AND Student_First_Name LIKE :search
                    OR Student_Last_Name LIKE :search";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':search', $query);

            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to search for enrollee',0,$e);        
        }
    }
    public function countAllEnrollees():int{
        try {
            $sql = "SELECT COUNT(*) AS total FROM enrollee";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$result['total'];
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to count all the enrollees',0,$e);
        }
    }
    public function sendTransactionStatus($id) : array{
        try {
            $sql = "SELECT et.*, e.Enrollment_Status FROM enrollment_transactions AS et 
                LEFT JOIN enrollee AS e ON et.Enrollee_Id = e.Enrollee_Id WHERE et.Enrollee_Id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to fetch the transaction status',0,$e);
        }
    }
    //HELPERS
    //OPERATIONS
    public function updateEnrollee(int $enrolleeId,int $status):bool{ 
        try {
            $sql = "UPDATE enrollee SET Enrollment_Status = :status WHERE Enrollee_Id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id'=>$enrolleeId,':status'=>$status]);
            if($stmt->rowCount() === 0) {
                return false;
            }
            return true;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to update enrollee status', 0,$e);
        }
    }
        public function setIsHandledStatus($id, $status):bool { //F 1.3.2
        try {
            $sql = "UPDATE enrollee SET Is_Handled = :status WHERE Enrollee_Id = :id";
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([':id'=>$id,':status'=>$status]);
            if($stmt->rowCount() === 0) {
                return false;
            }
            return true;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to set is handled status',132,$e);
        } 
    }
}