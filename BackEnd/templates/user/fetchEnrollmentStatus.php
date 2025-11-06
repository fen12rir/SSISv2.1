<?php
declare(strict_types=1);
require_once __DIR__ . '/../../user/models/userEnrolleesModel.php';

if (!isset($_GET['enrollee_id']) || !isset($_GET['user_id'])) {
    echo '<div class="error-message">Invalid request parameters</div>';
    exit;
}

$enrolleeId = (int)$_GET['enrollee_id'];
$userId = (int)$_GET['user_id'];

$model = new userEnrolleesModel();

try {
    $enrollmentStatus = $model->getUserStatus($userId, $enrolleeId);
    $transactionData = $model->sendTransactionStatus($enrolleeId);
    $enrollmentInfo = $model->getEnrollmentInformation($enrolleeId);
    $statusLabels = [
        1 => 'Enrolled',
        2 => 'Denied',
        3 => 'Pending',
        4 => 'Follow-Up'
    ];
    
    $statusClass = [
        1 => 'status-approved',
        2 => 'status-rejected',
        3 => 'status-pending',
        4 => 'status-review'
    ];
    
    $statusText = strtoupper($statusLabels[$enrollmentStatus] ?? 'Unknown');
    $statusCssClass = $statusClass[$enrollmentStatus] ?? '';
    
    if (!empty($enrollmentInfo)) {
        $info = $enrollmentInfo[0];
        $studentName = htmlspecialchars(($info['Student_First_Name'] ?? '') . ' ' . ($info['Student_Middle_Name'] ?? '') . ' ' . ($info['Student_Last_Name'] ?? ''));
        $lrn = htmlspecialchars((string)($info['Learner_Reference_Number'] ?? 'N/A'));
        $gradeLevel = htmlspecialchars((string)($info['E_Grade_Level'] ?? 'N/A'));
    }
?>

<div class="enrollment-status-content">
    <div class="status-header">
        <h3>Enrollment Status Details</h3>
    </div>
    
    <div class="status-body">
        <div class="info-row">
            <span class="info-label">Student Name:</span>
            <span class="info-value"><?php echo $studentName ?? 'N/A'; ?></span>
        </div>
        
        <div class="info-row">
            <span class="info-label">LRN:</span>
            <span class="info-value"><?php echo $lrn ?? 'N/A'; ?></span>
        </div>
        
        <div class="info-row">
            <span class="info-label">Grade Level:</span>
            <span class="info-value"><?php echo $gradeLevel ?? 'N/A'; ?></span>
        </div>
        
        <div class="info-row">
            <span class="info-label">Status:</span>
            <span class="info-value <?php echo $statusCssClass; ?>"><?php echo $statusText; ?></span>
        </div>
        
        <?php if ($enrollmentStatus === 1): ?>
            <div class="status-message success-message">
                <p>SUCCESSFULLY ENROLLED!</p>
            </div>
        <?php elseif ($enrollmentStatus === 2): ?>
            <?php if ($transactionData): ?>
                <div class="info-row">
                    <span class="info-label">Transaction Code:</span>
                    <span class="info-value"><?php echo htmlspecialchars((string)($transactionData['Transaction_Code'] ?? 'N/A')); ?></span>
                </div>
                <?php if (!empty($transactionData['Remarks'])): ?>
                <div class="info-row">
                    <span class="info-label">Remarks:</span>
                    <span class="info-value"><?php echo htmlspecialchars((string)$transactionData['Remarks']); ?></span>
                </div>
                <?php endif; ?>
            <?php endif; ?>
            <div class="status-message error-message">
                <p>Your enrollment form is DENIED. Please contact the school for more information.</p>
            </div>
        <?php elseif ($enrollmentStatus === 3): ?>
            <div class="status-message info-message">
                <p>Your enrollment form is currently being processed. Please wait for 3-4 working days.</p>
            </div>
        <?php elseif ($enrollmentStatus === 4): ?>
            <?php if ($transactionData): ?>
                <div class="info-row">
                    <span class="info-label">Transaction Code:</span>
                    <span class="info-value"><?php echo htmlspecialchars((string)($transactionData['Transaction_Code'] ?? 'N/A')); ?></span>
                </div>
                <?php if (!empty($transactionData['Remarks'])): ?>
                <div class="info-row">
                    <span class="info-label">Remarks:</span>
                    <span class="info-value"><?php echo htmlspecialchars((string)$transactionData['Remarks']); ?></span>
                </div>
                <?php endif; ?>
                
                <?php 
                $canResubmit = isset($transactionData['Can_Resubmit']) ? (int)$transactionData['Can_Resubmit'] : 0;
                $needConsultation = isset($transactionData['Need_Consultation']) ? (int)$transactionData['Need_Consultation'] : 0;
                ?>
                
                <?php if ($canResubmit === 1): ?>
                    <div class="status-message info-message">
                        <button class="edit-enrollment-form" data-id="<?php echo $enrolleeId; ?>" data-ajax="true">Edit Enrollment Form</button>
                        <p class="resubmit-note">Note: You can only resubmit the form once</p>
                    </div>
                <?php elseif ($needConsultation === 1): ?>
                    <div class="status-message info-message">
                        <p>Your enrollment form is in need of further discussion. Please wait for the school to contact you.</p>
                    </div>
                <?php else: ?>
                    <div class="status-message info-message">
                        <button class="edit-enrollment-form" data-id="<?php echo $enrolleeId; ?>" data-ajax="true">Edit Enrollment Form</button>
                        <p class="resubmit-note">Note: You can only resubmit the form once</p>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php
} catch (Exception $e) {
    echo '<div class="error-message">Error loading enrollment status: ' . htmlspecialchars($e->getMessage()) . '</div>';
}
?>
