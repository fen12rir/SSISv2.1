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
    
    $postData = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($postData['fileId']) || !is_numeric($postData['fileId'])) {
        throw new Exception('File ID is required');
    }

    $fileId = (int)$postData['fileId'];

    $controller = new teacherLockerController();
    $response = $controller->apiDeleteFile($fileId, $staffId);
    
    http_response_code($response['httpcode']);
    echo json_encode($response);
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

