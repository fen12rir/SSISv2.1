<?php
declare(strict_types=1);
require_once __DIR__ . '/../controller/adminSubjectsController.php';
require_once __DIR__ . '/../../common/getGradeLevels.php';
require_once __DIR__ . '/../../core/tableDataTemplate.php';
require_once __DIR__ . '/../../core/safeHTML.php';

class adminSubjectsView {
    protected $subjectsController;
    protected $getGradeLevels;
    protected $tableTemplate;

    public function __construct() {
        $this->subjectsController = new adminSubjectsController();
        $this->getGradeLevels = new getGradeLevels();
        $this->tableTemplate = new TableCreator();
    }

    public function displaySubjects() {
        try {
            $response = $this->subjectsController->viewSubjectsPerSection();

            if(!$response['success']) {
                echo '<div>' .htmlspecialchars($response['message']).'</div>';
            }
            else {
                echo '<table class="subjects-table">';
                echo $this->tableTemplate->returnHorizontalTitles([
                    'Subject Name', 'Section Name', 'Grade Level', 'Teacher Assigned', 'Actions'
                ],'subject-table-head');
                echo '<tbody>';
                foreach($response['data'] as $rows) {
                    $firstName = !empty($rows['Staff_First_Name']) ? htmlspecialchars($rows['Staff_First_Name']) : '';
                    $lastName = !empty($rows['Staff_Last_Name']) ? htmlspecialchars($rows['Staff_Last_Name']) : '';
                    $middleName = !empty($rows['Staff_Middle_Name']) ? htmlspecialchars($rows['Staff_Middle_Name']) : '';
                    $button = new safeHTML('<button data-id="'.$rows['Section_Subjects_Id'].'" class="assign-teacher"> <img src="../../assets/imgs/edit-white.png" loading="lazy" alt="edit"></button>');
                    
                    $fullName = (!empty($firstName) && !empty($lastName)) ?
                    htmlspecialchars($rows['Staff_Last_Name']) . ', ' . htmlspecialchars($rows['Staff_First_Name']) . ' ' . htmlspecialchars($rows['Staff_Middle_Name']) : 'No Teacher Assigned Yet';

                    echo $this->tableTemplate->returnHorizontalRows([
                        $rows['Subject_Name'],$rows['Section_Name'],$rows['Grade_Level'], $fullName ,$button
                    ],'subject-data');
                }
                echo '</tbody></table>';
            }
        }
        catch(Exception $e) {
            echo $this->tableTemplate->returnHorizontalRows([$e->getMessage()], 'exception-content');
        }
    }
}
