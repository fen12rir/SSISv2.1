<?php
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}
ob_start();

$pageTitle = 'Edit Information';
$pageCss = '<link rel="stylesheet" href="../../assets/css/teacher/teacher-edit-information-links.css" media="all">';
$pageJs = '<script type="module" src="../../assets/js/teacher/teacher-edit-information-links.js"></script>';

?>

<div class="teacher-edit-information-content">
    <div class="edit-information-wrapper">
        <div class="edit-information-header">
            <h1 class="edit-information-title">Edit Information</h1>
            <p class="edit-information-subtitle">Manage your personal information and profile settings</p>
        </div>
        <div class="container">
            <p class="title">Edit Information</p>
            <div class="links">
                <div class="card">
                    <button id="edit-personal-information">Edit Personal Information</button>
                </div>
                <div class="card">
                    <button id="edit-address">Edit Address</button>
                </div>
                <div class="card">
                    <button id="edit-credentials">Edit Credentials</button>
                </div>
                <div class="card">
                    <button id="edit-profile-picture">Edit Profile Picture</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="information-modal">
    <div class="modal-content" id="information-modal-content">
        
    </div>
</div>
<?php
$pageContent = ob_get_clean();
require_once __DIR__ . '/./teacher_base_designs.php';
?>
