<?php
declare(strict_types=1);
require_once __DIR__ . '/../controller/teacherSubjectsHandledController.php';
require_once __DIR__ . '/../../core/tableDataTemplate.php';
require_once __DIR__ . '/../../core/safeHTML.php';

class teacherSubjectsHandledView {
    protected $staffId;
    protected $subjectsController;
    protected $tableTemplate;

    public function __construct() {
        if(isset($_SESSION['Staff']['Staff-Id'])) $this->staffId = (int) $_SESSION['Staff']['Staff-Id'];
        $this->subjectsController = new teacherSubjectsHandledController(); 
        $this->tableTemplate = new TableCreator();
    }
    private function dayConvertToString(int $day) : string {
        $stringEqual = '';
        switch($day) {
            case 1:
                $stringEqual = 'monday';
                break;
            case 2:
                $stringEqual = 'tuesday';
                break;
            case 3: 
                $stringEqual = 'wednesday';
                break;
            case 4: 
                $stringEqual = 'thursday';
                break;
            case 5: 
                $stringEqual = 'friday';
                break;
            case 6: 
                $stringEqual = 'saturday';
                break;
            case 7: 
                $stringEqual = 'sunday';
                break;
        }
        return $stringEqual;
    }
    public function displaySubjects() { 
        try {
            $subjectData = $this->subjectsController->viewSubjectsHandled($this->staffId);

            if(!$subjectData['success']) {
                echo '<div class="error-message">' .htmlspecialchars($subjectData['message']).'</div>';
            }
            if(empty($subjectData)) {
                echo '<div class="error-message"> No subjects assigned yet. </div>';
            }
            else {
                echo '<table class="subjects-list">';
                    echo $this->tableTemplate->returnHorizontalTitles(['Subject Name', 'Day', 'Time', 'Section Name', 'Action'],'subject-titles');
                    echo '<tbody>';
                    foreach($subjectData['data'] as $rows) {
                        $subjectName = !empty($rows['Subject_Name']) ? $rows['Subject_Name'] : 'No Subject name yet';
                        $sectionName = !empty($rows['Section_Name']) ? $rows['Section_Name'] : 'No Section name yet';
                        $day = !empty($rows['Schedule_Day']) ? $this->dayConvertToString($rows['Schedule_Day']) :  'No Scheduled day yet';
                        $time = (!empty($rows['Time_Start']) && !empty($rows['Time_End'])) ? $rows['Time_Start'] . '-' . $rows['Time_End'] : 'No Scheduled time yet';
                        $button = new safeHTML('<button> Grade Students</button>');
                        echo $this->tableTemplate->returnHorizontalRows( [$subjectName,$day,$time, $sectionName, $button],'subject-details');
                    }
                    echo '</tbody></table>';
            }
        }
        catch(Exception $e) {
            echo '<div class="error-message">'.$e->getMessage().'</div>';
        }

    }
}