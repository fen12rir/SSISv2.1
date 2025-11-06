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

// Check if user is admin
if (!isset($_SESSION['Staff']['User-Id']) || !isset($_SESSION['Staff']['Staff-Id']) || $_SESSION['Staff']['Staff-Type'] != 1) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

try {
    require_once __DIR__ . '/../../admin/controller/adminAnnouncementsController.php';
    
    $announcementId = isset($_POST['announcement_id']) ? (int)$_POST['announcement_id'] : 0;
    $title = $_POST['title'] ?? '';
    $text = $_POST['text'] ?? '';
    $datePublication = $_POST['date_publication'] ?? '';
    $imageFile = isset($_FILES['image']) ? $_FILES['image'] : null;
    $removeImage = isset($_POST['remove_image']) && $_POST['remove_image'] === 'true';

    if ($announcementId <= 0) {
        throw new Exception('Invalid announcement ID');
    }

    $controller = new adminAnnouncementsController();
    $response = $controller->apiUpdateAnnouncement(
        $announcementId,
        trim($title),
        trim($text),
        $imageFile,
        trim($datePublication),
        $removeImage
    );
    
    http_response_code($response['httpcode']);
    echo json_encode($response);
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

