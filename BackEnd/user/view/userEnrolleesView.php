<?php
declare(strict_types =1);
require_once __DIR__ . '/../controller/userEnrolleesController.php';
require_once __DIR__ . '/../../Exceptions/IdNotFoundException.php';
require_once __DIR__ . '/../../core/tableDataTemplate.php';
require_once __DIR__ . '/../../core/safeHTML.php';
class displayEnrollmentForms {
    protected $controller;
    protected $userId;
    protected $tableTemplate;

    public function __construct() {
        $this->controller = new userEnrolleesController();
        $this->userId = isset($_SESSION['User']['User-Id']) ? (int)$_SESSION['User']['User-Id'] : null;
        $this->tableTemplate = new tableCreator();
    }
    public function displaySubmittedForms() {
        try {
            if(empty($this->userId)) {
                throw new IdNotFoundException('User Id is empty');
            }
            $data = $this->controller->viewUserEnrollees($this->userId);
            if(!$data['success']) {
                echo '<div class="error-message">' .htmlspecialchars($data['message']). '</div>';
            }
            else if($data['success'] && empty($data['data'])) {
                echo '<div class="error-message">'.htmlspecialchars($data['message']).'<a href="./user_enrollment_form.php">Submit a form?</a></div>';
            }
            else {
                echo '<link rel="stylesheet" href="../../assets/css/user/user-enrollees-modal.css">';
                echo '<table> <tbody>';
                foreach($data['data'] as $rows) {
                    $fname = !empty($rows['Student_First_Name']) ? $rows['Student_First_Name'] : 'No first name';
                    $lname = !empty($rows['Student_Last_Name']) ? $rows['Student_Last_Name'] : 'No last name';
                    $studentMiddleInitial = !empty($rows['Student_Middle_Name']) ? substr($rows['Student_Middle_Name'], 0, 1) . "." : "";
                    //values to render
                    $fullName = $lname . ', ' . $fname . ' ' . $studentMiddleInitial;
                    $button = new safeHTML('<a class= "Check-Status" href="../user/user_enrollment_status.php?id='. $rows['Enrollee_Id'] .'"> Check Status </a>');
                    echo $this->tableTemplate->returnHorizontalRows([
                        $fullName, $button
                    ],'user-enrollees-data');
                }      
                echo '</tbody></table>';
                
                // Add modal structure
                echo '<div class="modal" id="enrollmentStatusModal">
                        <div class="modal-content">
                            <span class="close-modal">&times;</span>
                            <div id="modal-body-content"></div>
                        </div>
                      </div>';
            }
        }
        catch(IdNotFoundException $e) {
            echo '<div class="error-message">' .htmlspecialchars($e->getMessage()).'</div>';
        }
        catch (Exception $e) {
            echo '<div class="error-message">'.htmlspecialchars($e->getMessage()).'</div>';
        }
    }
}

