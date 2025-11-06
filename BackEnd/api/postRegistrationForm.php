<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');
require_once __DIR__ . '/../common/registration.php';
    
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(['success'=> false, 'message'=> 'Invalid request method']);
    exit();
}

// Validate terms acceptance
$termsAcceptance = $_POST['terms-acceptance'] ?? null;
if ($termsAcceptance !== 'on') {
    echo json_encode(['success'=> false, 'message'=> 'You must accept the Terms & Conditions']);
    http_response_code(400);
    exit();
}

$controller = new Registration();
        
// PERSONAL INFORMATION
$First_Name = $_POST['Guardian-First-Name'] ?? null;
$Last_Name = $_POST['Guardian-Last-Name'] ?? null;
$Middle_Name = $_POST['Guardian-Middle-Name'] ?? null;
$Contact_Number = $_POST['Contact-Number'] ?? null;
$User_Type = 3;

$response = $controller->registerAndInsert($First_Name, $Last_Name, $Middle_Name, $Contact_Number, $User_Type);
http_response_code($response['httpcode']);
echo json_encode($response);
exit(); 
?>