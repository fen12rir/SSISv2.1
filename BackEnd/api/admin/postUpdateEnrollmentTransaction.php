<?php
declare(strict_types =1);
require_once __DIR__ . '/../../admin/controller/adminUnprocessedEnrollmentsController.php';
header('Content-Type: application/json');
try {
    if($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success'=> false, 'message' => 'Invalid Request method']);
        exit(); 
    }    
    $transactionStatus= isset($_POST['transaction-status']) ? (int) $_POST['transaction-status']:null; 
    $transactionId = isset($_POST['transaction-id'])? (int)$_POST['transaction-id']:null;
    $status = isset($_POST['enrollment-status']) ? (int)$_POST['enrollment-status'] : null;
    $enrolleeId = isset($_POST['enrollee-id']) ? (int)$_POST['enrollee-id'] : null;

    $controller = new adminUnprocessedEnrollmentsController();
    $response = $controller->apiPostUpdateEnrollmentTransaction($transactionStatus,$status, $transactionId,$enrolleeId);
    http_response_code($response['httpcode']);
    echo json_encode($response);
    exit();
}
catch(IdNotFounException $e) {
    echo json_encode(['success'=> false,'message'=> $e->getMessage()]);
    exit();
}
