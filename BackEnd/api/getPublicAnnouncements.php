<?php
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

try {
    require_once __DIR__ . '/../admin/controller/adminAnnouncementsController.php';
    
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    if ($limit > 50) $limit = 50; // Max limit
    
    $controller = new adminAnnouncementsController();
    $response = $controller->apiGetPublicAnnouncements($limit);
    
    http_response_code($response['httpcode']);
    echo json_encode($response);
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

