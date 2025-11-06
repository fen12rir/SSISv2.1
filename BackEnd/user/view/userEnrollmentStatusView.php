<?php
declare(strict_types =1);
require_once __DIR__ . '/../controllers/userEnrolleesController.php';
require_once __DIR__ . '/../../Exceptions/IdNOtFoundException.php';

class userEnrollmentStatusView {
    protected $userId;
    protected $controller;
    protected $enrolleeId;
    public const ENROLLED = 1;
    public const DENIED = 2;
    public const PENDING = 3;
    public const FOR_FOLLOW_UP = 4;

    public function __construct() {
        $this->controller = new userEnrolleesController();
        $this->userId = isset($_SESSION['User']['User-Id']) ? (int)$_SESSION['User']['User-Id'] : null;
        $this->enrolleeId = isset($_GET['id']) ? (int)$_GET['id'] : null;
    }
    private function stringEquivalent(int $status): string {
        return match ($status) {
            3 => 'pending',
            1 => 'enrolled',
            2 => 'denied',
            4 => 'follow-up',
            default => 'unknown',
        };
    }
    public function displayStatus() {
        try {
            if (is_null($this->userId)) {
                throw new IdNotFoundException('User ID not found');
            }
            if(is_null($this->enrolleeId)) {
                throw new IdNotFoundException('Enrollee ID not found');
            }
            $response = $this->controller->viewUserEnrollmentStatus($this->userId, $this->enrolleeId);
            if(!$response['success']) {
                echo '<div class="error-message">' . htmlspecialchars($response['message']).'</div>';
            }
            else if($response['success'] && empty($response['data'])) {
                echo '<div>' .htmlspecialchars($response['message']).'</div>';
            }
            else {
                $statusCode = $response['enrollment_status'];
                if ($statusCode === 1) { //display status for enrolled
                    $status = $this->stringEquivalent($statusCode);
                    echo "<p class=status>" .strtoupper($status) . "</p>";
                    echo "<p> SUCCESSFULLY ENROLLED! CHECK THE STUDENTS LIST TO VIEW THIS STUDENT'S DETAILS. </p>";
                }
                else if ($statusCode === 2) { //display status for denied
                    $status = $this->stringEquivalent($statusCode);
                    echo "<p class=status>" .strtoupper($status) . "</p>";
                    echo '<div>';
                    echo '<p class="transaction-code"><strong>Transaction Code:</strong> ' . htmlspecialchars($response['data']['Transaction_Code']) . '</p>';
                    echo '<p class="transaction-description"><strong>Description:</strong> ' . htmlspecialchars($response['data']['Remarks']) . '</p>';
                    echo '</div>';
                    echo "<p> Your enrollment form is DENIED. Please contact the school for more information. </p>";
                }
                else if ($statusCode === 3) { //display status for pending
                    $status = $this->stringEquivalent($statusCode);
                    echo "<p class=status>" .strtoupper($status) . "</p>";
                    echo "<p> Your enrollment form is currently being processed. Please wait for 3-4 working days <p>";
                }
                else  { // display for follow up
                    $status = $this->stringEquivalent($statusCode);
                    echo "<p class=status>" .strtoupper($status) . "</p>";
                    echo '<div class="reasons-container">';
                    echo '<p  class="transaction-code"><strong>Transaction Code:</strong> ' . htmlspecialchars($response['data']['Transaction_Code']) . '</p>';
                    echo '<p><strong>Description:</strong> ' . htmlspecialchars($response['data']['Remarks']) . '</p>';
                    echo '</div>';
                    if ($response['data']['Transaction_Status'] === 1) { // allow editing of enrollment for if allowed by admin
                        echo "<button class='edit-enrollment-form' data-id=". $this->enrolleeId ."> Edit Enrollment Form</button>";
                    }
                    else if($response['data']['Transaction_Status'] === 2) {//request meeting
                        echo "<p> Your enrollment form is in need of further discussion. Please wait for the school to contact you. </p>";
                    }
                    //Transaction status === 3 {Disable edit enrollment form. Means already resubmitted}
                } 
            }
        }
        catch(IdNotFoundException $e) {
            echo '<div class="error-message">'.htmlspecialchars($e->getMessage()).'</div>';
        }
        catch(Exception $e) {
            echo '<div class="error-message">'.htmlspecialchars($e->getMessage()).'</div>';
        }
    }
}

