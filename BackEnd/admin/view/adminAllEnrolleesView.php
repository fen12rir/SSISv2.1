<?php
declare(strict_types=1);
require_once __DIR__ . '/../controller/adminEnrolleesController.php';
require_once __DIR__ . '/../../core/tableDataTemplate.php';
require_once __DIR__ . '/../../core/safeHTML.php';
class adminAllEnrolleesView {
    protected $tableTemplate;
    protected $controller;
    public const ENROLLED = 1;
    public const DENIED = 2;
    public const PENDING = 3;
    public const TO_FOLLOW = 4;

    private function stringEquivalent(int $value): string {
       switch($value) {
            case self::ENROLLED:
                return "enrolled";
            case self::DENIED:
                return "denied";
            case self::PENDING:
                return "pending";
            case self::TO_FOLLOW:
                return "to-follow";
            default:
                return "unknown";
        }
    }
    public function __construct() {
        $this->controller = new adminEnrolleesController();
        $this->tableTemplate = new TableCreator();
    }
    public function displayCount() {
        try {
            $data = $this->controller->viewTransactionsCount();
            if(!$data['success']) {
                echo '<div class="error-message">' .htmlspecialchars($data['message']).'</div>';
                return;
            }
            else {
                echo $data['data'];
            }
        }
        catch(Exception $e) {
            echo '<div class="error-message"> There was an unexpected problem </div>';
        }
    }
    public function displayAllTransactions() {
        try {
            $data = $this->controller->viewAllEnrollmentTransactions();
            if(!$data['success'] || empty($data['data'])) {
                echo '<div class="error-message">' .htmlspecialchars($data['message']).'</div>';
            }else {
                echo '<table>';
                echo $this->tableTemplate->returnHorizontalTitles([
                'Student Name', 'Transaction Code', 'Handled By', 'Handled At','Enrollment Status Given', 
                'Transaction Status', 'View Info'
                ],'enrollees-title');
                echo '<tbody>';
                foreach($data['data'] as $rows) {   
                //Student name
                $firstName = !empty($rows['Student_First_Name']) ? $rows['Student_First_Name'] : 'No First name found';
                $lastName = !empty($rows['Student_Last_Name']) ? $rows['Student_Last_Name'] : 'No Last name found';
                $middleName = !empty($rows['Student_Middle_Name']) ? $rows['Student_Middle_Name'] : '';
                $studentFull = (!empty($firstName) && !empty($lastName)) 
                ? $lastName . ', '.$firstName . ' ' . $middleName : 'Name is empty';
                $transactionCode = $rows['Transaction_Code'];
                //Staff name
                $staffFirstName = !empty($rows['Staff_First_Name']) ? $rows['Staff_First_Name'] : 'No First name found';
                $staffLastName = !empty($rows['Staff_Last_Name']) ? $rows['Staff_Last_Name'] : 'No Last name found';
                $staffMiddleName = !empty($rows['Staff_Middle_Name']) ? $rows['Staff_Middle_Name'] : '';
                $staffFullName = (!empty($staffFirstName) && !empty($staffLastName)) 
                ? $staffLastName . ', '.$staffFirstName . ' ' . $staffMiddleName : 'Name is empty';
                $handledAt = $rows['Created_At'];
                $transactionStatus = ($rows['Is_Approved'] === 0) ? 'Unprocessed' : 'Finalized';
                $givenStatus = strtoupper($this->stringEquivalent((int)$rows['Enrollment_Status']));
                $button = new safeHTML('<button class="view-enrollee">View</button>');
                
                echo $this->tableTemplate->returnHorizontalRows([
                    $studentFull, $transactionCode, $staffFullName, $handledAt, $givenStatus, $transactionStatus, $button
                ],'enrollee-row');
            }
            echo '</tbody></table>';
        }
    }
    catch(Throwable $t) {
        echo '<div class="error-message">There was a syntax problem. Please wait for it to be fixed</div>';
    }
    }
} 
