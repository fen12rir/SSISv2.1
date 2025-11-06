<?php
declare(strict_types=1);
require_once __DIR__ . '/../../core/dbconnection.php';
require_once __DIR__ . '/../../Exceptions/IdNotFoundException.php';
require_once __DIR__ . '/../../admin/controller/adminUnprocessedEnrollmentsController.php';
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}
try {
    $id = isset($_GET['id']) ? (int) $_GET['id'] : null;   
    if(is_null($id)) {
        throw new IdNotFoundException('Enrollee ID not found');
    }
    $controller = new adminUnprocessedEnrollmentsController();
    $response = $controller->apiFetchEnrolleeRemarks($id);
    http_response_code($response['httpcode']);
    echo json_encode($response);
    exit();
}
catch(IdNotFoundException $e) {
    echo json_encode(['success'=> false,'message'=> $e->getMessage()]);
    exit();
} 
catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error']);
    exit();
}
