<?php
declare(strict_types=1);
header('Content-Type: application/json');
require_once __DIR__ . '/../../admin/controller/adminUnprocessedEnrollmentsController.php';
try {
    if($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success'=> false, 'message' => 'Wrong method']);
        exit();
    }
    $status = isset($_POST['enrollment-status']) ? (int)$_POST['enrollment-status'] : null;
    $enrolleeId = isset($_POST['enrollee-id']) ? (int)$_POST['enrollee-id'] : null;
    $controller = new adminUnprocessedEnrollmentsController();
    $response = $controller->apiPostUpdateEnrollee($status,$enrolleeId);
    http_response_code($response['httpcode']);
    echo json_encode($response);
    exit();
}
catch(IdNotFoundException $e) {
    echo json_encode(['success'=> false,'message'=>$e->getMessage()]);
    exit();
}

