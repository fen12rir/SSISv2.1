<?php
declare(strict_types=1);

require_once __DIR__ .'/../../core/dbconnection.php';
require_once __DIR__ . '/../../Exceptions/DatabaseException.php';

class adminStudentsModel {
    protected $conn;

    public function __construct() {
        $db = new Connect();
        $this->conn = $db->getConnection();
    }
    public function getAllStudents() : array{
        try {
            $sql = "SELECT  e.Enrollee_Id,
                            e.Student_First_Name,
                            e.Student_Middle_Name,
                            e.Student_Last_Name,
                            e.Learner_Reference_Number,
                            g.Grade_Level,
                            se.Section_Name,
                            e.Student_Email,
                            s.Student_Status
                    FROM students AS s
                    LEFT JOIN enrollee AS e ON s.Enrollee_Id = e.Enrollee_Id
                    LEFT JOIN grade_level AS g ON s.Grade_Level_Id = g.Grade_Level_Id
                    LEFT JOIN sections AS se ON s.Section_Id = se.Section_Id
                    ORDER BY FIELD(g.Grade_Level, 
                    'Kinder I', 'Kinder II', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6');";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to fetch all the students',0,$e);
        }
    }
    public function getStudentById($id) : ?array {
        try {
            $sql = "SELECT s.Student_Id, s.Enrollee_Id, s.Grade_Level_Id, s.Section_Id, s.Student_Status,
                            e.Student_First_Name, e.Student_Middle_Name, e.Student_Last_Name, 
                            e.Learner_Reference_Number, e.Student_Email,
                            g.Grade_Level, se.Section_Name
                    FROM students AS s
                    LEFT JOIN enrollee AS e ON s.Enrollee_Id = e.Enrollee_Id
                    LEFT JOIN grade_level AS g ON s.Grade_Level_Id = g.Grade_Level_Id
                    LEFT JOIN sections AS se ON s.Section_Id = se.Section_Id
                    WHERE s.Enrollee_Id = :id";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result ?:null;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to fetch the students by their ID',0,$e);
        }
    }
    
    public function getAdditionalStudentInfo($enrolleeId) : ?array {
        try {
            $sql = "SELECT 
                        e.Student_Extension, e.Psa_Number, e.Birth_Date, e.Age, e.Sex, e.Religion, e.Native_Language,
                        e.If_Cultural, e.Cultural_Group,
                        eb.Last_School_Attended, eb.School_Id AS Last_School_ID, eb.School_Address AS Last_School_Address, 
                        eb.School_Type AS Last_School_Type, eb.Initial_School_Choice, eb.Initial_School_Id, eb.Initial_School_Address,
                        ei.School_Year_Start, ei.School_Year_End, ei.Last_Grade_Level AS Last_Grade_Completed, 
                        ei.Last_Year_Attended AS Last_Year_Completed,
                        ds.Have_Special_Condition AS Has_Special_Needs, ds.Have_Assistive_Tech AS Has_Assistive_Tech,
                        ds.Special_Condition AS Special_Needs_Details, ds.Assistive_Tech AS Assistive_Tech_Details
                    FROM enrollee AS e
                    LEFT JOIN educational_background AS eb ON e.Educational_Background_Id = eb.Educational_Background_Id
                    LEFT JOIN educational_information AS ei ON e.Educational_Information_Id = ei.Educational_Information_Id
                    LEFT JOIN disabled_student AS ds ON e.Disabled_Student_Id = ds.Disabled_Student_Id
                    WHERE e.Enrollee_Id = :enrollee_id";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':enrollee_id', $enrolleeId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result ?:null;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to fetch student information',0,$e);
        }
    }
    
    public function updateStudent(array $data) : bool {
        try {
            $this->conn->beginTransaction();
            
            // Extract values
            $studentId = $data['student_id'];
            $enrolleeId = $data['enrollee_id'];
            $firstName = $data['first_name'];
            $middleName = $data['middle_name'];
            $lastName = $data['last_name'];
            $nameExtension = $data['name_extension'] ?? null;
            $lrn = $data['lrn'];
            $psaNumber = $data['psa_number'] ?? null;
            $birthdate = $data['birthdate'] ?? null;
            $age = $data['age'] ?? null;
            $gender = $data['gender'] ?? null;
            $religion = $data['religion'] ?? null;
            $nativeLanguage = $data['native_language'] ?? null;
            $email = $data['email'];
            $gradeLevel = $data['grade_level'];
            $section = !empty($data['section']) ? $data['section'] : null;
            $status = $data['status'];
            
            // Get educational background and information IDs
            $sql = "SELECT Educational_Background_Id, Educational_Information_Id, Disabled_Student_Id 
                    FROM enrollee 
                    WHERE Enrollee_Id = :enrollee_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':enrollee_id', $enrolleeId, PDO::PARAM_INT);
            $stmt->execute();
            $relatedIds = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $educationalBackgroundId = $relatedIds['Educational_Background_Id'] ?? null;
            $educationalInformationId = $relatedIds['Educational_Information_Id'] ?? null;
            $disabledStudentId = $relatedIds['Disabled_Student_Id'] ?? null;
            
            // Update enrollee table
            $sql = "UPDATE enrollee 
                    SET Student_First_Name = :first_name,
                        Student_Middle_Name = :middle_name,
                        Student_Last_Name = :last_name,
                        Student_Extension = :name_extension,
                        Learner_Reference_Number = :lrn,
                        Psa_Number = :psa_number,
                        Birth_Date = :birthdate,
                        Age = :age,
                        Sex = :gender,
                        Religion = :religion,
                        Native_Language = :native_language,
                        Student_Email = :email
                    WHERE Enrollee_Id = :enrollee_id";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':first_name', $firstName);
            $stmt->bindParam(':middle_name', $middleName);
            $stmt->bindParam(':last_name', $lastName);
            $stmt->bindParam(':name_extension', $nameExtension);
            $stmt->bindParam(':lrn', $lrn);
            $stmt->bindParam(':psa_number', $psaNumber);
            $stmt->bindParam(':birthdate', $birthdate);
            $stmt->bindParam(':age', $age);
            $stmt->bindParam(':gender', $gender);
            $stmt->bindParam(':religion', $religion);
            $stmt->bindParam(':native_language', $nativeLanguage);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':enrollee_id', $enrolleeId, PDO::PARAM_INT);
            $stmt->execute();
            
            // Update students table
            $sql = "UPDATE students 
                    SET Grade_Level_Id = :grade_level,
                        Section_Id = :section,
                        Student_Status = :status
                    WHERE Student_Id = :student_id";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':grade_level', $gradeLevel, PDO::PARAM_INT);
            $stmt->bindParam(':section', $section, $section === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindParam(':status', $status, PDO::PARAM_INT);
            $stmt->bindParam(':student_id', $studentId, PDO::PARAM_INT);
            $stmt->execute();
            
            // Update educational background if available
            if ($educationalBackgroundId && isset($data['last_school'])) {
                $sql = "UPDATE educational_background 
                        SET Last_School_Attended = :last_school,
                            School_Id = :last_school_id,
                            School_Address = :last_school_address,
                            School_Type = :last_school_type
                        WHERE Educational_Background_Id = :educational_background_id";
                        
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':last_school', $data['last_school']);
                $stmt->bindParam(':last_school_id', $data['last_school_id']);
                $stmt->bindParam(':last_school_address', $data['last_school_address']);
                $stmt->bindParam(':last_school_type', $data['last_school_type']);
                $stmt->bindParam(':educational_background_id', $educationalBackgroundId, PDO::PARAM_INT);
                $stmt->execute();
            }
            
            // Update educational information if available
            if ($educationalInformationId && isset($data['last_grade_completed'])) {
                $sql = "UPDATE educational_information 
                        SET Last_Grade_Level = :last_grade_completed,
                            Last_Year_Attended = :last_year_completed
                        WHERE Educational_Information_Id = :educational_information_id";
                
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':last_grade_completed', $data['last_grade_completed']);
                $stmt->bindParam(':last_year_completed', $data['last_year_completed']);
                $stmt->bindParam(':educational_information_id', $educationalInformationId, PDO::PARAM_INT);
                $stmt->execute();
            }
            
            // Update disabled student information if available
            if ($disabledStudentId && isset($data['has_special_needs'])) {
                $sql = "UPDATE disabled_student 
                        SET Have_Special_Condition = :has_special_needs,
                            Special_Condition = :special_needs_details,
                            Have_Assistive_Tech = :has_assistive_tech,
                            Assistive_Tech = :assistive_tech_details
                        WHERE Disabled_Student_Id = :disabled_student_id";
                
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':has_special_needs', $data['has_special_needs']);
                $stmt->bindParam(':special_needs_details', $data['special_needs_details']);
                $stmt->bindParam(':has_assistive_tech', $data['has_assistive_tech']);
                $stmt->bindParam(':assistive_tech_details', $data['assistive_tech_details']);
                $stmt->bindParam(':disabled_student_id', $disabledStudentId, PDO::PARAM_INT);
                $stmt->execute();
            }
            
            $this->conn->commit();
            return true;
        }
        catch(PDOException $e) {
            $this->conn->rollBack();
            throw new DatabaseException('failed to update student data',0,$e);
        }
    }
    public function getAllGradeLevels() : array {
        try {
            $sql = "SELECT * FROM grade_level ORDER BY Grade_Level_Id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to fetch all grade levels',0,$e);
        }
    }
    public function getSectionsByGradeLevel($gradeLevelId) : array {
        try {
            $sql = "SELECT * FROM sections WHERE Grade_Level_Id = :grade_level_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':grade_level_id', $gradeLevelId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to fetch sections per grade level', 0,$e);
        }
    }
    public function getAvailableStudents($sectionId) : array {
        try {
            $sql = "SELECT s.Student_Id, s.First_Name, s.Last_Name, s.Middle_Name FROM students AS s 
                INNER JOIN sections AS sec ON s.Grade_Level_Id = sec.Grade_Level_Id
                WHERE sec.Section_Id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $sectionId);
            $stmt->execute();
            $result =$stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;
        }    
        catch(PDOException $e) {
            throw new DatabaseException('Fetching available students failed', 0, $e);
        }
    }
    public function getCheckedStudents($id) : array { //gets the id of all students from an array of students that are checked
        try {
            $sql = "SELECT Student_Id FROM students WHERE Section_Id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Fetching checked students failed', 0, $e);
        }
    }
    //update students section helper function 1
    private function getAvailableStudentIds($sectionId) : array {
        try {
            $sql = "SELECT s.Student_Id FROM students AS s 
                INNER JOIN sections AS sec ON s.Grade_Level_Id = sec.Grade_Level_Id
                WHERE sec.Section_Id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $sectionId);
            $stmt->execute();
            $result =$stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;
        }    
        catch(PDOException $e) {
            throw new DatabaseException('Fetching available students failed', 0, $e);
        }
    }
    //helper 2
    private function updateStudentSection($studentId,$sectionId) : bool {
        try {
            $sql = "UPDATE students SET Section_Id = :sectionId WHERE Student_Id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $studentId);
            $stmt->bindParam(':sectionId', $sectionId);
            $result = $stmt->execute();

            return $result;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to update section',0,$e);
        }
    }
    //helper 3
    private function updateUncheckedStudents($studentId) : bool {
        try {
            $sql = "UPDATE students SET Section_Id = null WHERE Student_Id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $studentId);
            $result = $stmt->execute();

            return $result;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to update the unchecked students',0,$e);
        }
    }
    public function updateStudentSectionChanges(int $sectionId, array $studentIds) : array {
        $results = [
            'success'=> [],
            'failed'=> []
        ];
        try {
            //get all available student ids
            $studentId = $this->getAvailableStudentIds($sectionId); 
            foreach($studentId as $ids) {
                $students = (int)$ids['Student_Id'];
                $isChecked = in_array($students, $studentIds);
                if($isChecked) {
                    $check = $this->updateStudentSection($students, $sectionId);
                    if(!$check) {
                        $results['failed'][] = $students;
                    }
                    else {
                        $results['success'][] = $students;
                    }
                }
                else {
                    $uncheck = $this->updateUncheckedStudents($students);
                    if(!$uncheck) {
                        $results['failed'][] = $students;
                    }
                    else {
                        $results['success'][] = $students;
                    }
                }
            }

            return $results;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to update student changes',0,$e);
        }
    }
    public function insertEnrolleeToStudent(int $enrolleeId) : bool {
        try {
            $sql = "INSERT INTO students(Enrollee_Id, First_Name, Last_Name, Middle_Name, Age, Sex, LRN, Grade_Level_Id, Student_Status)
                    SELECT e.Enrollee_Id, e.Student_First_Name, e.Student_Last_Name, 
                    e.Student_Middle_Name, e.Age, e.Sex, e.Learner_Reference_Number, Enrolling_Grade_Level, 1 AS Status FROM enrollee AS e
                     JOIN educational_information AS ei ON e.Educational_Information_Id = ei.Educational_Information_Id
                     WHERE e.Enrollee_Id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $enrolleeId);
            $stmt->execute();
            if($stmt->rowCount() === 0) {
                return false;
            }
            return true;
        }
        catch(PDOException $e) {
            $this->conn->rollBack();
            throw new DatabaseException('Failed to insert the enrollee to student',0,$e);
        }
    }
    public function searchStudents($query) : array{
        try {
            $query = "%$query%";
            $sql = "SELECT * FROM students WHERE First_Name LIKE :search OR Last_Name LIKE :search OR Middle_Name LIKE :search";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':search'. $query);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to search for students',0, $e);
        }
    }
}