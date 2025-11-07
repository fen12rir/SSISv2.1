<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/teacherGradesModel.php';
require_once __DIR__ . '/../../Exceptions/DatabaseException.php';

class teacherGradesController {
    protected $gradesModel;

    public function __construct() {
        $this->gradesModel = new teacherGradesModel();
    }

    //API
    public function apiFetchSectionSubjectStudents(int $sectionSubjectId, int $staffId) : array {
        try {
            $data = $this->gradesModel->getStudentsOfSectionSubject($sectionSubjectId, $staffId);
            if(empty($data)) {
                return [
                    'httpcode'=> 200,
                    'success'=> true,
                    'message'=> 'No students found for this subject',
                    'data'=> []
                ];
            }
            return [
                'httpcode'=> 200,
                'success'=> true,
                'message'=> 'Students successfully fetched',
                'data'=> $data
            ];
        }
        catch(DatabaseException $e) {
            return [
                'httpcode'=> 500,
                'success'=> false,
                'message'=> 'There was a problem on our side: ' .$e->getMessage(),
                'data'=> []
            ];
        }
        catch(Exception $e) {
            return [
                'httpcode'=> 400,
                'success'=> false,
                'message'=> 'There was an unexpected problem: ' . $e->getMessage(),
                'data'=> []
            ];
        }
    }
    public function apiSaveGrades(int $sectionSubjectId, int $staffId, array $grades): array {
        try {
            // Verify the teacher has access to this section subject
            $verifySql = "SELECT 1 FROM section_subjects 
                         WHERE Section_Subjects_Id = :sectionSubjectId 
                         AND Staff_Id = :staffId";
            $conn = $this->gradesModel->getConnection();
            $verifyStmt = $conn->prepare($verifySql);
            $verifyStmt->bindParam(':sectionSubjectId', $sectionSubjectId, PDO::PARAM_INT);
            $verifyStmt->bindParam(':staffId', $staffId, PDO::PARAM_INT);
            $verifyStmt->execute();
            
            if (!$verifyStmt->fetch()) {
                return [
                    'httpcode' => 403,
                    'success' => false,
                    'message' => 'Unauthorized: You do not have access to grade this subject',
                    'data' => []
                ];
            }

            $conn->beginTransaction();
            
            foreach ($grades as $gradeData) {
                $studentId = (int)$gradeData['student_id'];
                $quarter = (int)$gradeData['quarter'];
                $gradeValue = (float)$gradeData['grade'];
                
                // Validate grade value
                if ($gradeValue < 0 || $gradeValue > 100) {
                    $conn->rollBack();
                    return [
                        'httpcode' => 400,
                        'success' => false,
                        'message' => "Invalid grade value for student ID {$studentId}, quarter {$quarter}. Grade must be between 0 and 100.",
                        'data' => []
                    ];
                }
                
                $this->gradesModel->saveOrUpdateGrade($studentId, $sectionSubjectId, $quarter, $gradeValue);
            }
            
            $conn->commit();
            
            return [
                'httpcode' => 200,
                'success' => true,
                'message' => 'Grades saved successfully',
                'data' => []
            ];
        }
        catch(DatabaseException $e) {
            $conn = $this->gradesModel->getConnection();
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            return [
                'httpcode' => 500,
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage(),
                'data' => []
            ];
        }
        catch(Exception $e) {
            $conn = $this->gradesModel->getConnection();
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            return [
                'httpcode' => 400,
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'data' => []
            ];
        }
    }

    //HELPERS
    //VIEW
    public function viewSubjectsToGrade(int $staffId) : array {
        try {
            if(empty($staffId)) {
                return [
                    'success'=> false,
                    'message'=> 'Staff ID not found',
                    'data'=> []
                ];
            }
            $subjectsToGrade = $this->gradesModel->getSubjectsToGrade($staffId);
            if(empty($subjectsToGrade)) {
                return [
                    'success'=> false,
                    'message'=> 'No subjects to grade',
                    'data'=> []
                ];
            }
            return [
                'success'=> true,
                'message'=> 'Subjects to grade successfully fetched',
                'data'=> $subjectsToGrade
            ];
        }
        catch(DatabaseException $e) {
            return [
                'success'=> false,
                'message'=> 'There was a problem on our side: ' . $e->getMessage(),
                'data'=> []
            ];
        }
        catch(Exception $e) {
            return [
                'success'=> false,
                'message'=> 'There was an unexpected problem: '.$e->getMessage(),
                'data'=> []
            ];
        }
    }
}