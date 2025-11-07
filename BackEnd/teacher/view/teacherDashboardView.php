<?php
declare(strict_types=1);
require_once __DIR__ . '/../controller/teacherDashboardController.php';

class teacherDashboardView {
    protected $controller;
    protected $staffId;

    public function __construct() {
        if(isset($_SESSION['Staff']['Staff-Id'])) {
            $this->staffId = (int) $_SESSION['Staff']['Staff-Id'];
        }
        $this->controller = new teacherDashboardController();
    }

    public function displayDashboardStats(): void {
        if(empty($this->staffId)) {
            echo '<div class="error-message">Staff ID not found</div>';
            return;
        }

        $data = $this->controller->getDashboardData($this->staffId);
        
        if(!$data['success']) {
            echo '<div class="error-message">' . htmlspecialchars($data['message']) . '</div>';
            return;
        }

        $stats = $data['data'];
        ?>
        <div class="dashboard-stats-grid">
            <a href="./teacher_subjects_handled.php" class="stat-card">
                <div class="stat-card-content">
                    <h2 class="stat-title">Subjects Handled</h2>
                    <span class="stat-value"><?php echo htmlspecialchars((string)$stats['subjects_count']); ?></span>
                </div>
                <img src="../../assets/imgs/subjects-logo.png" alt="Subjects" class="stat-icon">
            </a>
            <a href="./teacher_grades.php" class="stat-card">
                <div class="stat-card-content">
                    <h2 class="stat-title">Students to Grade</h2>
                    <span class="stat-value"><?php echo htmlspecialchars((string)$stats['students_count']); ?></span>
                </div>
                <img src="../../assets/imgs/a-plus.png" alt="Students" class="stat-icon">
            </a>
            <a href="./teacher_locker.php" class="stat-card">
                <div class="stat-card-content">
                    <h2 class="stat-title">Locker Files</h2>
                    <span class="stat-value"><?php echo htmlspecialchars((string)$stats['locker_files_count']); ?></span>
                </div>
                <img src="../../assets/imgs/subjects-logo.png" alt="Locker" class="stat-icon">
            </a>
            <?php if($stats['is_adviser'] && !empty($stats['advisory_section_id'])): ?>
            <a href="./teacher_advisory.php?adv_id=<?php echo htmlspecialchars((string)$stats['advisory_section_id']); ?>" class="stat-card">
                <div class="stat-card-content">
                    <h2 class="stat-title">Advisory Class</h2>
                    <span class="stat-value">Active</span>
                </div>
                <img src="../../assets/imgs/door-open.png" alt="Advisory" class="stat-icon">
            </a>
            <?php endif; ?>
        </div>
        <?php
    }
}

