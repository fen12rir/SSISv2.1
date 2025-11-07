<?php
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}
ob_start();
require_once __DIR__ . '/../../../BackEnd/teacher/view/teacherSubjectsHandledView.php';
$view = new teacherSubjectsHandledView();
$pageTitle = 'Subjects Handled';
$pageCss = '<link rel="stylesheet" href="../../assets/css/teacher/teacher-subjects-handled.css">';
$pageJs = '<script src="../../assets/js/teacher/teacher-subjects-handled.js" defer></script>';

?>

<div class="teacher-subjects-handled-content">
    <div class="subjects-handled-wrapper">
        <div class="subjects-handled-header">
            <h1>Your Subjects Handled</h1>
        </div>
        <div class="subjects-list-wrapper">
            <?php $view->displaySubjects(); ?>
        </div>
    </div>
</div>
<?php
    $pageContent = ob_get_clean();
    require_once __DIR__ . '/./teacher_base_designs.php';
?>