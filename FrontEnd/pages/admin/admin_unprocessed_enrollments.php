<?php
ob_start();
$pageTitle = "Admin Unhandled Enrollments";
$pageJs = '<script type="module" src="../../assets/js/admin/admin-unhandled-enrollments.js" defer></script>';
$pageCss = '<link rel="stylesheet" href="../../assets/css/admin/admin-unprocessed-enrollments.css">';
    require_once __DIR__ . '/../../../BackEnd/admin/view/adminUnprocessedEnrollmentsView.php';
    $view = new adminUnhandledEnrollmentsView();
?>
    <!--START OF THE MAIN CONTENT-->


<div class="denied-followup-content">
    <div class="header-left">
        <h2  id="enrolledTitle" style="background: #54cc25ff;"> Enrolled </h2>
    </div>
    <div class="table">
        <?php 
            $view->displayEnrolledTransactions();
        ?>
    </div>
    <div class="header-left">
        <h2  id="enrolledTitle" style="background: #f4bb36ff;"> Followed Up </h2>
    </div>
    <div class="table">
        <?php 
            $view->displayFollowUpTransactions();
        ?>
    </div>
    <div class="header-left">

        <h2 id="enrolledTitle" style="background: #f44336;"> Denied </h2>
    </div>
    <div class="table">
        <?php 
            $view->displayDeniedTransactions();
        ?>
    </div>
    <div id="enrolleeModal" class="modal">
        <div class="modal-content" id="enrollee-modal-content"></div>
    </div>
</div>
<?php 
$pageContent = ob_get_clean();
require_once __DIR__ . '/./admin_base_designs.php';
?>
