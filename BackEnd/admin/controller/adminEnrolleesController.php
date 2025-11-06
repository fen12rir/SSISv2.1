<?php
declare(strict_types=1);
require_once __DIR__ . '/../models/adminEnrollmentTransactionsModel.php';
require_once __DIR__ . '/../models/adminEnrolleesModel.php';
require_once __DIR__ . '/../../Exceptions/IdNotFoundException.php';
class adminEnrolleesController {
    protected $transactionsModel;
    protected $enrolleesModel;
    public function __construct() {
        $this->transactionsModel = new adminEnrollmentTransactionsModel();
        $this->enrolleesModel = new adminEnrolleesModel();
    }
    //API
    //HELPERS
    private function returnEnrolleePersonalInfo(int $enrolleeId):array {
        try {
            $data = $this->enrolleesModel->getEnrolleePersonalInformation($enrolleeId);
            if(empty($data)) {
                return ['success'=> true,'message'=>'Enrollee personal info not found','data'=>$data];
            }
            return ['success'=> true,'message'=>'Enrollee personal info successfully fetched','data'=>$data];
        }
        catch(DatabaseException $e) {
            return ['success'=> false,'message'=> 'There was a problem on our side','error_code'=> $e->getCode(),'error_message'=> $e->getPrevious()?->getMessage(),'data'=> []];
        }
        catch(Exception $e) {
            return ['success'=> false,'message'=> 'There was an unexpected problem: ' .$e->getMessage(), 'data'=> []];        
        }
    }
    private function returnEnrolleeEducationalInfo(int $enrolleeId):array {
        try {
            $data = $this->enrolleesModel->getEnrolleeEducationalInformation($enrolleeId);
            if(empty($data)) {
                return ['success'=> true,'message'=>'Enrollee educational info not found','data'=>$data];
            }
            return ['success'=> true,'message'=>'Enrollee educational info successfully fetched','data'=>$data];
        }
        catch(DatabaseException $e) {
            return ['success'=> false,'message'=> 'There was a problem on our side','error_code'=> $e->getCode(),'error_message'=> $e->getPrevious()?->getMessage(),'data'=> []];
        }
        catch(Exception $e) {
            return ['success'=> false,'message'=> 'There was an unexpected problem: ' .$e->getMessage(), 'data'=> []];        
        }
    }
    private function returnEnrolleeEducationalBackground(int $enrolleeId):array {
        try {
            $data = $this->enrolleesModel->getEnrolleeEducationalBackground($enrolleeId);
            if(empty($data)) {
                return ['success'=> true,'message'=>'Enrollee educational background not found','data'=>$data];
            }
            return ['success'=> true,'message'=>'Enrollee educational background successfully fetched','data'=>$data];
        }
        catch(DatabaseException $e) {
            return ['success'=> false,'message'=> 'There was a problem on our side','error_code'=> $e->getCode(),'error_message'=> $e->getPrevious()?->getMessage(),'data'=> []];
        }
        catch(Exception $e) {
            return ['success'=> false,'message'=> 'There was an unexpected problem: ' .$e->getMessage(), 'data'=> []];        
        }
    }
    private function returnEnrolleeDisablityInfo(int $enrolleeId):array {
        try {
            $data = $this->enrolleesModel->getEnrolleeDisabilityInformation($enrolleeId);
            if(empty($data)) {
                return ['success'=> true,'message'=>'Enrollee disability info not found','data'=>$data];
            }
            return ['success'=> true,'message'=>'Enrollee disability info successfully fetched','data'=>$data];
        }
        catch(DatabaseException $e) {
            return ['success'=> false,'message'=> 'There was a problem on our side','error_code'=> $e->getCode(),'error_message'=> $e->getPrevious()?->getMessage(),'data'=> []];
        }
        catch(Exception $e) {
            return ['success'=> false,'message'=> 'There was an unexpected problem: ' .$e->getMessage(), 'data'=> []];        
        }
    }
    private function returnEnrolleeAddress(int $enrolleeId):array {
        try {
            $data = $this->enrolleesModel->getEnrolleeAddress($enrolleeId);
            if(empty($data)) {
                return ['success'=> true,'message'=>'Enrollee addresss not found','data'=>$data];
            }
            return ['success'=> true,'message'=>'Enrollee address successfully fetched','data'=>$data];
        }
        catch(DatabaseException $e) {
            return ['success'=> false,'message'=> 'There was a problem on our side','error_code'=> $e->getCode(),'error_message'=> $e->getPrevious()?->getMessage(),'data'=> []];
        }
        catch(Exception $e) {
            return ['success'=> false,'message'=> 'There was an unexpected problem: ' .$e->getMessage(), 'data'=> []];        
        }
    }
    private function returnEnrolleeParentInfo(int $enrolleeId):array {
        try {
            $data = $this->enrolleesModel->getEnrolleeParentInformation($enrolleeId);
            if(empty($data)) {
                return ['success'=> true,'message'=>'Enrollee parent info not found','data'=>$data];
            }
            return ['success'=> true,'message'=>'Enrollee parent info successfully fetched','data'=>$data];
        }
        catch(DatabaseException $e) {
            return ['success'=> false,'message'=> 'There was a problem on our side','error_code'=> $e->getCode(),'error_message'=> $e->getPrevious()?->getMessage(),'data'=> []];
        }
        catch(Exception $e) {
            return ['success'=> false,'message'=> 'There was an unexpected problem: ' .$e->getMessage(), 'data'=> []];        
        }
    }
    private function returnPsaImageDir(int $enrolleeId):array {
        try {
            $data = $this->enrolleesModel->getPsaImg($enrolleeId);
            if(is_null($data)) {
                return ['success'=> true,'message'=> 'PSA image directory not found','data'=>$data];
            }
            return ['success'=> true,'message'=>'PSA image directory succesfully fetched','data'=>$data];
        }
        catch(DatabaseException $e) {
            return ['success'=> false,'message'=> 'There was a problem on our side','error_code'=> $e->getCode(),'error_message'=> $e->getPrevious()?->getMessage(),'data'=> []];
        }
        catch(Exception $e) {
            return ['success'=> false,'message'=> 'There was an unexpected problem: ' .$e->getMessage(), 'data'=> []];        
        }
    }
    //VIEW
    public function viewEnrolleeInfo(?int $enrolleeId) : array {
        try {
            if(is_null($enrolleeId)) {
                throw new IdNotFoundException('Enrollee ID not found');
            }
            $perInfo = $this->returnEnrolleePersonalInfo($enrolleeId);
            $eduInfo = $this->returnEnrolleeEducationalInfo($enrolleeId);
            $eduBg = $this->returnEnrolleeEducationalBackground($enrolleeId);
            $disInfo = $this->returnEnrolleeDisablityInfo($enrolleeId);
            $parInfo = $this->returnEnrolleeParentInfo($enrolleeId);
            $address = $this->returnEnrolleeAddress($enrolleeId);
            $psaDir = $this->returnPsaImageDir($enrolleeId);
            $isAllFalse = !$perInfo['success'] && !$eduInfo['sucess'] && !$eduBg['success'] && !$disInfo['success'] && !$parInfo['success'] 
            && !$address['success'];
            if($isAllFalse) {
                return [
                    'success'=> false,
                    'message'=> 'Getting enrollee information failed'
                ];
            }
            return [
                'success'=> true,
                'personal_info'=> $perInfo,
                'educ_info'=> $eduInfo,
                'educ_bg'=> $eduBg,
                'disability_info'=>$disInfo,
                'parent_info'=>$parInfo,
                'address'=>$address,
                'psa_dir'=>$psaDir
            ];
        }
        catch(DatabaseException $e) {
            return ['success'=> false,'message'=> 'There was a problem on our side','error_code'=> $e->getCode(),'error_message'=> $e->getPrevious()?->getMessage(),'data'=> []];
        }
        catch(Exception $e) {
            return ['success'=> false,'message'=> 'There was an unexpected problem: ' .$e->getMessage(), 'data'=> []];        
        }
    }
    public function viewAllEnrollmentTransactions() : array { // 1.7.1
        try {
            $data = $this->transactionsModel->getAllEnrolleeTransactionsInformation();
            return [
                'success'=> true,
                'message'=> !empty($data) ? 'Transactions successfully fetched' : 'Transactions are empty',
                'data'=> $data
            ];
        }
        catch(DatabaseException $e) {
            return [
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
                'message'=> 'There was an unexpected problem: ' .$e->getMessage(),
                'data'=> []
            ];
        }
    }
    public function viewTransactionsCount() : array { //F 1.7.2
        try {
            $data = $this->transactionsModel->getAllEnrolleeTransactionsCount();
            return [
                'success'=> true,
                'message'=> 'Transactions successfully counted',
                'data'=> $data
            ];
        }
        catch(DatabaseException $e) {
            return [
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
                'message'=> 'There was an unexpected problem: ' .$e->getMessage(),
                'data'=> []
            ];
        }
    }
}