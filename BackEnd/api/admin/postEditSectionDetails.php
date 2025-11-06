<?php

declare(strict_types=1);
require_once __DIR__ . '/../../admin/controller/adminViewSectionController.php';

header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success'=> false, 'message'=> 'Invalid request method']);
    exit();
}
$sectionName = $_POST['section-name'] ?? null;
$adviserId = isset($_POST['select-adviser']) && $_POST['select-adviser'] !== '' ? (int)$_POST['select-adviser'] : null;

$sectionId = isset($_POST['section-id']) ? (int)$_POST['section-id'] : null;
$studentIds = isset($_POST['students']) ? $_POST['students'] : [];

$controller = new adminViewSectionController();
$response = $controller->apiPostEditSectionDetails($sectionName, $adviserId, $sectionId, $studentIds);

http_response_code($response['httpcode']);
echo json_encode($response);
exit();
