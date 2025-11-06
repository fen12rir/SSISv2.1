<?php
declare(strict_types=1);
require_once __DIR__ . '/../../staff/models/staffEnrollmentTransactionsModel.php';
require_once __DIR__ . '/../../admin/models/adminEnrolleesModel.php';
require_once __DIR__ . '/../../admin/models/adminStudentsModel.php';
require_once __DIR__ . '/../../staff/models/staffEnrolleesModel.php';

class staffEnrollmentController {
    protected $transactionsModel;
    protected $adminStudentModel;
    protected $adminEnrolleeModel;
    protected $staffEnrolleeModel;
    private const BOOL_FALSE = 0;
    private const BOOL_TRUE = 1;
    public function __construct() { 
        $this->transactionsModel = new staffEnrollmentTransactionsModel();
        $this->adminStudentModel = new adminStudentsModel();
        $this->adminEnrolleeModel = new adminEnrolleesModel();
        $this->staffEnrolleeModel = new staffEnrolleesModel();
    }
    //API
    public function apiPostUpdateEnrolleeStatus(int $staffId,int $staffType, int $status,int $enrolleeId,?string $remarks) : array {
        try {
            if(empty($enrolleeId)) {
                return [
                    'httpcode'=> 500,
                    'success'=> false,
                    'message'=> 'Enrollee ID is invalid',
                    'data'=> []
                ];
            }
            if(!in_array($status, [1,2,4])) {
                return [
                    'httpcode'=> 500,
                    'success'=> false,
                    'message'=> 'Invalid Enrollee status provided',
                    'data'=> []
                ];
            }
            if(!in_array($staffType, [1,2]) || !isset($staffId)) {
                return [
                    'httpcode'=> 400,
                    'success'=> false,
                    'message'=> 'User is non-Staff or Unknown. Cannot save changes',
                    'data'=> []
                ];
            }
            $statusCode = [
                1 => 'E',
                2 => 'D',
                4 => 'F'
            ][$status];
            //RANDOM NUMBER CHARACTERS
            $rand = '';
            for($i = 0;$i<8;$i++) $rand .= random_int(0,9);
            $time = time();
            $transactionCode = $statusCode . "-" . $rand . "-" . $time;
            if($staffType === 1) {
                $adminUpdate = $this->executeAdminUpdate($enrolleeId, $transactionCode, $status, $staffId, $remarks);
                if(!$adminUpdate['success']) {
                    return [
                        'httpcode'=> 400,
                        'success'=> false,
                        'message'=> $adminUpdate['message'],
                        'data'=> []
                    ];
                }
                return [
                    'httpcode'=> 200,
                    'success'=> true,
                    'message'=> $adminUpdate['message'],
                    'data'=> []
                ];
            }
            else {
                $teacherUpdate = $this->executeTeacherUpdate($enrolleeId,$transactionCode,$status,$staffId,$remarks);
                if(!$teacherUpdate['success']) {
                    return [
                        'httpcode'=> 400,
                        'success'=> false,
                        'message'=> $teacherUpdate['message'],
                        'data'=> []
                    ];
                }
                return [
                    'httpcode'=> 200,
                    'success'=> true,
                    'message'=> $teacherUpdate['message'],
                    'data'=> []
                ];
            }
        }
        catch(DatabaseException $e) {
            return [
                'httpcode'=> 500,
                'success'=> false,
                'message'=> 'There was a problem on our side: '.$e->getMessage(),
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
    }
    //HELPERS
    private function executeTeacherUpdate(int $enrolleeId, string $transactionCode, int $status, int $staffId,?string $remarks) : array {
        try {
            if(!$this->adminEnrolleeModel->setIsHandledStatus($enrolleeId,self::BOOL_TRUE)
                || !$this->transactionsModel->insertEnrolleeTransaction($enrolleeId, $transactionCode, $status, $staffId, $remarks, self::BOOL_FALSE)) {
                return ['success'=> false,'message'=> 'Inserting enrollee transaction failed'];
            }
            return ['success'=> true,'message'=> 'Teacher successfully updated changes'];
        }
        catch(DatabaseException $e) {
            return [
                'httpcode'=> 500,
                'success'=> false,
                'message'=> 'There was a problem on our side: '. $e->getMessage(),
                'error_code'=> $e->getCode(),
                'error_message'=> $e->getPrevious()?->getMessage(),
                'data'=> []
            ];
        }
        catch(Exception $e) {
            return [
                'httpcode'=> 500,
                'success'=> false,
                'message'=> 'There was an unexpected problem: '.$e->getMessage(),
                'error_code'=> $e->getCode(),
                'error_message'=> $e->getPrevious()?->getMessage()
            ];
        }
    }
    private function executeAdminUpdate(int $enrolleeId, string $transactionCode, int $status, int $staffId, ?string $remarks) : array {
        try {
            if($status === 1) {
                if(!$this->enrolleesModel->udpateEnrollee($enrolleeId,$enrollmentStatus) || !$this->adminEnrolleeModel->setIsHandledStatus($enrolleeId, self::BOOL_TRUE)
                    || !$this->transactionsModel->updateIsApprovedToTrue($enrolleeId,self::BOOL_TRUE)) {
                    return ['success'=> false,'message'=> "Failed to update Enrollee's statuses",'data'=>[]];
                }
                if(!$this->transactionsModel->insertEnrolleeTransaction($enrolleeId,$transactionCode,$status, $staffId, $remarks, self::BOOL_TRUE)) {
                    return ['success'=> false,'message'=> 'Inserting enrollee transaction failed'];
                }
                if(!$this->adminStudentModel->insertEnrolleeToStudent($enrolleeId)) {
                    return ['success'=> false,'message'=> 'Enrollee insert to student failed'];
                }
                return ['success'=> true,'message'=> 'Admin successfully enrolled and inserted Enrollee to student','data'=>[]];
            }
            else if($status === 2){
                if(!$this->enrolleesModel->udpateEnrollee($enrolleeId,$enrollmentStatus) || !$this->adminEnrolleeModel->setIsHandledStatus($enrolleeId, self::BOOL_TRUE)
                    || !$this->transactionsModel->updateIsApprovedToTrue($enrolleeId,self::BOOL_TRUE)) {
                    return ['success'=> false,'message'=> "Failed to update Enrollee's statuses",'data'=>[]];
                }
                if(!$this->transactionsModel->insertEnrolleeTransaction($enrolleeId,$transactionCode,$status, $staffId, $remarks, self::BOOL_TRUE)) {
                    return ['success'=> false,'message'=> 'Inserting enrollee transaction failed'];
                }
                return ['success'=> true,'message'=> 'Admin successfully denied the Enrollee. Cannot change it again.','data'=>[]];
            }
            else if($status === 4) {
                if(!$this->adminEnrolleeModel->setIsHandledStatus($enrolleeId, self::BOOL_TRUE)
                    || !$this->transactionsModel->insertEnrolleeTransaction($enrolleeId,$transactionCode,$status, $staffId, $remarks, self::BOOL_FALSE)) {
                    return ['success'=> false,'message'=> 'Inserting enrollee transaction failed'];
                }
                return ['success'=> false,'message'=>'Admin successfully followed up the Enrollee.','data'=>[]];
            }
            else {
                return ['success'=> false ,'message'=> 'Failed to start Admin update operations'];
            }
        }
        catch(DatabaseException $e) {
            return [
                'httpcode'=> 500,
                'success'=> false,
                'message'=> 'There was a problem on our side: '.$e->getMessage(),
                'error_code'=> $e->getCode(),
                'error_message'=> $e->getPrevious()?->getMessage(),
                'data'=> []
            ];
        }
        catch(Exception $e) {
            return [
                'success'=> false,
                'message'=> 'There was an unexpected problem: '.$e->getMessage(),
            ];
        }
    }
    //VIEW
    public function viewPendingEnrollees(): array {
        try {
            $data = $this->staffEnrolleeModel->getPendingEnrollees();
            if(empty($data)) {
                return [
                    'success'=> false,
                    'message'=> 'Pending enrollees are empty',
                    'data'=> []
                ];
            }
            return [
                'success'=> true,
                'message'=> 'Pending enrollees successfully fetched',
                'data'=> $data
            ];
        }
        catch(DatabaseException $e) {
            return [
                'success'=> false,
                'message'=> 'There was a problem on our side: '.$e->getMessage(),
                'error_code'=> $e->getCode(),
                'error_message'=> $e->getPrevious->getMessage(),
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