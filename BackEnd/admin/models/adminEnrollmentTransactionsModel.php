<?php
declare(strict_types=1);
require_once __DIR__ .'/../../core/dbconnection.php';
require_once __DIR__ . '/../../Exceptions/DatabaseException.php';

class adminEnrollmentTransactionsModel {
    protected $conn;
    //automatically run and connect database
    public function __construct() {
        $db = new Connect();
        $this->conn = $db->getConnection();
    }
    //GETTERS
    public function getAllEnrolleeTransactionsInformation() : array { //F 1.1.1
        try {
            $sql = "SELECT et.*,e.Student_First_Name, e.Student_Last_Name, e.Student_Middle_Name,
                    s.Staff_First_Name,s.Staff_Last_Name,s.Staff_Middle_Name FROM enrollment_transactions AS et
            LEFT JOIN enrollee AS e ON e.Enrollee_Id = et.Enrollee_Id
            LEFT JOIN staffs As s ON s.Staff_Id = et.Staff_Id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to fetch all enrollees',111,$e);
        }
    }
    public function getAllEnrolleeTransactionsCount() : ?int {
        try {
            $sql =  "SELECT COUNT(Enrollment_Transaction_Id) AS Transaction_Count FROM enrollment_transactions";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return (int)($result['Transaction_Count'] ?? 0);
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to count all transaction',112,$e);
        }
    }
    public function getFollowedUpTransactions() : array { //F 1.1.3
        try {
            $sql = "SELECT et.*,
                              e.Student_First_Name,
                              e.Student_Last_Name,
                              e.Student_Middle_Name,
                              e.Learner_Reference_Number,
                              s.Staff_First_Name,
                              s.Staff_Last_Name,
                              s.Staff_Middle_Name,
                              DATE(et.Created_At) AS Date,
                              r.Contact_Number
                        FROM enrollment_transactions et
                        INNER JOIN enrollee e ON et.Enrollee_Id = e.Enrollee_Id
                        INNER JOIN staffs s ON et.Staff_Id = s.Staff_Id
                        INNER JOIN users AS u ON u.User_Id = e.User_Id
                        INNER JOIN registrations AS r ON r.Registration_Id = u.Registration_Id 
                        WHERE et.Enrollment_Status = 4 AND Is_Approved = 0;";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to get all the followed up transactions',113,$e);
        }
    } 
    public function getEnrolleeTransaction(int $enrolleeId):array { //F 1.1.4
        try {
            $sql = "SELECT et.Enrollment_Transaction_Id, et.Enrollment_Status,et.Remarks,et.Transaction_Status FROM enrollment_transactions AS et  WHERE et.Enrollee_Id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id'=> $enrolleeId]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            return $data ?: [];
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to fetch enrollee transaction remarks',114,$e);
        }
    }
    public function getDeniedTransactions() : array{
        try {
            $sql = "SELECT et.*, 
                        e.Student_First_Name,
                        e.Student_Last_Name,
                        e.Student_Middle_Name,
                        e.Learner_Reference_Number,
                        s.Staff_First_Name,
                        s.Staff_Last_Name,
                        s.Staff_Middle_Name,
                        DATE(et.Created_At) AS Date
                        FROM enrollment_transactions et 
                        JOIN enrollee e ON et.Enrollee_Id = e.Enrollee_Id
                        JOIN staffs s ON et.Staff_Id = s.Staff_Id
                        WHERE et.Enrollment_Status = 2 AND Is_Approved = 0;";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchALL(PDO::FETCH_ASSOC);

            return $result;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to get all the denied transactions',115,$e);
        }
    }
    public function getEnrolledTransactions() : array {
        try {
            $sql = "SELECT et.*, 
                        e.Student_First_Name,
                        e.Student_Last_Name,
                        e.Student_Middle_Name,
                        e.Learner_Reference_Number,
                        s.Staff_First_Name,
                        s.Staff_Last_Name,
                        s.Staff_Middle_Name,
                        DATE(et.Created_At) AS Date
                        FROM enrollment_transactions et 
                        JOIN enrollee e ON et.Enrollee_Id = e.Enrollee_Id
                        JOIN staffs s ON et.Staff_Id = s.Staff_Id
                        WHERE et.Enrollment_Status = 1 AND Is_Approved = 0;";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchALL(PDO::FETCH_ASSOC);

            return $result;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to fetch the enrolled transactions',116,$e);
        }
    }
    //HELPERS
    //OPERATIONS
    public function updateNeededAction(int $transactionId, int $transactionStatus):bool{
        try {
            $sql = "UPDATE enrollment_transactions SET Transaction_Status = :transaction WHERE Enrollment_Transaction_Id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':transaction'=>$transactionStatus,':id'=>$transactionId]);
            if($stmt->rowCount() === 0) {
                return false;
            }
            return true;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to update the transaction status',0,$e);
        }
    }
    public function updateIsApprovedToTrue(int $enrolleeId, int $isApproved):bool {
        try {
            $sql = "UPDATE enrollment_transactions SET Is_Approved = :approved WHERE Enrollee_Id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':approved'=>$isApproved,':id'=>$enrolleeId]);
            if($stmt->rowCount() === 0) {
                return false;
            }
            return true;
        }
        catch(PDOException $e) {
            error_log("[".date('Y-m-d H:i:s')."]" . $e->getMessage() . "\n", 3, __DIR__  . '/../../errorLogs.txt');
            throw new DatabaseException('Failed to update the transaction status',0,$e);
        }
    }
    public function updateTransactionToFollowUp(int $enrolleeId,int $status):bool {
        try {
            $sql = "UPDATE enrollment_transactions SET Enrollment_Status = :status WHERE Enrollee_Id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':status'=>$status,':id'=>$enrolleeId]);
            if($stmt->rowCount() === 0) {
                return false;
            }
            return true;
        }
        catch(PDOException $e) {
            error_log("[".date('Y-m-d H:i:s')."]" . $e->getMessage() . "\n", 3, __DIR__  . '/../../errorLogs.txt');
            throw new DatabaseException('Failed to update the transaction status',0,$e);
        }
    }
}
?>