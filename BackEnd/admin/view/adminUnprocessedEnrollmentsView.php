<?php
declare(strict_types=1);
require_once __DIR__ . '/../controller/adminUnprocessedEnrollmentsController.php';
require_once __DIR__ . '/../../core/tableDataTemplate.php';
require_once __DIR__ . '/../../core/safeHTML.php';
class adminUnhandledEnrollmentsView {
    protected $tableTemplate;
    protected $transactionsController;
    //ENROLLMENT STATUSES
    private const ENROLLED = 1;
    private const DENIED = 2;
    private const FOLLOWUP = 4;
    //TRANSACTION STATUSES
    private const UNPROCESSED = 0;
    private const FOR_RESUBMISSION = 1;
    private const FOR_CONSULTATION = 2;
    public function __construct() {
        $this->tableTemplate = new tableCreator();
        $this->transactionsController = new adminUnprocessedEnrollmentsController();
    }
    private function transactionValue(int $value):string {
        $statuses = [
            self::UNPROCESSED => 'Unprocessed',
            self::FOR_RESUBMISSION => 'For resubmission',
            self::FOR_CONSULTATION => 'For consultation',
        ];
        return $statuses[$value] ?? 'Unknown status';
    }
    private function stringEquivalent(int $value): string {
        switch($value) {
            case self::ENROLLED:
                return "enrolled";
            case self::DENIED:
                return "denied";
            case self::FOLLOWUP:
                return "to-follow";
            default:
                return "Unknown";
        }
    }
    public function displayEnrolledTransactions() {
        try {
            $data = $this->transactionsController->viewMarkedEnrolledTransactions();
            if(!$data['success']) {
                echo '<div class="error-message"><span>'.htmlspecialchars($data['message']). '</span></div>';
            }
            else {
                echo '<table class="enrollments">';
                echo $this->tableTemplate->returnHorizontalTitles(['LRN', 'Enrollee Name', 'Handled By', 'Transaction Code','Enrollment Status Given'
                ,'Handled At','Transaction Status','Remarks'],'enrolled-transaction-titles');
                //RENDER TABLE
                foreach($data['data'] as $row) {
                    $transactionNum = (int)$row['Transaction_Status'];
                    $lrn = !empty($row['Learner_Reference_Number']) ?  $row['Learner_Reference_Number'] :"No LRN";
                    $status = strtoupper($this->stringEquivalent($row['Enrollment_Status']));
                    $viewSubmission = $row['Transaction_Status'] == 1 ? "<button id='" .$row['Enrollee_Id']."' data-id='" .$row['Enrollee_Id']."' class='view-resubmission'>View Resubmission</button>" : "";
                    $studentMiddleInitial = !empty($row['Student_Middle_Name']) ? substr(htmlspecialchars($row['Student_Middle_Name']), 0, 1) . "." : "";
                    $fullName = htmlspecialchars($row['Student_Last_Name']) . ', ' . htmlspecialchars($row['Student_First_Name']) . ' ' .  $studentMiddleInitial;
                    $staffMiddleInitial = !empty($row['Staff_Middle_Name']) ? substr($row['Staff_Middle_Name'], 0, 1) . "." : "";
                    $staffName = htmlspecialchars($row['Staff_Last_Name']) . ', ' . htmlspecialchars($row['Staff_First_Name']) . ' ' . $staffMiddleInitial;
                    $transactionStatus = $transactionNum === 3 
                    ? new safeHTML('<button id="'.$row['Enrollee_Id'].'" data-enrollee="'.$row['Enrollee_Id'].'" class="view-resubmission">View Resubmission</button>')
                    : strtoupper($this->transactionValue((int)$row['Transaction_Status']));
                    $button = new safeHTML('<button id="' .$row['Enrollee_Id'].'" data-enrollee="'.$row['Enrollee_Id'].'" class="view-reason">View Remarks</button>');
                    echo $this->tableTemplate->returnHorizontalRows([$lrn,$fullName,$staffName,$row['Transaction_Code'],$status,$row['Date'],$transactionStatus,$button],'denied-followup-row');
                }
                echo '</tbody></table>';
            }
        }
        catch(Throwable $t) {
            echo '<div class="error-message">There was a syntax error</div>';
        }
    }
    public function displayFollowUpTransactions() {
        try {
            $data = $this->transactionsController->viewMarkedFollowedUpTransactions();
            if(!$data['success']) {
                echo '<div class="error-message"><span>'.htmlspecialchars($data['message']). '</span></div>';
            }
            else {
                echo '<table class="enrollments">';
                echo $this->tableTemplate->returnHorizontalTitles(['LRN', 'Enrollee Name', 'Handled By', 'Transaction Code','Enrollment Status Given'
                ,'Handled At','Transaction Status','Remarks'],'followedup-transaction-titles');
                //RENDER TABLE
                foreach($data['data'] as $row) {
                    $transactionNum = (int)$row['Transaction_Status'];
                    $lrn = !empty($row['Learner_Reference_Number']) ?  $row['Learner_Reference_Number'] :"No LRN";
                    $status = strtoupper($this->stringEquivalent($row['Enrollment_Status']));
                    $viewSubmission = $row['Transaction_Status'] == 1 ? "<button id='" .$row['Enrollee_Id']."' data-id='" .$row['Enrollee_Id']."' class='view-resubmission'>View Resubmission</button>" : "";
                    $studentMiddleInitial = !empty($row['Student_Middle_Name']) ? substr(htmlspecialchars($row['Student_Middle_Name']), 0, 1) . "." : "";
                    $fullName = htmlspecialchars($row['Student_Last_Name']) . ', ' . htmlspecialchars($row['Student_First_Name']) . ' ' .  $studentMiddleInitial;
                    $staffMiddleInitial = !empty($row['Staff_Middle_Name']) ? substr($row['Staff_Middle_Name'], 0, 1) . "." : "";
                    $staffName = htmlspecialchars($row['Staff_Last_Name']) . ', ' . htmlspecialchars($row['Staff_First_Name']) . ' ' . $staffMiddleInitial;
                    $showCpNumber= ($transactionNum === 2 && !empty($row['Contact_Number'])) ? "Number: ".$row['Contact_Number'] : '';
                    $transactionStatus = $transactionNum === 3 
                    ? new safeHTML('<button id="'.$row['Enrollee_Id'].'" data-enrollee="'.$row['Enrollee_Id'].'" class="view-resubmission">View Resubmission</button>')
                    : strtoupper($this->transactionValue((int)$row['Transaction_Status'])) .' '. $showCpNumber;
                    $button = new safeHTML('<button id="'.$row['Enrollee_Id'].'" data-enrollee="'.$row['Enrollee_Id'].'" class="view-reason">View Remarks</button>');
                    echo $this->tableTemplate->returnHorizontalRows([$lrn,$fullName,$staffName,$row['Transaction_Code'],$status,$row['Date'],$transactionStatus,$button],'denied-followup-row');
                }
                echo '</tbody></table>';
            }
        }
        catch(Throwable $t) {
            echo '<div class="error-message">There was a syntax error</div>';
        }
    }
    public function displayDeniedTransactions() {
        try {
            $data = $this->transactionsController->viewMarkedDeniedTransactions();
            if(!$data['success']) {
                echo '<div class="error-message"><span>'.htmlspecialchars($data['message']). '</span></div>';
            }
            else {
                echo '<table class="enrollments">';
                echo $this->tableTemplate->returnHorizontalTitles(['LRN', 'Enrollee Name', 'Handled By', 'Transaction Code','Enrollment Status Given'
                ,'Handled At','Transaction Status','Remarks'],'denied-transaction-titles');
                //TABLE
                foreach($data['data'] as $row) {
                    $lrn = !empty($row['Learner_Reference_Number']) ?  $row['Learner_Reference_Number'] :"No LRN";
                    $status = strtoupper($this->stringEquivalent($row['Enrollment_Status']));
                    $viewSubmission = $row['Transaction_Status'] == 1 ? "<button id='" .$row['Enrollee_Id']."' data-id='" .$row['Enrollee_Id']."' class='view-resubmission'>View Resubmission</button>" : "";
                    $studentMiddleInitial = !empty($row['Student_Middle_Name']) ? substr(htmlspecialchars($row['Student_Middle_Name']), 0, 1) . "." : "";
                    $fullName = htmlspecialchars($row['Student_Last_Name']) . ', ' . htmlspecialchars($row['Student_First_Name']) . ' ' .  $studentMiddleInitial;
                    $staffMiddleInitial = !empty($row['Staff_Middle_Name']) ? substr($row['Staff_Middle_Name'], 0, 1) . "." : "";
                    $staffName = htmlspecialchars($row['Staff_Last_Name']) . ', ' . htmlspecialchars($row['Staff_First_Name']) . ' ' . $staffMiddleInitial;
                    $transactionStatus = strtoupper($this->transactionValue((int)$row['Transaction_Status'], $row['Enrollee_Id']));
                    $button = new safeHTML('<button id="'.$row['Enrollee_Id'].'" data-enrollee="'.$row['Enrollee_Id'].'" class="view-reason">View Remarks</button>');
                    echo $this->tableTemplate->returnHorizontalRows([$lrn,$fullName,$staffName,$row['Transaction_Code'],$status,$row['Date'],$transactionStatus,$button],'denied-followup-row');
                }
                echo '</tbody></table>';
            }
        }
        catch(Throwable $t) {
            echo '<div class="error-message">There was a syntax error</div>';
        }
    }
}
