<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../staff/controllers/staffEnrollmentController.php';
header("Content-Type: application/json");
try {
    if($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message'=> 'Invalid request method']);
        exit();
    }
    if(!isset($_SESSION['Staff']) || !in_array($_SESSION['Staff']['Staff-Type'], [1,2])) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
        exit();
    }
    //UDATE VALUES
    $enrolleeId = (int)$_POST['id'] ?: null;
    $status = (int)$_POST['status'] ?: null;
    $remarks = $_POST['remarks'] ?? null;
    $staffId = isset($_SESSION['Staff']['Staff-Id']) ? (int)$_SESSION['Staff']['Staff-Id'] : null;
    $staffType = isset($_SESSION['Staff']['Staff-Type']) ? (int) $_SESSION['Staff']['Staff-Type'] : null;
    //CONTROLLER OBJECT
    $controller = new staffEnrollmentController();
    $response = $controller->apiPostUpdateEnrolleeStatus($staffId, $staffType,$status, $enrolleeId,$remarks);

    http_response_code($response['httpcode']);
    echo json_encode($response);
    exit();
}
catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit();
}
