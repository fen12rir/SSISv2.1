<?php
declare(strict_types=1);
session_start();
require_once __DIR__ .  '/../../user/controller/userPostEnrollmentFormController.php';
require_once __DIR__ . '/../../Exceptions/IdNotFoundException.php';

header('Content-Type: application/json');
try {
    // Check if it's a POST request
    if ($_SERVER['REQUEST_METHOD'] !== "POST") {
        throw new Exception('Invalid request method');
    }
    // Check for user session
    if (!isset($_SESSION['User']['User-Id'])) {
        throw new IdNotFoundException('Unrecognized user');
    }

    $userId = (int)$_SESSION['User']['User-Id'];
    $controller = new userEnrollmentFormController();
    // EDUCATIONAL INFORMATION
    $School_Year_Start = $_POST['start-year'] ?? null;
    $School_Year_End = $_POST['end-year'] ?? null;
    $hasLRN = isset($_POST['bool-LRN']) ? (int)$_POST['bool-LRN'] : null;
    $Enrolling_Grade_Level = $_POST['grades-tbe'] ?? null;
    $Last_Grade_Level = $_POST['last-grade'] ?? null;
    $Last_Year_Attended = $_POST['last-year'] ?? null;
    // EDUCATIONAL BACKGROUND
    $Last_School_Attended = $_POST['lschool'] ?? null;
    $School_Id = isset($_POST['lschoolID']) ? (int)$_POST['lschoolID'] : null;
    $School_Address = $_POST['lschoolAddress'] ?? null;
    $School_Type = $_POST['school-type'] ?? null;
    $Initial_School_Choice = $_POST['fschool'] ?? null;
    $Initial_School_Id = isset($_POST['fschoolID']) ? (int)$_POST['fschoolID'] : null;
    $Initial_School_Address = $_POST['fschoolAddress'] ?? null;
    // ENROLLEE INFORMATION
    $Student_First_Name = $_POST['fname'] ?? null;
    $Student_Middle_Name = $_POST['mname'] ?? null;
    $Student_Last_Name = $_POST['lname'] ?? null;
    $Student_Extension = $_POST['extension'] ?? null;
    $Learner_Reference_Number = isset($_POST['LRN']) ? (int)$_POST['LRN'] : null;
    $Psa_Number = isset($_POST['PSA-number']) ? (int)$_POST['PSA-number'] : null;
    $Birth_Date = $_POST['bday'] ?? null;
    $Age = isset($_POST['age']) ? (int)$_POST['age'] : null;
    $Sex = $_POST['gender'] ?? null;
    $Religion = $_POST['religion'] ?? null;
    $Native_Language = $_POST['language'] ?? null;
    $If_Cultural = isset($_POST['group']) ? (int)$_POST['group'] : null;
    $Cultural_Group = $_POST['community'] ?? null;
    $Student_Email = $_POST['email'] ?? null;
    //  DISABILITY INFORMATION
    $Have_Special_Condition = isset($_POST['sn']) ? (int)$_POST['sn'] : null;
    $Special_Condition = $_POST['boolsn'] ?? null;
    $Have_Assistive_Tech = isset($_POST['at']) ? (int)$_POST['at'] : null;
    $Assistive_Tech = $_POST['atdevice'] ?? null;
    //  EROLLEE ADDRESS
    $House_Number = isset($_POST['house-number']) ? (int)$_POST['house-number'] : null;
    $Subd_Name = $_POST['subdivision'] ?? null;
    $Brgy_Code = isset($_POST['barangay']) ? (int)$_POST['barangay'] : null;
    $Brgy_Name = $_POST['barangay-name'] ?? null;
    $Municipality_Code = isset($_POST['city-municipality']) ? (int)$_POST['city-municipality'] : null;
    $Municipality_Name = $_POST['city-municipality-name'] ?? null;
    $Province_Code = isset($_POST['province']) ? (int)$_POST['province'] : null;
    $Province_Name = $_POST['province-name'] ?? null;
    $Region_Code = isset($_POST['region']) ? (int)$_POST['region'] : null;
    $Region = $_POST['region-name'] ?? null;
    // ENROLLEE PARENTS INFORMATION
    // FATHER
    $Father_First_Name = $_POST['Father-First-Name'] ?? null;
    $Father_Last_Name = $_POST['Father-Last-Name'] ?? null;
    $Father_Middle_Name = $_POST['Father-Middle-Name'] ?? null;
    $Father_Educational_Attainment = $_POST['F-highest-education'] ?? null;
    $Father_Contact_Number = $_POST['F-Number'] ?? null;
    $FIf_4Ps = isset($_POST['fourPS']) ? (int)$_POST['fourPS'] : null;
    //MOTHER
    $Mother_First_Name = $_POST['Mother-First-Name'] ?? null;
    $Mother_Last_Name = $_POST['Mother-Last-Name'] ?? null;
    $Mother_Middle_Name = $_POST['Mother-Middle-Name'] ?? null;
    $Mother_Educational_Attainment = $_POST['M-highest-education'] ?? null;
    $Mother_Contact_Number = $_POST['M-Number'] ?? null;
    $MIf_4Ps = isset($_POST['fourPS']) ? (int)$_POST['fourPS'] : null;
    //GUARDIAN
    $Guardian_First_Name = $_POST['Guardian-First-Name'] ?? null;
    $Guardian_Last_Name = $_POST['Guardian-Last-Name'] ?? null;
    $Guardian_Middle_Name = $_POST['Guardian-Middle-Name'] ?? null;
    $Guardian_Educational_Attainment = $_POST['G-highest-education'] ?? null;
    $Guardian_Contact_Number = $_POST['G-Number'] ?? null;
    $GIf_4Ps =isset($_POST['fourPS']) ? (int)$_POST['fourPS'] : null;
    // ENROLLEE STATUS(PENDING)
    $Enrollment_Status = 3;
    //IMAGE
    $image = $_FILES['psa-image'] ?? null;
    // Insert the values into the database
    $response = $controller->apiPostAddEnrollee(
        $userId, $School_Year_Start, $School_Year_End, $hasLRN, $Enrolling_Grade_Level, $Last_Grade_Level, $Last_Year_Attended,
        $Last_School_Attended, $School_Id, $School_Address, $School_Type, $Initial_School_Choice, $Initial_School_Id, $Initial_School_Address,
        $Have_Special_Condition, $Have_Assistive_Tech, $Special_Condition, $Assistive_Tech,
        $House_Number, $Subd_Name, $Brgy_Name, $Brgy_Code, $Municipality_Name, $Municipality_Code, $Province_Name, $Province_Code, $Region, $Region_Code,
        $Father_First_Name, $Father_Last_Name, $Father_Middle_Name, $Father_Educational_Attainment, $Father_Contact_Number, $FIf_4Ps,
        $Mother_First_Name, $Mother_Last_Name, $Mother_Middle_Name, $Mother_Educational_Attainment, $Mother_Contact_Number, $MIf_4Ps,
        $Guardian_First_Name, $Guardian_Last_Name, $Guardian_Middle_Name, $Guardian_Educational_Attainment, $Guardian_Contact_Number, $GIf_4Ps,
        $Student_First_Name,$Student_Last_Name,$Student_Middle_Name,$Student_Extension, $Learner_Reference_Number, $Psa_Number, $Birth_Date, $Age, $Sex, $Religion,
        $Native_Language, $If_Cultural, $Cultural_Group, $Student_Email, $Enrollment_Status, $image);
    //SET CONTROLLER HTTP RESPONSE CODE
    http_response_code($response['httpcode']);
    echo json_encode($response);
    exit();
} 
catch(IdNotFoundException $e) {
    echo json_encode(['success'=> false, 'message'=> $e->getMessage()]);
    exit();
}
catch (Exception $e) {
    error_log('Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit();
}
?>