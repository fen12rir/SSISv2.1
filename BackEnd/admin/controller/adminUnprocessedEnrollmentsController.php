<?php
declare(strict_types=1);
require_once __DIR__ . '/../models/adminEnrollmentTransactionsModel.php';
require_once __DIR__ . '/../models/adminEnrolleesModel.php';
require_once __DIR__ . '/../models/adminStudentsModel.php';
require_once __DIR__ . '/../../Exceptions/IdNotFoundException.php';
class adminUnprocessedEnrollmentsController {
    protected $transactionsModel;
    protected $enrolleesModel;
    protected $studentsModel;
    protected const BOOL_TRUE = 1;
    public function __construct() {
        $this->transactionsModel = new adminEnrollmentTransactionsModel();
        $this->enrolleesModel = new adminEnrolleesModel();
        $this->studentsModel = new adminStudentsModel();
    }
    //API
    public function apiPostUpdateEnrollee(?int $enrollmentStatus, ?int $enrolleeId):array {
        try {
            if(is_null($enrolleeId)) {
                throw new IdNotFoundException('Enrollee ID not found');
            }
            if(is_null($enrollmentStatus) || !in_array($enrollmentStatus,[1,2,4])) {
                return ['httpcode'=> 400,'success'=>false,'message'=>'Enrollment status provided was not valid','data'=>[]];
            }
            if($enrollmentStatus === 1) {
                if(!$this->enrolleesModel->updateEnrollee($enrolleeId,$enrollmentStatus) || !$this->enrolleesModel->setIsHandledStatus($enrolleeId, self::BOOL_TRUE) 
                    || !$this->transactionsModel->updateIsApprovedToTrue($enrolleeId,self::BOOL_TRUE)) {
                    return ['httpcode'=> 500,'success'=> false,'message'=> "Failed to update Enrollee's statuses",'data'=>[]];
                }
                if(!$this->studentsModel->insertEnrolleeToStudent($enrolleeId)) {
                    return ['httpcode'=> 500,'success'=> false,'message'=> 'Failed to insert enrollee to student'];
                }
                return ['httpcode'=> 200,'success'=> true,'message'=> 'Successfully enrolled Enrollee and inserted to student','data'=>[]];
            }
            else if($enrollmentStatus === 2){
                if(!$this->enrolleesModel->updateEnrollee($enrolleeId,$enrollmentStatus) || !$this->enrolleesModel->setIsHandledStatus($enrolleeId, self::BOOL_TRUE)
                    || !$this->transactionsModel->updateIsApprovedToTrue($enrolleeId,self::BOOL_TRUE)) {
                    return ['httpcode'=> 500,'success'=> false,'message'=> "Failed to update Enrollee's statuses",'data'=>[]];
                }
                return ['httpcode'=> 200,'success'=> true,'message'=> 'Enrollee was updated and denied','data'=>[]];
            }
            else if($enrollmentStatus === 4){
                if(!$this->enrolleesModel->updateEnrollee($enrolleeId,$enrollmentStatus)) {
                    return ['httpcode'=> 500,'success'=> false,'message'=> 'Failed to reflect Enrollee to followed up','data'=>[]];
                }
                if(!$this->transactionsModel->updateTransactionToFollowUp($enrolleeId,$enrollmentStatus)) {
                    return ['httpcode'=> 500,'success'=> false,'message'=>'Failed to flag Enrollee for follow up','data'=>[]];
                }
                return ['httpcode'=> 200,'success'=> true,'message'=>'Successfully followed up enrollee','data'=>[]];
            }
            else {
                return ['httpcode'=> 200,'success'=> false,'message'=> 'Update operation did not continue','data'=>[]];
            }
        }
        catch(DatabaseException $e) {
            error_log("[".date('Y-m-d H:i:s')."]" . $e->getMessage() . "\n", 3, __DIR__  . '/../../errorLogs.txt');
            return [
                'httpcode'=> 500,
                'success'=> false,
                'message'=> 'There was a problem on our side.',
                'data'=>[]
            ];
        }
        catch(Exception $e){
            error_log("[".date('Y-m-d H:i:s')."]" . $e->getMessage() . "\n", 3, __DIR__  . '/../../errorLogs.txt');
            return [
                'httpcode'=> 400,
                'success'=> false,
                'message'=> 'There was an unexpected problem.',
                'data'=>[]
            ];
        }
    }
    public function apiPostUpdateEnrollmentTransaction(?int $transactionStatus,?int $enrollmentStatus,?int $transactionId,?int $enrolleeId):array {
        try {
            if(is_null($enrolleeId)) {
                throw new IdNotFoundException('Enrollee ID not found');
            }
            if(is_null($transactionId)) {
                throw new IdNotFoundException("Transaction ID not found");
            }
            if(is_null($enrollmentStatus) || !in_array($enrollmentStatus,[1,2,4])) {
                return ['httpcode'=> 400,'success'=>false,'message'=>'Enrollment status provided was not valid','data'=>[]];
            }
            if(is_null($transactionStatus) || !in_array($transactionStatus,[1,2])) {
                return ['httpcode'=> 400,'success'=> false,'message'=>'Transaction status provided is not valid','data'=>[]];
            }
            if(!$this->transactionsModel->updateNeededAction($transactionId,$transactionStatus)) {
                return ['httpcode'=> 400,'success'=> false,'message'=>'Transaction status failed to udpate','data'=>[]];
            }
            if(!$this->enrolleesModel->updateEnrollee($enrolleeId,$enrollmentStatus)) {
                return ['httpcode'=> 400,'success'=> false,'message'=>"Enrollee's enrollment status was not updated",'data'=>[]];
            }
            return ['httpcode'=> 200,'success'=> true,'message'=>"Enrollment transaction and enrollee's enrollment status was successfully updated",'data'=>[]];
        }
        catch(DatabaseException $e) {
            return [
                'httpcode'=> 500,
                'success'=> false,
                'message'=> 'There was a problem on our side.',
                'data'=>[]
            ];
        }
        catch(Exception $e){
            return [
                'httpcode'=> 400,
                'success'=> false,
                'message'=> 'There was an unexpected problem.',
                'data'=>[]
            ];
        }
    }
    public function apiFetchEnrolleeRemarks(?int $enrolleeId):array {
        try {
            if(is_null($enrolleeId)) {
                throw new IdNotFoundException('Enrollee ID not found');
            }
            $data = $this->transactionsModel->getEnrolleeTransaction($enrolleeId);
            if(empty($data)) {
                return [
                    'httpcode'=> 200,
                    'success'=> false,
                    'message'=> 'There were no remarks found',
                    'data'=> []
                ];
            }
            return [
                'httpcode'=> 200,
                'success'=> true,
                'message'=> 'Remarks successfully fetched',
                'data'=> $data
            ];
        }
        catch(DatabaseException $e) {
            return [
                'httpcode'=> 500,
                'success'=> false,
                'message'=> 'There was a problem on our side.',
                'data'=>[]
            ];
        }
        catch(Exception $e){
            return [
                'httpcode'=> 400,
                'success'=> false,
                'message'=> 'There was an unexpected problem.',
                'data'=>[]
            ];
        }
    }
    //HELPERS
    //VIEW
    public function viewMarkedEnrolledTransactions() : array {
        try {
            $data = $this->transactionsModel->getEnrolledTransactions();
            if(empty($data)) {
                return [
                    'success'=> false,
                    'message'=> 'No enrolled transactions found',
                    'data'=> []
                ];
            }
            return [
                'success'=> true,
                'message'=> 'Enrolled transactions successfully fetched',
                'data'=> $data
            ];
        }
        catch(DatabaseException $e) {
            return [
                'success'=> false,
                'message'=> 'There was a problem on our side: ' . $e->getMessage(),
                'data'=>[]
            ];
        }
        catch(Exception $e){
            return [
                'success'=> false,
                'message'=> 'There was an unexpected problem: ' . $e->getMessage(),
                'data'=>[]
            ];
        }
    }
    public function viewMarkedFollowedUpTransactions() : array {
        try {
            $data = $this->transactionsModel->getFollowedUpTransactions();
            if(empty($data)) {
                return [
                    'success'=> false,
                    'message'=> 'No followed up transactions found',
                    'data'=> []
                ];
            }
            return [
                'success'=> true,
                'message'=> 'Followed up transactions successfully fetched',
                'data'=> $data
            ];
        }
        catch(DatabaseException $e) {
            return [
                'success'=> false,
                'message'=> 'There was a problem on our side: ' . $e->getMessage(),
                'data'=>[]
            ];
        }
        catch(Exception $e){
            return [
                'success'=> false,
                'message'=> 'There was an unexpected problem: ' . $e->getMessage(),
                'data'=>[]
            ];
        }
    }
    public function viewMarkedDeniedTransactions() : array {
        try {
            $data = $this->transactionsModel->getDeniedTransactions();
            if(empty($data)) {
                return [
                    'success'=> false,
                    'message'=> 'No enrolled transactions found',
                    'data'=> []
                ];
            }
            return [
                'success'=> true,
                'message'=> 'Enrolled transactions successfully fetched',
                'data'=> $data
            ];
        }
        catch(DatabaseException $e) {
            return [
                'success'=> false,
                'message'=> 'There was a problem on our side: ' . $e->getMessage(),
                'data'=>[]
            ];
        }
        catch(Exception $e){
            return [
                'success'=> false,
                'message'=> 'There was an unexpected problem: ' . $e->getMessage(),
                'data'=>[]
            ];
        }
    }
    //GETTERS
    
}