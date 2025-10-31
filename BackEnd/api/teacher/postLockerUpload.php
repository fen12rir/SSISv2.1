<?php
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

// Check if user is teacher
if (!isset($_SESSION['Staff']['User-Id']) || !isset($_SESSION['Staff']['Staff-Id']) || $_SESSION['Staff']['Staff-Type'] != 2) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

try {
    require_once __DIR__ . '/../../teacher/controller/teacherLockerController.php';
    
    $staffId = (int)$_SESSION['Staff']['Staff-Id'];
    
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No file uploaded or upload error occurred');
    }

    $description = $_POST['description'] ?? null;
    $description = !empty($description) ? trim($description) : null;

    $controller = new teacherLockerController();
    $response = $controller->apiUploadFile($staffId, $_FILES['file'], $description);
    
    http_response_code($response['httpcode']);
    echo json_encode($response);
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

