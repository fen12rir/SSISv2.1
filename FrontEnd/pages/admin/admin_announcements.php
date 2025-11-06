<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['Staff']['User-Id']) || !isset($_SESSION['Staff']['Staff-Id']) || $_SESSION['Staff']['Staff-Type'] != 1) {
    header("Location: ../../Login.php");
    exit();
}

$pageTitle = "Announcements";
$currentPage = "announcements";
$pageCss = '<link rel="stylesheet" href="../../assets/css/admin/admin-announcements.css">';
$pageJs = '<script type="module" src="../../assets/js/admin/admin-announcements.js" defer></script>';

ob_start();
?>

<div class="announcements-container">
    <div class="announcements-header">
        <h1 class="announcements-title">Announcements Management</h1>
        <button class="add-announcement-btn" id="add-announcement-btn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Add Announcement
        </button>
    </div>

    <!-- Loading State -->
    <div id="loading-state" class="loading-container">
        <div class="spinner"></div>
        <p>Loading announcements...</p>
    </div>

    <!-- Announcements List -->
    <div id="announcements-list" class="announcements-list">
        <!-- Announcements will be dynamically inserted here -->
    </div>

    <!-- Empty State -->
    <div id="empty-state" class="empty-state" style="display: none;">
        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
            <polyline points="14 2 14 8 20 8"></polyline>
            <line x1="16" y1="13" x2="8" y2="13"></line>
            <line x1="16" y1="17" x2="8" y2="17"></line>
        </svg>
        <h3>No announcements yet</h3>
        <p>Create your first announcement to get started</p>
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="announcement-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modal-title">Add Announcement</h2>
            <span class="close" id="close-modal">&times;</span>
        </div>
        <form id="announcement-form" enctype="multipart/form-data">
            <input type="hidden" id="announcement-id" name="announcement_id">
            <input type="hidden" id="remove-image-flag" name="remove_image" value="false">
            
            <div class="form-group">
                <label for="announcement-title">Title <span class="required">*</span></label>
                <input type="text" id="announcement-title" name="title" required placeholder="Enter announcement title">
            </div>

            <div class="form-group">
                <label for="announcement-text">Text/Explanation <span class="required">*</span></label>
                <textarea id="announcement-text" name="text" rows="5" required placeholder="Enter announcement details..."></textarea>
            </div>

            <div class="form-group">
                <label for="announcement-date">Date Publication <span class="required">*</span></label>
                <input type="date" id="announcement-date" name="date_publication" required>
            </div>

            <div class="form-group">
                <label for="announcement-image">Image (Optional)</label>
                <input type="file" id="announcement-image" name="image" accept="image/jpeg,image/jpg,image/png,image/gif">
                <small>Allowed types: JPG, PNG, GIF (Max 5MB)</small>
                <div id="image-preview-container" style="display: none; margin-top: 10px;">
                    <img id="image-preview" src="" alt="Preview" style="max-width: 200px; max-height: 200px; border-radius: 4px;">
                    <button type="button" id="remove-image-btn" class="remove-image-btn">Remove Image</button>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="cancel-btn" id="cancel-btn">Cancel</button>
                <button type="submit" class="submit-btn">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Success/Error Messages -->
<div class="success-message"></div>
<div class="error-message"></div>

<?php
$pageContent = ob_get_clean();
require_once __DIR__ . '/./admin_base_designs.php';
?>

