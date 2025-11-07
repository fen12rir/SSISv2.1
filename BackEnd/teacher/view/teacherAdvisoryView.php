<?php
declare(strict_types=1);
require_once __DIR__ . '/../controller/teacherAdvisoryController.php';
require_once __DIR__ . '/../../core/tableDataTemplate.php';
require_once __DIR__ . '/../../core/safeHTML.php';

class teacherAdvisoryView {
    protected $conn;
    protected $advisoryController;
    protected $id;
    protected $staffId;
    protected $tableTemplate;

    public function __construct() {
        $this->advisoryController = new teacherAdvisoryController();
        $this->tableTemplate = new TableCreator();
        $this->id = null;
        $this->staffId = null;
        
        if(isset($_GET['adv_id']) && !empty($_GET['adv_id'])) {
            $this->id = (int)$_GET['adv_id']; //this is the section id
        }
        if(isset($_SESSION['Staff']['Staff-Id']) && !empty($_SESSION['Staff']['Staff-Id'])) {
            $this->staffId = (int)$_SESSION['Staff']['Staff-Id'];
        }
        
        // If section ID is not provided but staff ID is, try to get it automatically
        if (is_null($this->id) && !is_null($this->staffId) && $this->staffId > 0) {
            require_once __DIR__ . '/../models/teacherSectionAdvisersModel.php';
            $sectionAdviserModel = new teacherSectionAdvisersModel();
            $adviserData = $sectionAdviserModel->checkIfAdviser($this->staffId);
            if ($adviserData && isset($adviserData['Section_Id'])) {
                $this->id = (int)$adviserData['Section_Id'];
            }
        }
    }
    public function displayAdvisoryPage() {
        // Check if section ID is provided
        if (is_null($this->id) || $this->id <= 0) {
            echo '<div class="advisory-content"> 
                    <p>Section ID not provided. Please select an advisory section.</p>
                </div>';
            return;
        }
        
        // Check if staff ID is provided
        if (is_null($this->staffId) || $this->staffId <= 0) {
            echo '<div class="advisory-content"> 
                    <p>Staff ID not found. Please log in again.</p>
                </div>';
            return;
        }
        
        $isAdviser = $this->advisoryController->viewCheckIfAdvisory($this->staffId, $this->id);
        if(!$isAdviser['success']) {
            echo '<div class="advisory-content"> 
                    <p>'.htmlspecialchars($isAdviser['message']).'</p>
                </div>';
        }
        else {
            echo '<div class="advisory-header">';
            echo '<div class="advisory-name-wrapper"><h1 class="advisory-name">'.$this->returnSectionName().'</h1></div>';
            echo '<div class="advisory-button-wrapper"> <a href="masterlist.php?section='.$this->id.'">Generate Master list </a> </div>';
            echo '</div>';
            echo '<div class="students-wrapper">
                <h1> Students List </h1>
                <div class="students-list-wrapper">
                '.$this->returnAdvisoryStudents().'
                </div>
            </div>';
            echo '<div class="subjects-wrapper">
                <h1> Section Subjects </h1>'
                .$this->returnSectionSubjects().
            '</div>';
        }
    }
    public function returnAdvisoryStudents() {
        if (is_null($this->id) || $this->id <= 0) {
            return '<div class="error-message">Section ID not provided.</div>';
        }
        
        $students = $this->advisoryController->viewSectionStudents($this->id);
        $html = '';
        if(!$students['success']) {
            $html = '<div class="error-message">' . htmlspecialchars($students['message']) . '</div>';
        }
        else {
            $html = '<table class="students-list">';
            $html .= $this->tableTemplate->returnHorizontalTitles( ['Student Name', 'Action'],'students-title');
            $html .= '<tbody>';
            foreach($students['data'] as $index =>$student) {
                $lastName =  htmlspecialchars($student['Last_Name'] ?? '');
                $firstName = htmlspecialchars($student['First_Name'] ?? '');
                $middleName =  htmlspecialchars($student['Middle_Name'] ?? '');
                $fullName = new safeHTML('<span>'. ($index+1) . '. </span>' . $lastName .', '. $firstName .' '. $middleName);
                $button = new safeHTML('<div class="view-student-button-wrapper"><button class="view-student-button" data-id="'. $student['Student_Id'] .'">View Information</button></div>');
                $isNotNullName = (!empty($lastName) || !empty($firstName)) ? $fullName : '';
                $html .= $this->tableTemplate->returnHorizontalRows([
                    $isNotNullName, $button],'students-data');
            }
            $html .= '</tbody></table>';
        }
        return $html;
    }
    public function returnSectionName() {
        try {
            if (is_null($this->id) || $this->id <= 0) {
                return 'Section ID not provided';
            }
            
            $sectionName = $this->advisoryController->viewSectionName($this->id);

            if (!$sectionName['success']) {
                return htmlspecialchars($sectionName['message']);
            }
            else {
                return htmlspecialchars($sectionName['data']['Grade_Level']) . ' - '.htmlspecialchars($sectionName['data']['Section_Name']);
            }
        }
        catch(Exception  $e) {
            return 'Something went wrong. Please try again Later.';
        }
    }
    public function returnSectionSubjects() {
        if (is_null($this->id) || $this->id <= 0) {
            return '<div class="error-message">Section ID not provided.</div>';
        }
        
        $html = '';
        $subjects = $this->advisoryController->viewSectionSubjects($this->id);
        if(!$subjects['success']) {
            $html = htmlspecialchars($subjects['message']);
        }
        else {
            $html = '<table class="subjects-list">';
            $html .= $this->tableTemplate->returnHorizontalTitles( ['Subject Name'],'subjects-header');
            $html .= '<tbody>';
            foreach($subjects['data'] as $rows) {
                $subjectName = !empty($rows['Subject_Name']) ? $rows['Subject_Name'] : 'No subjects name';
                $html .= $this->tableTemplate->returnHorizontalRows([$subjectName],'subjects-data');
            }
            $html .= '</tbody></table>';
        }
        return $html;
    }
}