<?php 
ob_start();
$pageTitle = "Admin Grade Levels";
$pageCss = '<link rel="stylesheet" href="../../assets/css/admin/admin-grade-levels.css">'; 
$pageJs= '<script type="module" src="../../assets/js/admin/admin-grade-levels.js" defer></script>';
    
?>

<div class="admin-grade-levels-content">
    <div class="grade-levels-title-wrapper"> 
        <h1 class="page-title">Grade Levels</h1>
        <button class="submit-btn" id="add-section-btn">Add Section</button>
    </div>
    
    <div class="grade-levels-container">
        <div id="loading-state" class="loading-container">
            <div class="spinner"></div>
            <p>Loading grade levels...</p>
        </div>
        
        <div id="grade-levels-list" class="grade-levels-list">
            <!-- Grade levels will be dynamically inserted here -->
        </div>
        
        <div id="empty-state" class="empty-state" style="display: none;">
            <p>No grade levels found.</p>
        </div>
    </div>
    
    <div id="add-section-modal" class="modal">
        <div class="modal-content"></div>
    </div>
</div>

<?php
$pageContent = ob_get_clean();
require_once __DIR__ . '/./admin_base_designs.php';
?>

