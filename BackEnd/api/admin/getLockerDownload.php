<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../admin/models/adminLockerModel.php';

// Check if user is admin
if (!isset($_SESSION['Staff']['User-Id']) || !isset($_SESSION['Staff']['Staff-Id']) || $_SESSION['Staff']['Staff-Type'] != 1) {
    http_response_code(403);
    echo 'Unauthorized access';
    exit();
}

try {
    if (!isset($_GET['fileId']) || !is_numeric($_GET['fileId'])) {
        throw new Exception('File ID is required');
    }

    $fileId = (int)$_GET['fileId'];
    $staffId = (int)$_SESSION['Staff']['Staff-Id'];

    $model = new adminLockerModel();
    $file = $model->getFileById($fileId, $staffId);

    if (!$file || !file_exists($file['File_Path'])) {
        http_response_code(404);
        echo 'File not found';
        exit();
    }

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
    http_response_code(400);
    echo 'Error: ' . $e->getMessage();
}
?>

