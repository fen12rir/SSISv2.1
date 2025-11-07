<?php
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}
ob_start();
require_once __DIR__ . '/../../../BackEnd/teacher/view/teacherDashboardView.php';
$view = new teacherDashboardView();
$pageTitle = 'Dashboard';
$pageCss = '<link rel="stylesheet" href="../../assets/css/teacher/teacher-dashboard.css">';
$pageJs = '<script src="../../assets/js/teacher/teacher-dashboard.js" defer></script>';
?>
<div class="teacher-dashboard-content">
    <div class="dashboard-wrapper">
        <div class="dashboard-header">
            <h1 class="dashboard-title">Teacher Dashboard</h1>
            <p class="dashboard-subtitle">Welcome back! Here's an overview of your activities.</p>
        </div>
        <div class="dashboard-stats-container">
            <?php $view->displayDashboardStats(); ?>
        </div>
    </div>
</div>
<?php
$pageContent = ob_get_clean();
require_once __DIR__ . '/./teacher_base_designs.php';
?>