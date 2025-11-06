<?php
declare(strict_types=1);
require_once __DIR__ . '/../models/userEnrolleesModel.php';
require_once __DIR__ . '/../../Exceptions/DatabaseException.php';
class userEnrolleesController {
    protected $enrolleesModel;
    public function __construct() {
        $this->enrolleesModel = new userEnrolleesModel();
    }
    //API
    public function apiEnrolleeData(int $enrolleeId) : array { //F 3.5.3
        try {
            if(!is_numeric($enrolleeId)) {
                return [
                    'httpcode'=> 500,
                    'success'=> false,
                    'message'=> 'Invalid ID',
                    'data'=> []
                ];
            }
            $data = $this->enrolleesModel->getEnrolleeInformation($enrolleeId);
            if(empty($data)) {
                return [
                    'httpcode'=> 400,
                    'success'=> false,
                    'message'=> 'Enrollee data is empty',
                    'data'=> []
                ];
            }           
            $inputtableData = $this->returnInputtableData($data);
            return [
                'httpcode'=> 200,
                'success'=> true,
                'message'=> 'Enrollee data successfully fetched',
                'data'=> $inputtableData
            ];
        }
        catch(DatabaseException $e) {
            return [
                'httpcode'=> 500,
                'success'=>  false,
                'message'=> 'There was a problem on our side: ' .$e->getMessage(),
                'error_code'=> $e->getCode(),
                'error_message'=> $e->getPrevious->getMessage(),
                'data'=> []
            ];
        }
        catch(Exception $e) {
            return [
                'httpcode'=> 400,
                'success'=> false,
                'message'=> 'There was an unexpected problem: '.$e->getMessage(),
                'data'=> []
            ];
        }
        catch(Throwable $t){
            return [
                'httpcode'=> 500,
                'success'=> false,
                'message'=> 'There was a syntax problem. Please wait for it to be fixed',
                'data'=> []
            ];
        }
    }
    //HELPERS
    private function returnInputtableData(array $data) : array { //F 3.6.3
        $result = [];
        try {
            if(empty($data)) {
                return [
                    'success'=> false,
                    'message'=> 'Data to transform is empty',
                    'data'=> []
                ];
            }
            $result = [
                'First Name' => ['value' => $data['Student_First_Name'] ?? '' , 'type' => 'text'],
                'Last Name' => ['value' => $data['Student_Last_Name'] ?? '', 'type' => 'text'],
                'Middle Name' => ['value' => $data['Student_Middle_Name'] ?? '', 'type' => 'text'],
                'Extension' => ['value' => $data['Student_Extension'] ?? '', 'type' => 'text'],
                'LRN' => ['value' => $data['Learner_Reference_Number'] ?? '', 'type' => 'number'],
                'PSA' => ['value' => $data['Psa_Number'] ?? '', 'type' => 'number'],
                'Age' => ['value' => $data['Age'] ?? '', 'type' => 'number'],
                'Birthdate' => ['value' => $data['Birth_Date'] ?? '', 'type' => 'date'],
                'Sex' => ['value' => $data['Sex'] ?? '', 'type' => 'radio'],
                'Religion' => ['value' => $data['Religion'] ?? '', 'type' => 'text'],
                'Native Language' => ['value' => $data['Native_Language'] ?? '', 'type' => 'text'],
                'Belongs in Cultural Group' => ['value'=> $data['If_Cultural'] ?? '', 'type' => 'radio'],
                'Cultural Group' => ['value' => $data['Cultural_Group'] ?? '', 'type' => 'text'],
                'Email Address' => ['value' => $data['Student_Email'] ?? '', 'type' => 'email'],
                'Enrolling Grade Level' => ['value' => $data['Enrolling_Grade_Level'] ?? '', 'type' => 'select'],
                'Last Grade Level' => ['value' => $data['Last_Grade_Level'] ?? '', 'type' => 'select'],
                'Last Year Attended' => ['value' => $data['Last_Year_Attended'] ?? '', 'type' => 'number'],
                'Last School Attended' => ['value' => $data['Last_School_Attended'] ?? '', 'type' => 'text'],
                'School ID' => ['value' => $data['School_Id'], 'type' => 'number'] ?? '',
                'School Address' => ['value' => $data['School_Address'] ?? '', 'type' => 'text'],
                'School Type' => ['value' => $data['School_Type'] ?? '', 'type' => 'radio'],
                'Region' => ['value' => $data['Region'] ?? '', 'code' => $data['Region_Code'] ?? '', 'type' => 'select'],
                'Province' => ['value' => $data['Province_Name'] ?? '', 'code' => $data['Province_Code'] ?? '', 'type' => 'select'],
                'City/Municipality' => ['value' => $data['Municipality_Name'] ?? '', 'code' => $data['Municipality_Code'] ?? '', 'type' => 'select'],
                'Barangay' => ['value' => $data['Brgy_Name'] ?? '', 'code' => $data['Brgy_Code'] ?? '', 'type' => 'select'],
                'Subdivision' => ['value' => $data['Subd_Name'] ?? '', 'type' => 'text'],
                'House Number' => ['value' => $data['House_Number'] ?? '', 'type' => 'number'],
                'Has a Special Condition' => ['value' => $data['Have_Special_Condition'] ?? '', 'type' => 'radio'],
                'Special Condition' => ['value' => $data['Special_Condition'] ?? '', 'type' => 'text'],
                'Has Assistive Technology' => ['value' => $data['Have_Assistive_Tech'] ?? '', 'type' => 'radio'],
                'Assistive Technology' => ['value' => $data['Assistive_Tech'] ?? '', 'type' => 'text']
            ];
            if (isset($data['Parent_Information'])) {
                // Father's Information
                if (isset($data['Parent_Information']['father'])) {
                    $father = $data['Parent_Information']['father'];
                    $result['Father First Name'] = ['value' => $father['first_name'] ?? '', 'type' => 'text'];
                    $result['Father Middle Name'] = ['value' => $father['middle_name'] ?? '', 'type' => 'text'];
                    $result['Father Last Name'] = ['value' => $father['last_name'] ?? '', 'type' => 'text'];
                    $result['Father Educational Attainment'] = ['value' => $father['educational_attainment'] ?? '', 'type' => 'select'];
                    $result['Father Contact Number'] = ['value' => $father['contact_number'] ?? '', 'type' => 'text'];
                    $result['Father 4Ps Member'] = ['value' => $father['if_4ps'] ?? '', 'type' => 'radio'];
                }
                // Mother's Information
                if (isset($data['Parent_Information']['mother'])) {
                    $mother = $data['Parent_Information']['mother'];
                    $result['Mother First Name'] = ['value' => $mother['first_name'] ?? '', 'type' => 'text'];
                    $result['Mother Middle Name'] = ['value' => $mother['middle_name'] ?? '', 'type' => 'text'];
                    $result['Mother Last Name'] = ['value' => $mother['last_name'] ?? '', 'type' => 'text'];
                    $result['Mother Educational Attainment'] = ['value' => $mother['educational_attainment'] ?? '', 'type' => 'select'];
                    $result['Mother Contact Number'] = ['value' => $mother['contact_number'] ?? '', 'type' => 'text'];
                    $result['Mother 4Ps Member'] = ['value' => $mother['if_4ps'] ?? '', 'type' => 'radio'];
                }
                // Guardian's Information
                if (isset($data['Parent_Information']['guardian'])) {
                    $guardian = $data['Parent_Information']['guardian'];
                    $result['Guardian First Name'] = ['value' => $guardian['first_name'] ?? '', 'type' => 'text'];
                    $result['Guardian Middle Name'] = ['value' => $guardian['middle_name'] ?? '', 'type' => 'text'];
                    $result['Guardian Last Name'] = ['value' => $guardian['last_name'] ?? '', 'type' => 'text'];
                    $result['Guardian Educational Attainment'] = ['value' => $guardian['educational_attainment'] ?? '', 'type' => 'select'];
                    $result['Guardian Contact Number'] = ['value' => $guardian['contact_number'] ?? '', 'type' => 'text'];
                    $result['Guardian 4Ps Member'] = ['value' => $guardian['if_4ps'] ?? '', 'type' => 'radio'];
                }
            }
            //IMAGE 
            if(!empty($data['filename']) || !empty($data['filename'])) {
                $result['Psa Image'] = [
                    'value' => [
                        'filename'=> $data['filename'],
                        'path'=> $data['directory']
                    ],
                    'type'=> 'image'
                ];
            }
            return $result;
        }
        catch(DatabaseException $e) {
            return [
                'success'=> false,
                'message'=> 'There was a problem on our side: '.$e->getMessage(),
                'error_code'=> $e->getCode(),
                'error_message'=> $e->getPrevious()->getMessage(),
                'data'=> []
            ];
        }
        catch(Exception $e) {
            return [
                'success'=> false,
                'message'=> 'There was an unexpected problem: '.$e->getMessage(),
                'data'=> []
            ];
        }
    }
    private function returnUserStatus(int $uId, int $eId) : array {
        try {
            $data = $this->enrolleesModel->getUserstatus($uId,$eId);
            if($data <= 0) {
                return [
                    'success'=> false,
                    'message'=> 'Invalid enrollment status',
                    'data'=> []
                ];
            }
            return [
                'success'=> true,
                'message'=> 'Enrollment status successfully returned',
                'data'=> $data
            ];
        }
        catch(DatabaseException $e) {
            return [
                'success'=> false,
                'message'=> 'There was a problem on our side: '.$e->getMessage(),
                'data'=> []
            ];
        }
        catch(Exception $e) {
            return [
                'success'=> false,
                'message'=> 'There was an unexpected problem: '.$e->getMessage(),
                'data'=> []
            ];
        }
    }
    //VIEW
    public function viewUserEnrollees(int $userId) : array {
        try {
            if(!is_numeric($userId)) {
                return [
                    'success'=> false,
                    'message'=> 'Invalid user ID',
                    'data'=> []
                ];
            }
            $data = $this->enrolleesModel->getUserEnrollees($userId);
            if(empty($data)) {
                return [
                    'success'=> true,
                    'message'=> 'User Enrollees are empty',
                    'data'=> []
                ];
            }
            return [
                'success'=> true,
                'message'=> 'User Enrollees successfully submitted',
                'data'=> $data
            ];
        }
        catch(DatabaseException $e) {
            return [
                'success'=> false,
                'message'=> 'There was a problem on our side: '.$e->getMessage(),
                'data'=> []
            ];
        }
        catch(Exception $e) {
            return [
                'success'=> false,
                'message'=> 'There was an unexpected problem: '.$e->getMessage(),
                'data'=> []
            ];
        }
    }
    public function viewUserEnrollmentStatus(int $userId, int $enrolleeId) : array {
        try {
            if(!is_numeric($userId)) {
                return [
                    'success'=> false,
                    'message'=> 'Invalid User ID',
                    'data'=> []
                ];
            }
            if(!is_numeric($enrolleeId)) {
                return [
                    'success'=> false,
                    'message'=> 'Invalid enrollee ID',
                    'data'=> []
                ];
            }
            $userEnrollmentStatus = $this->returnUserStatus($userId, $enrolleeId);
            $data = $this->enrolleesModel->getUserTransactionStatus($enrolleeId);
            if(!$userEnrollmentStatus['success']) {
                return [
                    'success'=> false,
                    'message'=> $userEnrollmentStatus['message'],
                    'data'=> []
                ];
            }
            if(empty($data)) {
                return [
                    'success'=> true,
                    'message'=> 'Transaction details are empty',
                    'data'=> []
                ];
            }
            return [
                'success'=> true,
                'message'=> 'Transaction details successfully fetched',
                'data'=> $data,
                'enrollment_status'=> $userEnrollmentStatus['data']
            ];
        }
        catch(DatabaseException $e) {
            return [
                'success'=> false,
                'message'=> 'There was a problem on our side: '.$e->getMessage(),
                'data'=> []
            ];
        }
        catch(Exception $e) {
            return [
                'success'=> false,
                'message'=> 'There was an unexpected problem: '.$e->getMessage(),
                'data'=> []
            ];
        }
    }
}