<?php
declare(strict_types=1);
require_once __DIR__ . '/../../core/dbconnection.php';
require_once __DIR__ . '/../../Exceptions/DatabaseException.php';

class userEnrolleesModel {
    protected $conn;

    public function __construct() {
        $db = new Connect();
        $this->conn = $db->getConnection();
    }
    //GETTERS
    public function getPendingEnrollees() : array { //F 3.1.1
        try {
            $sql = "SELECT * FROM enrollee WHERE Enrollment_Status = 3";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to fetch pending enrollees',311,$e);
        }
    }
    private function getParentInformation(int $enrolleeId) : array { //F 3.1.2
        try {
            $sql = "SELECT pi.*, ep.Relationship
                         FROM enrollee_parents AS ep
                         JOIN parent_information AS pi ON ep.Parent_Id = pi.Parent_Id
                         WHERE ep.Enrollee_Id = :enrolleeId";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':enrolleeId',$enrolleeId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to fetch parent information',312,$e);
        }
    }
    public function getEnrolleeInformation(int $enrolleeId) : array { //F 3.1.3
        try {
                $sql = "SELECT e.*,ei.*,eb.*, ea.*,ds.*,pd.filename, pd.directory,
                    egl.Grade_Level AS E_Grade_Level,
                    lgl.Grade_Level AS L_Grade_Level
                FROM enrollee AS e
                INNER JOIN educational_information AS ei ON  e.Educational_Information_Id = ei.Educational_Information_Id
                INNER JOIN grade_level AS egl ON egl.Grade_Level_Id = ei.Enrolling_Grade_Level
                INNER JOIN grade_level AS lgl ON lgl.Grade_Level_Id = ei.Last_Grade_Level 
                INNER JOIN educational_background AS eb ON e.Educational_Background_Id = eb.Educational_Background_Id
                INNER JOIN enrollee_address AS ea ON e.Enrollee_Address_Id = ea.Enrollee_Address_Id
                INNER JOIN disabled_student AS ds ON e.Disabled_Student_Id = ds.Disabled_Student_Id
                INNER JOIN Psa_directory AS pd ON e.Psa_Image_Id = pd.Psa_Image_Id 
                WHERE e.Enrollee_Id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $enrolleeId,PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $parents = $this->getParentInformation($enrolleeId);
            if(!empty($parents)) {
                $parentInfo = [];
                foreach ($parents as $parent) {
                    $relationship = strtolower($parent['Relationship']);
                    $parentInfo[$relationship] = [
                        'first_name' => $parent['First_Name'],
                        'middle_name' => $parent['Middle_Name'],
                        'last_name' => $parent['Last_Name'],
                        'educational_attainment' => $parent['Educational_Attainment'],
                        'contact_number' => $parent['Contact_Number'],
                        'if_4ps' => $parent['If_4Ps']
                    ];
                }
                $result['Parent_Information'] = $parentInfo;
            }
            return $result;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to fetch enrollee information',313,$e);
        }
    }
    public function countEnrollees() : int { // F 3.1.4
        $sql = "SELECT COUNT(*) AS total FROM enrollee WHERE Enrollment_Status = 3";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int)$result['total'];
    }
    public function getPsaImg(int $enrolleeId) : ?string { //F 3.1.5
        try {
            $sql = " SELECT psa_directory.directory FROM enrollee 
                INNER JOIN psa_directory ON enrollee.Psa_Image_Id = Psa_directory.Psa_Image_Id
                WHERE Enrollee_Id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['id' => $id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return isset($result['directory']) ? (string)$result['directory'] : null;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to fetch PSA image file path',315,$e);
        }
    }
    public function getUserEnrollees(int $userId) : array{ //F 3.1.6
        try {
            $sql = "SELECT * FROM enrollee WHERE User_Id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT );
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $result;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to fetch user enrollees',316,$e);
        }
    }
    public function getUserStatus(int $userId, int $enrolleeId) : int { //F 3.1.7
        try {
            $sql = "SELECT Enrollment_Status FROM enrollee WHERE User_Id = :userId AND Enrollee_Id = :enrolleeId";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':enrolleeId', $enrolleeId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return (int)$result['Enrollment_Status'];
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to fetch user status',317,$e);
        }
    }
        public function getAllPartialEnrollees() : array { //F 3.1.8
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
            throw new DatabaseException('Failed to fetch partial enrollee information',318,$e);
        }
    }
    public function getEnrolled() : array { //F 3.1.9
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
            throw new DatabaseException('Failed to fetch all enrolled',319,$e);
        }
    }
    public function countEnrolled() : int { //F 3.1.10
        try {
            $sql = "SELECT COUNT(*) AS total FROM enrollee WHERE Enrollment_Status = 1;";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$result['total'];
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to count enrolled',3110,$e);
        }
    }
    public function searchEnrollees($query) : array { //F 3.1.11
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
            throw new DatabaseException('Failed to search for enrollees',3111,$e);
        }
    }
    public function getAllEnrollees() : array { //F 3.1.12
        try {
            $sql = "SELECT  e.Enrollee_Id,
                        e.Learner_Reference_Number,
                        e.Student_First_Name,
                        e.Student_Last_Name,
                        e.Student_Middle_Name,
                        enrolling_level.Grade_Level AS E_Grade_Level,
                        e.Enrollment_Status,     
                        p.First_Name,
                        p.Last_Name,
                        p.Middle_Name,
                        p.Contact_Number              
                FROM enrollee_parents
                INNER JOIN enrollee AS e ON enrollee_parents.Enrollee_Id = e.Enrollee_Id
                INNER JOIN educational_information ON e.Educational_Information_Id = educational_information.Educational_Information_Id 
                INNER JOIN grade_level AS enrolling_level ON enrolling_level.Grade_Level_Id = educational_information.Enrolling_Grade_Level
                INNER JOIN grade_level AS last_level ON last_level.Grade_Level_Id = educational_information.Last_Grade_Level
                INNER JOIN parent_information AS p ON enrollee_parents.Parent_Id = p.Parent_Id 
                WHERE p.Parent_Type = 'Guardian'";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to fetch all enrollees',3112,$e);
        }
    }
    public function countAllEnrollees() : int { //F 3.1.13
        try {
             $sql = "SELECT COUNT(*) AS total FROM enrollee";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$result['total'];
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to count all enrollees',0,$e);
        }
    }
    public function getUserTransactionStatus(int $enrolleeId) : array { //F 3.1.14
        try {
            $sql = "SELECT et.*, e.Enrollment_Status FROM enrollment_transactions AS et 
                    INNER JOIN enrollee AS e ON et.Enrollee_Id = e.Enrollee_Id WHERE et.Enrollee_Id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $enrolleeId);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: [];
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to fetch enrollee transaction',3114,$e);  
        }
    }
    public function getPSAImageData($enrolleeId) : ?array { //F 3.1.15
        try {
            $sql = "SELECT pd.filename, pd.directory 
                    FROM enrollee e
                    JOIN Psa_directory pd ON e.Psa_Image_Id = pd.Psa_Image_Id
                    WHERE e.Enrollee_Id = :enrolleeId";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':enrolleeId', $enrolleeId);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } 
        catch (PDOException $e) {
            throw new DatabaseException('Failed to get image data',3115,$e);
        }
    }
    //HELPERS
    private function setResubmitStatus(int $enrolleeId) : bool { //F 3.2.1
        $result = true;
        try {
            $sql = "UPDATE enrollment_transactions SET Transaction_Status = 3 WHERE Enrollee_ID = :enrolleeId";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':enrolleeId', $enrolleeId, PDO::PARAM_INT);
            if(!$stmt->execute()) {
                $result = false;
            }
            return $result;
            }
        catch (PDOException $e) {
            throw new DatabaseException('Failed to update resubmission status',321,$e);
        }
    }
    private function updateEducationalInformation(int $enrolleeId, array $data) : bool { //F 3.2.2
        $result = true;
        try {
            $sql = "UPDATE educational_information ei 
                JOIN enrollee e ON e.Educational_Information_ID = ei.Educational_Information_ID
                SET 
                ei.Enrolling_Grade_Level = :enrollingGrade,
                ei.Last_Grade_Level = :lastGrade,
                ei.Last_Year_Attended = :lastYear
                WHERE e.Enrollee_ID = :enrolleeId";
            $stmt = $this->conn->prepare($sql);
            if(!$stmt->execute([
                ':enrollingGrade' => $data['enrolling_grade_level'],
                ':lastGrade' => $data['last_grade_level'],
                ':lastYear' => $data['last_year_attended'],
                ':enrolleeId' => $enrolleeId
            ])) {
                $result = false;
            }
            return $result;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to update educational information',322,$e);
        }
    }
    private function updateEducationalBackground(int $enrolleeId, array $data) : bool { //F 3.2.3
        $result = true;
        try {
            $sql = "UPDATE educational_background eb 
                JOIN enrollee e ON e.Educational_Background_ID = eb.Educational_Background_ID
                SET 
                eb.Last_School_Attended = :lastSchool,
                eb.School_Id = :schoolId,
                eb.School_Address = :schoolAddress,
                eb.School_Type = :schoolType
                WHERE e.Enrollee_ID = :enrolleeId";
            $stmt = $this->conn->prepare($sql);
            if(!$stmt->execute([
                ':lastSchool' => $data['last_school_attended'],
                ':schoolId' => $data['school_id'],
                ':schoolAddress' => $data['school_address'],
                ':schoolType' => $data['school_type'],
                ':enrolleeId' => $enrolleeId
            ])) {
                $result = false;
            };
            return $result;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to update educational background',323,$e);
        }
    }
    private function updateDisabledEnrolleeInfo(int $enrolleeId, array $data) : bool { //F 3.2.4
        $result = true;
        try {
            $sql = "UPDATE disabled_student ds 
                JOIN enrollee e ON e.Disabled_Student_ID = ds.Disabled_Student_ID
                SET 
                ds.Have_Special_Condition = :hasSpecialCondition,
                ds.Special_Condition = :specialCondition,
                ds.Have_Assistive_Tech = :hasAssistiveTech,
                ds.Assistive_Tech = :assistiveTech
                WHERE e.Enrollee_ID = :enrolleeId";

            $stmt = $this->conn->prepare($sql);
            if(!$stmt->execute([
                ':hasSpecialCondition' => $data['has_a_special_condition'],
                ':specialCondition' => $data['special_condition'],
                ':hasAssistiveTech' => $data['has_assistive_technology'],
                ':assistiveTech' => $data['assistive_technology'],
                ':enrolleeId' => $enrolleeId
            ])) {
                $result = false;
            }
            return $result;
        }
        catch(PDOException $e) {
            throw new DatabaseExceptio('Failed to update disability info',324,$e);
        }
    }
    private function updateEnrolleeAddress(int $enrolleeId, array $data) : bool { //F 3.2.5
        $result = true;
        try {
            $sql = "UPDATE enrollee_address AS ea 
                JOIN enrollee AS e ON e.Enrollee_Address_ID = ea.Enrollee_Address_ID
                SET 
                ea.Region_Code = :region,
                ea.Region = :regionName,
                ea.Province_Code = :province,
                ea.Province_Name = :provinceName,
                ea.Municipality_Code = :municipality,
                ea.Municipality_Name = :municipalityName,
                ea.Brgy_Code = :barangay,
                ea.Brgy_Name = :barangayName,
                ea.Subd_Name = :subdivision,
                ea.House_Number = :houseNumber
                WHERE e.Enrollee_ID = :enrolleeId";

            $stmt = $this->conn->prepare($sql);
            if(!$stmt->execute([
                ':region' => $data['region'],
                ':regionName' => $data['region_name'],
                ':province' => $data['province'],
                ':provinceName' => $data['province_name'],
                ':municipality' => $data['city-municipality'],
                ':municipalityName' => $data['city_municipality_name'],
                ':barangay' => $data['barangay'],
                ':barangayName' => $data['barangay_name'],
                ':subdivision' => $data['subdivision'],
                ':houseNumber' => $data['house_number'],
                ':enrolleeId' => $enrolleeId
            ])) {
                $result = false;
            }
            return $result;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to update enrollee address',325,$e);
        }
    }
    private function updateParentInformation(int $enrolleeId, array $data, string $relationship) : bool { //F 3.2.6
        $result = true;
        try {
            $sql = "UPDATE parent_information pi
                                JOIN enrollee_parents ep ON pi.Parent_Id = ep.Parent_Id
                                SET 
                                pi.First_Name = :firstName,
                                pi.Middle_Name = :middleName,
                                pi.Last_Name = :lastName,
                                pi.Educational_Attainment = :educationalAttainment,
                                pi.Contact_Number = :contactNumber,
                                pi.If_4Ps = :if4ps
                                WHERE ep.Enrollee_Id = :enrolleeId 
                                AND ep.Relationship = :relationship";

            $stmt = $this->conn->prepare($sql);
            if(!$stmt->execute([
                ':firstName' => $data['first_name'],
                ':middleName' => $data['middle_name'],
                ':lastName' => $data['last_name'],
                ':educationalAttainment' => $data['educational_attainment'],
                ':contactNumber' => $data['contact_number'],
                ':if4ps' => $data['if_4ps'],
                ':enrolleeId' => $enrolleeId,
                ':relationship' => ucfirst($relationship)
            ])) {
                $result = false;
            }
            return $result;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to update parent information',326,$e);
        }
    }
    private function updatePsaDirectory(int $enrolleeId, array $data) : bool { //F 3.2.7
        $result = true;
        if(!isset($data['psa_image']) || empty($data['psa_image']['filename'])) {
            return $result;
        }
        try {
            $sql = "UPDATE Psa_directory AS pd 
            INNER JOIN enrollee AS e ON e.Psa_Image_Id = pd.Psa_Image_Id
            SET  pd.filename = :filename, pd.directory = :directory WHERE e.Enrollee_Id = :enrolleeId";
            $stmt = $this->conn->prepare($sql);
            if(!$stmt->execute([
                ':enrolleeId'=> $enrolleeId,
                ':filename' => $data['psa_image']['filename'],
                ':directory' => $data['psa_image']['filepath']
            ])) {
                $result = false;
            }
            return $result;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to update psa directory',327,$e);
        }
    }
    //OPERATIONS
    public function updateEnrollee(int $enrolleeId, int $status) : bool { //F 3.3.1
        $isSuccess = true;
        try {
            $sql = "UPDATE enrollee SET Enrollment_Status = :status WHERE Enrollee_Id = :id"; 
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':status', $status, PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            if(!$stmt->execute()) {
                $isSuccess = false;
            }
            return $isSuccess;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to update enrollee',331,$e);
        }
    }
    public function insertEnrolleeTransaction(int $id , string $transactionCode , int $staffId, string $reason, string $description) : bool { //F 3.3.2
        $isSuccess = true;
        try {
            $sql ="INSERT INTO enrollment_transactions(Enrollee_Id,Transaction_Code, Staff_Id, Reason,Description)
            VALUES (:enrollee_id, :transaction_code, :staff_Id,:reason, :description)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':enrollee_id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':transaction_code', $transactionCode);
            $stmt->bindParam(':staff_Id', $staffId, PDO::PARAM_INT);
            $stmt->bindParam(':reason', $reason);
            $stmt->bindParam(':description', $description);
            if(!$stmt->execute()) {
                $isSuccess = false;
            }
            return $isSuccess;
        }   
        catch(PDOException $e) {
            throw new DatabaseException('Failed to insert enrollee transaction',332,$e);
        }
    } 
    public function updateEnrolleeInformation(int $enrolleeId, array $data) : bool { //F 3.3.3
        try {
            $this->conn->beginTransaction();
            //start executing enrollee information by part
            $isSuccessEducationalInformation = $this->updateEducationalInformation($enrolleeId, $data);
            $isSuccessEducationalBackground = $this->updateEducationalBackground($enrolleeId, $data);
            $isSuccessDisabledStudent = $this->updateDisabledEnrolleeInfo($enrolleeId, $data);
            $isSuccessEnrolleeAddress = $this->updateEnrolleeAddress($enrolleeId, $data);
            $isSuccessPsaDirectory = $this->updatePsaDirectory($enrolleeId, $data);
            //loop through each parent type
            foreach($data['parent_information'] as $relationship => $parents) {
                if(empty(array_filter($parents))) continue;
                if(!$this->updateParentInformation($enrolleeId, $parents, $relationship)) {
                    $this->conn->rollBack();
                    return false;
                }
            }
           if (!$isSuccessEducationalInformation || !$isSuccessEducationalBackground ||
            !$isSuccessDisabledStudent || !$isSuccessEnrolleeAddress || !$isSuccessPsaDirectory) {
                $this->conn->rollBack();
                return false;
            }
            $sql = "UPDATE enrollee SET 
                Student_First_Name = :firstName,
                Student_Last_Name = :lastName,
                Student_Middle_Name = :middleName,
                Student_Extension = :extension,
                Learner_Reference_Number = :lrn,
                Psa_Number = :psa,
                Age = :age,
                Birth_Date = :birthdate,
                Sex = :sex,
                Religion = :religion,
                Native_Language = :nativeLanguage,
                If_Cultural = :ifCultural,
                Cultural_Group = :culturalGroup,
                Student_Email = :email
                WHERE Enrollee_ID = :enrolleeId";
            $stmt = $this->conn->prepare($sql);
            if(!$stmt->execute([
                ':firstName' => $data['first_name'],
                ':lastName' => $data['last_name'],
                ':middleName' => $data['middle_name'],
                ':extension' => $data['extension'],
                ':lrn' => $data['lrn'],
                ':psa' => $data['psa'],
                ':age' => $data['age'],
                ':birthdate' => $data['birthdate'],
                ':sex' => $data['sex'],
                ':religion' => $data['religion'],
                ':nativeLanguage' => $data['native_language'],
                ':ifCultural' => $data['belongs_in_cultural_group'],
                ':culturalGroup' => $data['cultural_group'],
                ':email' => $data['email_address'],
                ':enrolleeId' => $enrolleeId
            ])) {
                $this->conn->rollBack();
                return false;
            }
            $this->conn->commit();
            return true;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to update enrollee information',333,$e);
        }
    }
}