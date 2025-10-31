<?php
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Start output buffering to prevent any accidental output
ob_start();

session_start();

// Check if user is admin
if (!isset($_SESSION['Staff']['User-Id']) || !isset($_SESSION['Staff']['Staff-Id']) || $_SESSION['Staff']['Staff-Type'] != 1) {
    ob_end_clean();
    http_response_code(403);
    header('Content-Type: text/plain');
    echo 'Unauthorized access';
    exit();
}

try {
    require_once __DIR__ . '/../../admin/models/adminLockerModel.php';
    
    if (!isset($_GET['fileId']) || !is_numeric($_GET['fileId'])) {
        throw new Exception('File ID is required');
    }

    $fileId = (int)$_GET['fileId'];
    $staffId = (int)$_SESSION['Staff']['Staff-Id'];

    $model = new adminLockerModel();
    $file = $model->getFileById($fileId, $staffId);

    if (!$file || !file_exists($file['File_Path'])) {
        ob_end_clean();
        http_response_code(404);
        header('Content-Type: text/plain');
        echo 'File not found';
        exit();
    }

    // Clean output buffer before sending headers
    ob_end_clean();

    // Set headers for file download
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($file['Original_File_Name']) . '"');
    header('Content-Length: ' . filesize($file['File_Path']));
    header('Cache-Control: must-revalidate');
    header('Pragma: public');

    // Output file
    readfile($file['File_Path']);
    exit();
} catch (Exception $e) {
    ob_end_clean();
    http_response_code(400);
    header('Content-Type: text/plain');
    echo 'Error: ' . $e->getMessage();
    exit();
}

