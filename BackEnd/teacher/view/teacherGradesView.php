<?php
declare(strict_types=1);
require_once __DIR__ . '/../controller/teacherGradesController.php';
require_once __DIR__ . '/../../Exceptions/IdNotFoundException.php';
require_once __DIR__ . '/../../core/tableDataTemplate.php';
require_once __DIR__ . '/../../core/safeHTML.php';

class teacherGradesView {
    protected $controller;
    protected $staffId;
    protected $tableTemplate;

    public function __construct() {
        if(isset($_SESSION['Staff']['Staff-Id'])) $this->staffId = (int) $_SESSION['Staff']['Staff-Id'];
        $this->controller = new teacherGradesController();
        $this->tableTemplate = new TableCreator();
    }

    public function displaySubjectsToGrade() {
        try {
            if(empty($this->staffId)) {
                throw new IdNotFoundException('Staff ID not found',0);
            }
            $data = $this->controller->viewSubjectsToGrade($this->staffId);
            if(!$data['success']) {
                echo '<div class="error-message">'.htmlspecialchars($data['message']). '</div>';
            }
            else {
                echo '<table class="subjects-to-grade">';
                echo $this->tableTemplate->returnHorizontalTitles([
                    'Subject Name', 'Section Name', 'Students Count', 'Display Students'
                ],'subjects-to-grade-header');
                echo '<tbody>';
                foreach($data['data'] as $rows) {
                    $subjectName = !empty($rows['Subject_Name']) ? $rows['Subject_Name'] : 'No Subject name';
                    $sectionName = !empty($rows['Section_Name']) ? $rows['Section_Name'] : 'No section name';
                    $button = new safeHTML('<div class="grade-button-wrapper"><button id="grade-button" data-id="'.$rows['Section_Subjects_Id'].'"> Grade </button></div>');
                    echo $this->tableTemplate->returnHorizontalRows([
                        $subjectName, $sectionName, $rows['Student_Count'], $button
                    ],'subjects-to-grade-data');
                }
                echo '</tbody></table>';
            }
        }
        catch(IdNotFoundException $e) {
            echo '<div class="error-message">'. $e->getMessage(). '</div>';
        }
        catch(Exception $e) {
            echo '<div class="error-message">'. $e->getMessage(). '</div>';
        }
    }
}