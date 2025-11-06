<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../user/controller/userEnrollmentFormController.php';
require_once __DIR__ . '/../../Exceptions/IdNotFoundException.php';

header('Content-Type: application/json');
// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit();
}
try {
    // Get POST data
    $postData = json_decode(file_get_contents('php://input'), true);
    if(!isset($_SESSION['User']['User-Id'])) {
        throw new IdNotFoundException('Enrollee ID not found');
    }
    $userId = (int) $_SESSION['User']['User-Id'];
    $controller = new userEnrollmentFormController();
    $response = $controller->apiUpdateEnrolleeInfo($userId, $postData); //REF: F 3.5.1
    http_response_code($response['httpcode']);
    echo json_encode($response);
    exit(); 
}
catch(IdNotFoundException $e) {
    echo json_encode(['success'=> false, 'message'=> $e->getMessage()]);
    exit();
}
catch(Exception $e) {
    echo json_encode(['success'=> false, 'message'=> $e->getMessage()]);
    exit();
}