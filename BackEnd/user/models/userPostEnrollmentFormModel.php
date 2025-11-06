<?php
declare(strict_types=1);
require_once __DIR__ .'/../../core/dbconnection.php';
require_once __DIR__ . '/../../Exceptions/DatabaseException.php';


class userPostEnrollmentFormModel {
    protected $conn;
    
    //automatically run and connect database
    public function __construct() {
        $db = new Connect();
        $this->conn = $db->getConnection();
    }
    //GETTERS
    // HELPERS
    private function educational_information(int $School_Year_Start, int $School_Year_End, int $If_LRNN_Returning, 
    int $Enrolling_Grade_Level, ?string $Last_Grade_Level, ?string $Last_Year_Attended) :int { //F 3.2.8
        try {
            $sql = "INSERT INTO educational_information (School_Year_Start, School_Year_End, If_LRN_Returning, Enrolling_Grade_Level, Last_Grade_Level, Last_Year_Attended) 
            VALUES (:School_Year_Start, :School_Year_End, :If_LRN_Returning, :Enrolling_Grade_Level,:Last_Grade_Level, :Last_Year_Attended)";
            $stmt = $this->conn->prepare($sql); 
            $stmt->bindValue(':School_Year_Start', $School_Year_Start, PDO::PARAM_INT);
            $stmt->bindValue(':School_Year_End', $School_Year_End, PDO::PARAM_INT);
            $stmt->bindValue(':If_LRN_Returning', $If_LRNN_Returning, PDO::PARAM_INT);
            $stmt->bindValue(':Enrolling_Grade_Level', $Enrolling_Grade_Level, PDO::PARAM_STR);
            $stmt->bindValue(':Last_Grade_Level', $Last_Grade_Level ?: null, PDO::PARAM_STR);
            $stmt->bindValue(':Last_Year_Attended', $Last_Year_Attended, $Last_Year_Attended === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            if (!$insert_educational_information->execute()) {
                $errorInfo = $insert_educational_information->errorInfo();
                error_log('SQL Error: ' . json_encode($errorInfo));
                throw new PDOException('Failed to execute educational information insert: ' . $errorInfo[2]);
            }
            return (int)$this->conn->lastInsertId();
        }
        catch (PDOException $e) {
            error_log("[".date('Y-m-d H:i:s')."]" . $e->getMessage() . "\n", 3, __DIR__ . '/../../../errorLogs.txt');
            throw new DatabaseException('Failed to insert educational background',328,$e);
        }
    }
    private function educational_background(string $Last_School_Attended, int $School_Id, string $School_Address, string $School_Type, string $Initial_School_Choice, 
        int $Initial_School_Id, string $Initial_School_Address) : int { //F 3.2.9
        try {
            $sql = "INSERT INTO educational_background (Last_School_Attended, School_Id, School_Address, School_Type, Initial_School_Choice, 
                                Initial_School_Id, Initial_School_Address)
                    VALUES (:Last_School_Attended, :School_Id, :School_Address, :School_Type, :Initial_School_Choice,:Initial_School_Id, :Initial_School_Address)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':Last_School_Attended', $Last_School_Attended);
            $stmt->bindParam(':School_Id', $School_Id);
            $stmt->bindParam(':School_Address', $School_Address);
            $stmt->bindParam(':School_Type', $School_Type);
            $stmt->bindParam(':Initial_School_Choice', $Initial_School_Choice);
            $stmt->bindParam(':Initial_School_Id', $Initial_School_Id);
            $stmt->bindParam(':Initial_School_Address', $Initial_School_Address);
            if (!$stmt->execute()) {
                throw new PDOException('Failed to execute educational background insert');
            }
            return (int)$this->conn->lastInsertId();
        } 
        catch (PDOException $e) {
            error_log("[".date('Y-m-d H:i:s')."]" . $e->getMessage() . "\n", 3, __DIR__ . '/../../../errorLogs.txt');
            throw new DatabaseException('Failed to insert educational background',329,$e);
        }
    }
    private function disabled_student(int $Have_Special_Condition, int $Have_Assistive_Tech, 
    ?string $Special_Condition, ?string $Assistive_Tech) : int { //F 3.2.10
        try {
            $sql = "INSERT INTO disabled_student (Have_Special_Condition, Have_Assistive_Tech,
                                    Special_Condition, Assistive_Tech)
                                    VALUES (:Have_Special_Condition, :Have_Assistive_Tech, :Special_Condition, :Assistive_Tech)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':Have_Special_Condition', $Have_Special_Condition);
            $stmt->bindParam(':Have_Assistive_Tech', $Have_Assistive_Tech);
            $stmt->bindParam(':Special_Condition', $Special_Condition);
            $stmt->bindParam(':Assistive_Tech', $Assistive_Tech);
            if (!$stmt->execute()) {
                throw new PDOException('Failed to execute disabled student insert');
            }
            return (int) $this->conn->lastInsertId();
        } 
        catch (PDOException $e) {
            error_log("[".date('Y-m-d H:i:s')."]" . $e->getMessage() . "\n", 3, __DIR__ . '/../../../errorLogs.txt');
            throw new DatabaseException('Failed to insert disabled student',3210,$e);
        } 
    }
    private function enrollee_address(int $House_Number, string $Subd_Name, string $Brgy_Name, int $Brgy_Code, string $Municipality_Name, int $Municipality_Code, 
    string $Province_Name, int $Province_Code, string $Region, int $Region_Code) : int { //F 3.2.11
        try {
            $sql = "INSERT INTO enrollee_address (House_Number, Subd_Name, Brgy_Name, Brgy_Code, Municipality_Name, Municipality_Code, 
                                    Province_Name, Province_Code, Region, Region_Code)
                                    VALUES (:House_Number, :Subd_Name, :Brgy_Name, :Brgy_Code, :Municipality_Name, :Municipality_Code, 
                                    :Province_Name, :Province_Code, :Region, :Region_Code)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':House_Number', $House_Number);
            $stmt->bindParam(':Subd_Name', $Subd_Name);
            $stmt->bindParam(':Brgy_Name', $Brgy_Name);
            $stmt->bindParam(':Brgy_Code', $Brgy_Code);
            $stmt->bindParam(':Municipality_Name', $Municipality_Name);
            $stmt->bindParam(':Municipality_Code', $Municipality_Code);
            $stmt->bindParam(':Province_Name', $Province_Name);
            $stmt->bindParam(':Province_Code', $Province_Code);
            $stmt->bindParam(':Region', $Region);
            $stmt->bindParam(':Region_Code', $Region_Code);
            if (!$stmt->execute()) {
                throw new PDOException('Failed to execute enrollee address insert');
            } 
            return (int)$this->conn->lastInsertId();
        }
        catch (PDOException $e) {
            error_log("[".date('Y-m-d H:i:s')."]" . $e->getMessage() . "\n", 3, __DIR__ . '/../../../errorLogs.txt');
            throw new DatabaseException('Failed to insert enrollee address',3211,$e);
        }
    }
    private function father_information(string $Father_First_Name, string $Father_Last_Name, ?string $Father_Middle_Name, string $Father_Parent_Type,
    string $Father_Educational_Attainment, string $Father_Contact_Number, int $FIf_4Ps) : int { //F 3.2.12
        try {
            $sql  = "INSERT INTO parent_information (First_Name, Last_Name, Middle_Name, Parent_Type, 
                                        Educational_Attainment, Contact_Number, If_4Ps)
                                        VALUES (:First_Name, :Last_Name, :Middle_Name, :Parent_Type, :Educational_Attainment,
                                        :Contact_Number, :If_4Ps)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':First_Name', $Father_First_Name);
            $stmt->bindParam(':Last_Name', $Father_Last_Name);
            $stmt->bindParam(':Middle_Name', $Father_Middle_Name);
            $stmt->bindParam(':Parent_Type', $Father_Parent_Type);
            $stmt->bindParam(':Educational_Attainment', $Father_Educational_Attainment);
            $stmt->bindParam(':Contact_Number', $Father_Contact_Number);
            $stmt->bindParam(':If_4Ps', $FIf_4Ps);
            if (!$stmt->execute()) {
                throw new PDOException('Failed to execute father information insert');
            } 
            return (int) $this->conn->lastInsertId();                                    
        }
        catch (PDOException $e) {
            error_log("[".date('Y-m-d H:i:s')."]" . $e->getMessage() . "\n", 3, __DIR__ . '/../../../errorLogs.txt');
            throw new DatabaseException('Failed to insert father information',3212,$e);
        }
    }
    private function mother_information(string $Mother_First_Name, string $Mother_Last_Name, ?string $Mother_Middle_Name, string $Parent_Type, 
    string $Mother_Educational_Attainment, string $Mother_Contact_Number, int $MIf_4Ps) : int {  //F 3.2.13
        try {
            $sql  = "INSERT INTO parent_information (First_Name, Last_Name, Middle_Name, Parent_Type, 
                                        Educational_Attainment, Contact_Number, If_4Ps)
                                        VALUES (:First_Name, :Last_Name, :Middle_Name, :Parent_Type, :Educational_Attainment,
                                        :Contact_Number, :If_4Ps)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':First_Name', $Mother_First_Name);
            $stmt->bindParam(':Last_Name', $Mother_Last_Name);
            $stmt->bindParam(':Middle_Name', $Mother_Middle_Name);
            $stmt->bindParam(':Parent_Type', $Parent_Type);
            $stmt->bindParam(':Educational_Attainment', $Mother_Educational_Attainment);
            $stmt->bindParam(':Contact_Number', $Mother_Contact_Number);
            $stmt->bindParam(':If_4Ps', $MIf_4Ps);
            if (!$stmt->execute()) {
                throw new PDOException('Failed to execute mother information insert');
            } 
            return (int) $this->conn->lastInsertId();                                    
        }
        catch (PDOException $e) {
            error_log("[".date('Y-m-d H:i:s')."]" . $e->getMessage() . "\n", 3, __DIR__ . '/../../../errorLogs.txt');
            throw new DatabaseException('Failed to insert mother information',3213,$e);
        }
    }
    private function guardian_information(string $Guardian_First_Name, string $Guardian_Last_Name, ?string $Guardian_Middle_Name, string $Parent_Type, 
    string $Guardian_Educational_Attainment, string $Guardian_Contact_Number, int $GIf_4Ps) :int { //F 3.2.14
        try {
            $sql  = "INSERT INTO parent_information (First_Name, Last_Name, Middle_Name, Parent_Type, 
                                        Educational_Attainment, Contact_Number, If_4Ps)
                                        VALUES (:First_Name, :Last_Name, :Middle_Name, :Parent_Type, :Educational_Attainment,
                                        :Contact_Number, :If_4Ps)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':First_Name', $Guardian_First_Name);
            $stmt->bindParam(':Last_Name', $Guardian_Last_Name);
            $stmt->bindParam(':Middle_Name', $Guardian_Middle_Name);
            $stmt->bindParam(':Parent_Type', $Parent_Type);
            $stmt->bindParam(':Educational_Attainment', $Guardian_Educational_Attainment);
            $stmt->bindParam(':Contact_Number', $Guardian_Contact_Number);
            $stmt->bindParam(':If_4Ps', $GIf_4Ps);
            if (!$stmt->execute()) {
                throw new PDOException('Failed to execute guardian information insert');
            } 
            return (int)$this->conn->lastInsertId();                                      
        }
        catch (PDOException $e) {
            error_log("[".date('Y-m-d H:i:s')."]" . $e->getMessage() . "\n", 3, __DIR__ . '/../../../errorLogs.txt');
            throw new DatabaseException('Failed to insert guardian information',3214,$e);
        }
    }
    private function psa_directory(string $filename, string $directory) :int { //F 3.2.15
        try {
            $sql = "INSERT INTO Psa_directory(filename, directory) 
                            VALUES (:filename, :directory)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':filename', $filename);
            $stmt->bindParam(':directory', $directory);
            if (!$stmt->execute()) {
                throw new PDOException('Failed to execute psa directory insert');
            } 
            return (int)$this->conn->lastInsertId();
        } 
        catch (PDOException $e) {
            error_log("[".date('Y-m-d H:i:s')."]" . $e->getMessage() . "\n", 3, __DIR__ . '/../../../errorLogs.txt');
            throw new DatabaseException('Failed to insert PSA image directory', 3215, $e);
        }
    }   
    private function insertParentToEnrolleeParents(int $enrolleeId,int $parentId,string $relationship) : bool{ //F 3.2.16
        try {
            $sql = "INSERT INTO enrollee_parents(Enrollee_Id, Parent_Id, Relationship) VALUES(:enrolleeId, :parentId, :relationship)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':enrolleeId', $enrolleeId);
            $stmt->bindParam(':parentId',$parentId);
            $stmt->bindParam(':relationship', $relationship);
            if(!$stmt->execute()) {
                throw new PDOException('Failed to execute enrollee parents insert');
                return false;
            }
            return true;
        }
        catch(PDOException $e) {
            error_log("[".date('Y-m-d H:i:s')."]" . $e->getMessage() . "\n", 3, __DIR__ . '/../../../errorLogs.txt');
            throw new DatabaseException('Failed to insert enrollee parernts',3216,$e);
        }
    }
    // OPERATIONS
    public function insert_enrollee(int $userId,int $schoolYearStart,int $schoolYearEnd,int $hasLrn,int $enrollingGradeLevel,?int $lastGradeLevel,?int $lastYearAttended,
    string $lastSchoolAttended,int $schoolId,string $schoolAddress,string $schoolType,string $initialSchoolChoice,int $initialSchoolId,string $initialSchoolAddress,
    int $hasSpecialCondition,int $hasAssistiveTech, ?string $specialCondition, ?string $assistiveTech,
    int $houseNumber,string $subdName, string $brgyName,int $brgyCode,string $municipalityName,int $municipalityCode,string $provinceName,int $provinceCode, 
    string $region,int $regionCode,
    string $fatherFirstName,string $Father_Last_Name,?string $fatherMiddleName,string $fatherEducationalAttainment, string $fatherCpNumber, 
    int $isFather4ps,
    string $motherFirstName,string $motherLastName,?string $motherMiddleName,string $motherEducationalAttainment, 
    string $motherCpNumber, int $isMother4ps,
    string $guardianFirstName,string $guardianLastName,?string $guardianMiddleName,string $guardianEducationalAttainment,
    string $guardianCpNumber , $GIf_4Ps,
    string $studentFirstName,string $studentLastName,?string $studentMiddleName,?string $studentSuffix,?int $lrn,int $psaNumber,string $birthDate, 
    int $age,string $sex,string $religion, 
    string $nativeLanguage,int $isCultural,?string $culturalGroup,string $studentEmail,int $enrollmentStatus,string $filename,string $directory) : bool { //F 3.3.4
        //Initialize variable for parent types
        $fatherParentType = 'Father';
        $motherParentType = 'Mother';
        $guardianParentType = 'Guardian';
        try{
            $this->conn->beginTransaction();
            //REF: 3.2.8
            $educationalInformationId = $this->educational_information($schoolYearStart, $schoolYearEnd, $hasLrn, $enrollingGradeLevel, $lastGradeLevel, $lastYearAttended);
            //REF: 3.2.9
            $educationalBackgroundId = $this->educational_background($lastSchoolAttended, $schoolId, $schoolAddress, $schoolType, 
            $initialSchoolChoice, $initialSchoolId, $initialSchoolAddress);
            //REF: 3.2.10
            $disabledStudentId = $this->disabled_student($hasSpecialCondition, $hasAssistiveTech, $specialCondition, $assistiveTech);
            //REF: 3.2.11
            $enrolleeAddressId = $this->enrollee_address($houseNumber, $subdName, $brgyName, $brgyCode, $municipalityName, 
            $municipalityCode, $provinceName, $provinceCode, $region, $regionCode);
            //REF: 3.2.12
            $fatherInformationId = $this->father_information($fatherFirstName, $Father_Last_Name, $fatherMiddleName, $fatherParentType, 
            $fatherEducationalAttainment, $fatherCpNumber, $isFather4ps);
            //REF: 3.2.13
            $motherInformationId = $this->mother_information($motherFirstName, $motherLastName, $motherMiddleName, $motherParentType, 
            $motherEducationalAttainment, $motherCpNumber, $isMother4ps);
            //REF: 3.2.14
            $guardianInformationId = $this->guardian_information($guardianFirstName, $guardianLastName, $guardianMiddleName, $guardianParentType, 
            $guardianEducationalAttainment, $guardianCpNumber, $GIf_4Ps);
            //REF: 3.2.15
            $psaDirectoryId = $this->images($filename, $directory);
            // Insert enrollee
            $sql = "INSERT INTO enrollee (User_Id,Student_First_Name, Student_Middle_Name, Student_Last_Name, Student_Extension, Learner_Reference_Number, Psa_Number, Birth_Date, Age, Sex, Religion, 
                            Native_Language, If_Cultural, Cultural_Group, Student_Email, Enrollment_Status, Enrollee_Address_Id,
                            Educational_Information_Id, Educational_Background_Id, Disabled_Student_Id, Psa_Image_Id)
                            VALUES (:User_Id,:Student_First_Name, :Student_Middle_Name, :Student_Last_Name, :Student_Extension, :Learner_Reference_Number, :Psa_Number, :Birth_Date, :Age, :Sex, :Religion, :Native_Language, 
                            :If_Cultural, :Cultural_Group, :Student_Email, :Enrollment_Status, :Enrollee_Address_Id, :Educational_Information_Id, 
                            :Educational_Background_Id, :Disabled_Student_Id, :Psa_Image_Id);";

            // just binding parameters
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':User_Id', $User_Id);
            $stmt->bindParam(':Student_First_Name', $studentFirstName);
            $stmt->bindParam(':Student_Middle_Name', $studentMiddleName);
            $stmt->bindParam(':Student_Last_Name', $studentLastName);
            $stmt->bindParam(':Student_Extension', $studentSuffix);
            $stmt->bindParam(':Learner_Reference_Number', $lrn);
            $stmt->bindParam(':Psa_Number', $psaNumber);
            $stmt->bindParam(':Birth_Date', $birthDate);
            $stmt->bindParam(':Age', $age);
            $stmt->bindParam(':Sex', $sex);
            $stmt->bindParam(':Religion', $religion);
            $stmt->bindParam(':Native_Language', $nativeLanguage);
            $stmt->bindParam(':If_Cultural', $isCultural);
            $stmt->bindParam(':Cultural_Group', $culturalGroup);
            $stmt->bindParam(':Student_Email', $studentEmail);
            $stmt->bindParam(':Enrollment_Status', $enrollmentStatus);
            $stmt->bindParam(':Enrollee_Address_Id', $enrolleeAddressId);
            $stmt->bindParam(':Educational_Information_Id', $educationalInformationId);
            $stmt->bindParam(':Educational_Background_Id', $educationalBackgroundId);
            $stmt->bindParam(':Disabled_Student_Id', $disabledStudentId);
            $stmt->bindParam(':Psa_Image_Id', $psaDirectoryId);
            if (!$stmt->execute()) {
                $this->conn->rollBack();
                throw new PDOException('Failed to execute enrollee isnert');
            } 
            // If the enrollee is successfully inserted, get the last inserted ID
            $enrolleeId = (int)$this->conn->lastInsertId();
            //REF: 3.2.16
            $insertFatherToEnrolleeParents = $this->insertParentToEnrolleeParents($enrolleeId,$fatherInformationId,$fatherParentType);
            if(!$insertFatherToEnrolleeParents) {
                $this->conn->rollBack();
                throw new PDOException('Failed to insert father to enrollee parents');
            }
            //REF: 3.2.16
            $insertMotherToEnrolleeParents = $this->insertParentToEnrolleeParents($enrolleeId, $motherInformationId, $motherParentType);
            if(!$insertMotherToEnrolleeParents) {
                $this->conn->rollBack();
                throw new PDOException('Failed to insert mother to enrollee parents');
            }
            //REF:3.2.16
            $insertGuardianToEnrolleeParents = $this->insertParentToEnrolleeParents($enrolleeId, $guardianInformationId,$guardianParentType);
            if(!$insertGuardianToEnrolleeParents) {
                $this->conn->rollBack();
                throw new PDOException('Failed to insert guardian to enrollee parents');
            }
            $this->conn->commit();
            return true;
        }
        catch(PDOException $e) {
            //rollback if something goes wrong
            $this->conn->rollBack();
            error_log("[".date('Y-m-d H:i:s')."]" . $e->getMessage() . "\n", 3, __DIR__ . '/../../../errorLogs.txt');
            throw new DatabaseException('Failed to insert enrollee',334,$e);
        }
    }
    //check matching numeric values in the database
    public function checkLRN(int $lrn, ?int $enrolleeId) : bool { //F 3.3.5
        try {
            $sql = 'SELECT 1 FROM enrollee WHERE Learner_Reference_Number = :lrn';
            if($enrolleeId !== null) {
                $sql .= ' AND Enrollee_Id != :id';
            }
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':lrn', $lrn);
            if($enrolleeId !== null) {
                $stmt->bindParam(':id',$enrolleeId);
            }
            $stmt->execute();
            $result = $stmt->fetchColumn();
            return (bool)$result;
        }
        catch(PDOException $e) {
            error_log("[".date('Y-m-d H:i:s')."]" . $e->getMessage() . "\n", 3, __DIR__ . '/../../../errorLogs.txt');
            throw new DatabaseException('Failed to check LRN',335,$e);
        }
    }
    public function checkPSA(int $psa, ?int $enrolleeId) : bool { //F 3.3.6
        try {
            $sql = 'SELECT 1 FROM enrollee WHERE Psa_Number = :psa';
            if($enrolleeId !== null) {
                $sql .= ' AND Enrollee_Id != :id';
            }
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':psa', $psa);
            if($enrolleeId !== null) {
                $stmt->bindParam(':id',$enrolleeId);
            }
            $stmt->execute();
            $result = $stmt->fetchColumn();
            return (bool)$result;
        }
        catch(PDOException $e) {
            error_log("[".date('Y-m-d H:i:s')."]" . $e->getMessage() . "\n", 3, __DIR__ . '/../../../errorLogs.txt');
            throw new DatabaseException('Failed to check PSA',336,$e);
        }
    }
}
?>