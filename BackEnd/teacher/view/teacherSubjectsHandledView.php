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
                $stringEqual = 'Monday';
                break;
            case 2:
                $stringEqual = 'Tuesday';
                break;
            case 3: 
                $stringEqual = 'Wednesday';
                break;
            case 4: 
                $stringEqual = 'Thursday';
                break;
            case 5: 
                $stringEqual = 'Friday';
                break;
            case 6: 
                $stringEqual = 'Saturday';
                break;
            case 7: 
                $stringEqual = 'Sunday';
                break;
        }
        return $stringEqual;
    }
    
    private function formatTime(?string $timeStart, ?string $timeEnd) : string {
        if (empty($timeStart) || empty($timeEnd)) {
            return 'No Scheduled time yet';
        }
        // Format time to be more readable (e.g., "08:00:00" -> "8:00 AM")
        $start = date('g:i A', strtotime($timeStart));
        $end = date('g:i A', strtotime($timeEnd));
        return $start . ' - ' . $end;
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
                        $time = $this->formatTime($rows['Time_Start'] ?? null, $rows['Time_End'] ?? null);
                        $sectionSubjectId = !empty($rows['Section_Subjects_Id']) ? (int)$rows['Section_Subjects_Id'] : 0;
                        $gradeLink = 'teacher_grades.php';
                        $button = new safeHTML('<div class="action-button-wrapper"><a href="' . htmlspecialchars($gradeLink) . '" class="grade-students-btn">Grade Students</a></div>');
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