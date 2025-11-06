<?php
declare(strict_types=1);
require_once __DIR__ . '/../controller/teacherStudentInformationController.php';
require_once __DIR__ . '/../../Exceptions/IdNotFoundException.php';
require_once __DIR__ . '/../../core/tableDataTemplate.php';
class teacherStudentInformationView {
    protected $tableTemplate;
    protected $controller;
    protected $studentId;
    public function __construct() {
        $this->controller = new teacherStudentInformationController();
        $this->tableTemplate = new TableCreator();
        $this->studentId = isset($_GET['student_id']) ? (int)$_GET['student_id'] : null;
    }
    public function displayStudentInformation() {
        try {
            if(is_null($this->studentId)) {
                throw new IdNotFoundException('Student ID not recognized');
            } 
            $studentInfo = $this->controller->viewStudentInformation($this->studentId);
            if(!$studentInfo['success']) {
                echo '<div class="error-message">'.htmlspecialchars($studentInfo['message']).'</div>';
                return;
            }
            echo '<table><tbody>';     
                $data = $studentInfo['data'];
                $firstName = $data['First_Name'];
                $lastName = $data['Last_Name'];
                $hasMiddleInitial = !empty($data['Middle_Name']) ? $data['Middle_Name'] : '';
                $hasSuffix = !empty($data['Suffix']) ? ', ' . $data['Suffix'] : '';
                $fullName = $lastName . ', ' . $firstName . ' ' . $hasMiddleInitial . $hasSuffix;
                //echo associative table
                echo $this->tableTemplate->returnVerticalTables(['Buong Pangalan','Petsa ng Kapanganakan','Edad','LRN','Kasarian','Baitang','Section'],
                [$fullName,$data['Birthday'],$data['Age'],$data['LRN'],
                $data['Sex'],$data['Grade_Level'], $data['Section_Name']
                ], 'student-info-part');

            echo '</tbody></table>';
        }
        catch(IdNotFoundException $e) {
            echo '<div class="error-message">'.$e->getMessage().'<div>';
        }
        catch(Exception $e) {
            echo '<div class="error-message">'.$e->getMessage().'<div>';
        }
    }
    public function displayStudentGrades() {
        try {
            if(is_null($this->studentId)) {
                throw new IdNotFoundException('Student ID not recognized');
            } 
            $grades = $this->controller->viewStudentGrades($this->studentId);
            if(!$grades['success']) {
                echo '<div class="error-message">'.htmlspecialchars($studentInfo['message']).'</div>';
                return;
            }
            echo '<table><tbody>';     
            echo $this->tableTemplate->returnHorizontalTitles(['Subject', '1st Quarter', '2nd Quarter', '3rd Quarter', '4th Quarter'],'grades-header');
            foreach($grades['data'] as $grade) {
                $firstQ = !is_null($grade['1st']) ? $grade['1st'] : '';
                $secondQ = !is_null($grade['2nd']) ? $grade['2nd'] : '';
                $thirdQ = !is_null($grade['3rd']) ? $grade['3rd'] : '';
                $fourthQ = !is_null($grade['4th']) ? $grade['4th'] : '';
                echo $this->tableTemplate->returnHorizontalRows([$grade['Subject_Name'],$firstQ,$secondQ,$thirdQ.$fourthQ]);
            }    
            echo '</tbody></table>';
        }
        catch(IdNotFoundException $e) {
            echo '<div class="error-message">'.$e->getMessage().'<div>';
        }
        catch(Exception $e) {
            echo '<div class="error-message">'.$e->getMessage().'<div>';
        }
    }
}
