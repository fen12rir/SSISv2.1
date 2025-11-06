<?php
declare(strict_types=1);
require_once __DIR__ . '/../models/teacherStudentInformationModel.php';
class teacherStudentInformationController {
    protected $studentsModel;

    public function __construct() {
        $this->studentsModel = new teacherStudentInformationModel();
    }
    //API
    //HELPERS
    //VIEW
    public function viewStudentInformation(int $studentId):array {
        try {
            $data = $this->studentsModel->getStudentInformation($studentId);
            if(empty($data)) {
                return [
                    'success'=> false,
                    'message'=> 'There is no student information to show',
                    'data'=> []
                ];
            }
            return [
                'success'=> true,
                'message'=> 'Student information successfully fetched',
                'data'=> $data
            ];
        }   
        catch(DatabaseException $e) {
            return [
                'success'=> false,
                'message'=> 'There was a problem on our side',
                'data'=> []
            ];
        }
        catch(Exception $e) {
            return [
                'success'=> false,
                'message'=> 'There was an unexpected problem',
                'data'=> []
            ];
        }
    }
    public function viewStudentGrades(int $studentId):array {
        try {
            $data = $this->studentsModel->getStudentGrades($studentId);
            return [
                'success'=> true,
                'message'=> 'Grades successfully fetched',
                'data'=> $data
            ];
        }
        catch(DatabaseException $e) {
            return [
                'success'=> false,
                'message'=> 'There was a problem on our side',
                'data'=> []
            ];
        }
        catch(Exception $e) {
            return [
                'success'=> false,
                'message'=> 'There was an unexpected problem',
                'data'=> []
            ];
        }
    }
}