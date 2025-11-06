<?php
ob_start(); 
require_once __DIR__ . '/../../../BackEnd/admin/view/adminAllEnrolleesView.php';

$pageTitle = 'SSIS-Admin All Enrollees'; 
$pageCss = '<link rel="stylesheet" href="../../assets/css/admin/admin-all-enrollees.css">';
$pageJs = '<script src="../../assets/js/admin/admin-all-enrollees.js" defer></script>';
$view = new adminAllEnrolleesView();
?>
<div class="admin-all-enrollees-content">
    <div class="enrollment-access">
        <div class="admin-all-enrollees-header">
            <div class="header-left">
                <div class="count-display">
                    Total Enrollees: <span class="count-number">
                    <?php
                        $view->displayCount();
                        ?>
                    </span>
                </div>
            </div>
            <div class="header-right">
                <input type="text" id="search" class="search-box" placeholder="Search by name, LRN, grade level, or status...">
            </div>
        </div>
        <div class="all-enrollees-table">
                <?php      
                $view->displayAllTransactions();
                ?>
        </div>
        <div id="enrolleeModal" class="modal">
            <div class="modal-content">
            </div>
        </div>
    </div>
</div>
</div>  
<?php
$pageContent = ob_get_clean();
require_once __DIR__ . '/./admin_base_designs.php';
?>