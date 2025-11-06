<?php
declare(strict_types=1);
require_once __DIR__ . '/../../user/controller/userEnrolleesController.php';
require_once __DIR__ . '/../../Exceptions/IdNotFoundException.php';

header('Content-Type: application/json');
try {
    $controller = new userEnrolleesController();
    if (!isset($_GET['editId'])) {
        throw new IdNotFoundException('Enrollee ID not found');
    }
    $enrolleeId = isset($_GET['editId']) ? (int) $_GET['editId'] : null;
    $response= $controller->apiEnrolleeData($enrolleeId); //REF: 3.5.3
    http_response_code($response['httpcode']);
    echo json_encode($response);
    exit();
}
catch(IdNotFoundException $e) {
    echo json_encode(['success'=> false, 'message'=> $e->getMessage()]);
    exit();
}
catch(Exception $e) {
    echo json_encode(['success'=> false,'message'=> $e->getMessage()]);
    exit();
}

