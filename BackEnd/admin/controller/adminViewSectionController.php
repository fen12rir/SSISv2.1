<?php

require_once __DIR__ . '/../models/adminSectionsModel.php';
require_once __DIR__ . '/../models/adminStudentsModel.php';
require_once __DIR__ . '/../../Exceptions/DatabaseException.php';

class adminViewSectionController {
    protected $studentsModel;
    protected $sectionsModel;

    public function __construct() {
        $this->studentsModel = new adminStudentsModel();
        $this->sectionsModel = new adminSectionsModel();
    }
    //API
    public function apiPostEditSectionDetails(?string $sectionName, ?int $adviserId, int $sectionId, array $studentIds) : array {      
        try {
            $section = $this->updateSectionName($sectionName, $sectionId);
            $adviser = $this->updateAdviserName($sectionId, $adviserId);
            $students = $this->updateStudents($sectionId, $studentIds);

            $isAllSuccess = ($section['success'] && $adviser['success'] && $students['success']);
            $isAnySuccess = $section['success'] || $adviser['success'] || $students['success'];

            $failedMessage = [];
            if(!$section['success']) $failedMessage[] = $section['message'];
            if(!$adviser['success']) $failedMessage[] = $adviser['message'];
            if(!$students['success']) $failedMessage[] = $students['message'];

            if($isAnySuccess && !empty($failedMessage)) {
                return [
                    'httpcode'=> 200,
                    'success' => true,
                    'partialSuccess'=> true,
                    'message'=> $failedMessage,
                    'data' => [
                        'section'=> $section,
                        'adviser'=> $adviser,
                        'student'=> $students
                    ]
                ];
            }
            if(!$isAllSuccess) {
                return [
                    'httpcode'=> 400,
                    'success'=> false,
                    'partialSuccess'=> false,
                    'message' => 'All updates failed',
                    'data'=> []                
                ];
            }
            return [
                'httpcode'=> 201,
                'success'=> true,
                'partialSuccess'=> false,
                'message'=> 'All updates are successful',
                'data' => [
                        'section'=> $section,
                        'adviser'=> $adviser,
                        'student'=> $students
                ]
            ];
        }
        catch(DatabaseException $e) {
            return [
                'httpcode'=> 500,
                'success'=> false,
                'partialSuccess'=> false,
                'message'=>'Database error: ' .$e->getMessage(),
                'data'=>[]
            ];
        }
        catch(Exception $e) {
            return [
                'httpcode'=>400,
                'success'=> false,
                'partialSuccess'=> false,
                'message'=> 'Error: '.$e->getMessage(),
                'data'=>[]
            ];
        }
    }
    //HELPERS
    private function updateSectionName(string $sectionName, int $sectionId) : array {
        try {
            if(empty($sectionName)) {
                return [
                    'success' => false,
                    'message'=> 'Section name cannot be empty',
                    'data'=> []
                ];
            }
            $section = $this->sectionsModel->updateSectionNameResult($sectionName, $sectionId);
            if($section['existing']) {
                return [
                    'success'=> false,
                    'message'=> 'Section name already exists. Please choose another one',
                    'data'=> []
                ];
            }
            if(!$section['success']) {
                return [
                    'success'=> false,
                    'message'=> 'Failed to update section name',
                    'data'=> []
                ];
            }
            return [
                'success'=> true,
                'message'=> 'Section name successfully updated',
                'data'=> $section
            ];
        }
        catch(DatabaseException $e) {
            return [
                'success'=> false,
                'message'=> 'There was a problem on our side ' . $e->getMessage(),
                'data'=> []
            ];
        }
        catch(Exception $e) {
            return [
                'success'=> false,
                'message'=> 'There was an unexpected problem ' . $e->getMessage(),
                'data'=> []
            ];
        }
    }
    private function updateAdviserName(int $sectionId, ?int $adviserId) : array {
        try {
            if($adviserId === null || $adviserId <= 0) {
                return [
                    'success'=> false,
                    'message'=> 'No adviser ID selected'
                ];
            }
            $adviser = $this->sectionsModel->updateAdviser($sectionId, $adviserId);
            if(!$adviser) {
                return [
                    'success'=> false,
                    'message'=> 'Update failed',
                    'data'=> []
                ];
            }
            return [
                'success'=> true,
                'message'=> 'Adviser name updated successfully',
                'data'=> []
            ];
        }
        catch(DatabaseException $e) {
            return [
                'success'=> false,
                'message'=> 'There was a problem on our side ' . $e->getMessage(),
                'data'=> []
            ];
        }
        catch(Exception $e) {
            return [
                'success'=> false,
                'message'=> 'There was an unexpected problem ' . $e->getMessage(),
                'data'=> []
            ];
        }
    }
    private function updateStudents(int $sectionId, array $studentIds) : array {
        try {
            if(empty($studentIds)) {
                return [
                    'success'=> false,
                    'message'=> 'No students were selected',
                ];
            }
            $students = $this->studentsModel->updateStudentSectionChanges($sectionId, $studentIds);
            if(!empty($students['failed'])) {
                return [
                    'success'=> (count($students['failed']) == count($studentIds) && empty($students['success'])),
                    'message'=> (count($students['failed']) == count($studentIds) && empty($students['success'])) ? 'Failed to update students' : 'Some failed to update',
                    'failed'=> $students['failed'],
                    'data'=> []
                ];
            }
            return [
                'success'=> true,
                'message'=> 'All changes are saved',
                'data'=> $students['success']
            ];
        }    
        catch(DatabaseException $e) {
            return [
                'success'=> false,
                'message'=> 'There was a problem on our side ' . $e->getMessage(),
                'data'=> []
            ];
        }
        catch(Exception $e) {
            return [
                'success'=> false,
                'message'=> 'There was an unexpected problem ' . $e->getMessage(),
                'data'=> []
            ];
        }
    }
    //VIEW
    public function viewEditSectionFormStudents(int $sectionId) : array {
        try {
            $students = $this->studentsModel->getAvailableStudents($sectionId);
            $getChecked = $this->studentsModel->getCheckedStudents($sectionId);
            $isChecked = array_column($getChecked, 'Student_Id');
            if(empty($students)) {
                return [
                    'success'=> false,
                    'message'=> 'No students Found for this section yet',
                    'data'=> []
                ];
            }
            foreach($students as &$student) {
                $student['isChecked'] = in_array($student['Student_Id'], $isChecked);
            }
            unset($student);
            return [
                'success'=> true,
                'message'=> 'Students successfully fetched',
                'data'=> $students
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
                'message'=> 'There was an unexpected problem: ' . $e->getMessage(),
                'data'=> []
            ];
        }
    } 
    public function viewEditSectionFormTeachers(int $sectionId) : array {
        try {
            $teachers = $this->sectionsModel->getAllTeachers();
            $currentAdviser = $this->sectionsModel->checkCurrentAdviser($sectionId);

            if(empty($teachers)) {
                return [
                    'success'=> false,
                    'message'=> 'No teachers found',
                    'data'=> []
                ];
            }
            foreach($teachers as &$teacher) {
                $teacherIds = (int)$teacher['Staff_Id'];
                $teacher['isSelected'] = ($currentAdviser!== null && $currentAdviser === $teacherIds);
            }
            unset($teacher);
            return [
                'success'=> true,
                'message'=> 'Teachers successfully fetched',
                'data'=> $teachers
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
                'message'=> 'There was an unexpected problem: ' . $e->getMessage(),
                'data'=> []
            ];
        }
    }    
    public function viewEditSectionFormSectionName(int $sectionId) : array {
        try {
            $sectionName = $this->sectionsModel->getSectionName($sectionId);
            if(empty($sectionName)) {
                return [
                    'success'=> false,
                    'message'=> 'No section name yet',
                    'data'=> []
                ];
            }
            return [
                'success'=> true,
                'message'=> 'Section name found',
                'data'=> $sectionName
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
                'message'=> 'There was an unexpected problem: ' . $e->getMessage(),
                'data'=> []
            ];
        }
    }
    public function viewSectionSubjectDetails(int $sectionId) : array {
        try {
            $data = $this->sectionsModel->getApplicableSubjectsByGradeLevel($sectionId);

            if(empty($data)) {
                return [
                    'success'=> false,
                    'message'=> 'No subject details yet',
                    'data' => []
                ];
            }
            if(!$data) {
                return [
                    'success'=> false,
                    'message'=> 'Failed to get section subjects',
                    'data'=> []
                ];
            }
            return [
                'success'=> true,
                'message'=> 'Subject details successfully fetched',
                'data'=> $data
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
                'message'=> 'There was an unexpected problem: ' . $e->getMessage(),
                'data'=> []
            ];
        }
    }
}