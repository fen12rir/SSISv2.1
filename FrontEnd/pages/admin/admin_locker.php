<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['Staff']['User-Id']) || !isset($_SESSION['Staff']['Staff-Id']) || $_SESSION['Staff']['Staff-Type'] != 1) {
    header("Location: ../../Login.php");
    exit();
}

$pageTitle = "Locker";
$currentPage = "locker";
$pageCss = '<link rel="stylesheet" href="../../assets/css/admin/admin-locker.css">';
$pageJs = '<script src="../../assets/js/admin/admin-locker.js" defer></script>';

ob_start();
?>

<div class="locker-container">
    <div class="locker-header">
        <h1 class="locker-title">File Locker</h1>
        <p class="locker-subtitle">Store and manage your documents, presentations, and files</p>
    </div>

    <div class="locker-actions">
        <button class="upload-btn" id="upload-file-btn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="17 8 12 3 7 8"></polyline>
                <line x1="12" y1="3" x2="12" y2="15"></line>
            </svg>
            Upload File
        </button>
        <div class="search-container">
            <input type="text" id="search-files" placeholder="Search files..." class="search-input">
        </div>
    </div>

    <!-- Upload Modal -->
    <div id="upload-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Upload File</h2>
                <span class="close" id="close-upload-modal">&times;</span>
            </div>
            <form id="upload-form" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="file-input">Select File</label>
                    <input type="file" id="file-input" name="file" required accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.zip,.rar">
                    <small>Allowed types: Images, PDF, Documents, Presentations, Spreadsheets, Archives (Max 50MB)</small>
                </div>
                <div class="form-group">
                    <label for="file-description">Description (Optional)</label>
                    <textarea id="file-description" name="description" rows="3" placeholder="Add a description for this file..."></textarea>
                </div>
                <div class="form-actions">
                    <button type="button" class="cancel-btn" id="cancel-upload">Cancel</button>
                    <button type="submit" class="submit-btn">Upload</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Loading State -->
    <div id="loading-state" class="loading-container">
        <div class="spinner"></div>
        <p>Loading files...</p>
    </div>

    <!-- Files Grid -->
    <div id="files-grid" class="files-grid">
        <!-- Files will be dynamically inserted here -->
    </div>

    <!-- Empty State -->
    <div id="empty-state" class="empty-state" style="display: none;">
        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
            <polyline points="14 2 14 8 20 8"></polyline>
            <line x1="16" y1="13" x2="8" y2="13"></line>
            <line x1="16" y1="17" x2="8" y2="17"></line>
            <polyline points="10 9 9 9 8 9"></polyline>
        </svg>
        <h3>No files yet</h3>
        <p>Upload your first file to get started</p>
    </div>
</div>

<?php
$pageContent = ob_get_clean();
require_once __DIR__ . '/./admin_base_designs.php';
?>

