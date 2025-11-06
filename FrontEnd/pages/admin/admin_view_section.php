<?php 

ob_start();

$pageTitle = 'Admin view section';
$pageCss = '<link rel="stylesheet" href="../../assets/css/admin/admin-view-section.css">';
$pageJs = '<script type="module" src="../../assets/js/admin/admin-view-section.js" defer></script>';
require_once __DIR__ . '/../../../BackEnd/admin/view/adminViewSectionView.php';
$adminViewSectionView = new adminViewSectionView();
?>

<div class="admin-view-section-content">
    <div class="section-header">
        <div class="header-left">
            <h1 class="section-title"><?php $adminViewSectionView->displaySectionName(); ?></h1>
            <p class="section-adviser">Adviser: <?php $adminViewSectionView->displayAdviserName(); ?></p>
        </div>
        <div class="header-actions">
            <button id="edit-section-btn" class="edit-section-btn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
                Edit Section Details
            </button>
            <a href="admin_grade_levels.php" class="back-btn">Back</a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="stats-section">
        <?php $adminViewSectionView->displaySectionStats(); ?>
    </div>

    <!-- Main Content - Two Column Layout -->
    <div class="main-content-grid">
        <!-- Left Panel - Students -->
        <div class="students-panel">
            <div class="panel-header">
                <h2 class="panel-title">Students</h2>
            </div>
            <div class="panel-content">
                <?php $adminViewSectionView->displayAllStudentsTable(); ?>
            </div>
        </div>

        <!-- Right Panel - Subjects -->
        <div class="subjects-panel">
            <div class="panel-header">
                <h2 class="panel-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    </svg>
                    Subjects
                </h2>
            </div>
            <div class="panel-content">
                <?php $adminViewSectionView->displaySubjectDetails(); ?>
            </div>
        </div>
    </div>

    <div class="modal" id="admin-view-section-edit-modal">
        <div class="modal-content" id="admin-view-section-edit-content"></div>
    </div>
</div>

<?php
$pageContent = ob_get_clean();
require_once __DIR__ . '/./admin_base_designs.php';
?>
