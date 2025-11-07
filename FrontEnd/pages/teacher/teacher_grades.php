<?php
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}
ob_start();
require_once __DIR__ . '/../../../BackEnd/teacher/view/teacherGradesView.php';
$view = new teacherGradesView();
$pageTitle = 'Grade Students';
$pageJs = '<script type="module" src="../../assets/js/teacher/teacher-grades.js" defer></script>';
$pageCss = '<link rel="stylesheet" href="../../assets/css/teacher/teacher-grades.css">';
?>
<div class="teacher-grades-content">
    <div class="grades-wrapper">
        <div class="grades-header">
            <h1>Your subjects to grade</h1>
        </div>
        <div class="subjects-to-grade-wrapper">
            <?php 
                $view->displaySubjectsToGrade();
            ?>
        </div>
    </div>
    
    <!-- Modal Structure -->
    <div class="modal" style="display: none;">
        <div class="modal-content">
        </div>
    </div>
</div>
<?php
    $pageContent = ob_get_clean();
    require_once __DIR__ . '/./teacher_base_designs.php';
?>