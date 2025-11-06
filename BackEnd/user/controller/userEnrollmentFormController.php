<?php
declare(strict_types=1);
require_once __DIR__ . '/../models/userPostEnrollmentFormModel.php';
require_once __DIR__ . '/../models/userEnrolleesModel.php';
require_once __DIR__ . '/../../Exceptions/DatabaseException.php';

class userEnrollmentFormController {
    protected $postFormModel;
    protected $enrolleesModel;

    public function __construct() {
        $this->postFormModel = new userPostEnrollmentFormModel();
        $this->enrolleesModel = new userEnrolleesModel();
    }
    //API
    public function apiPostAddEnrollee(int $uId,int $schoolYStart,int $schoolYEnd,int $hasLRN, int $enrollGLevel,?int $lastGLevel,?int $lastYAttended, 
    string $lastSAttended,int $sId,string $sAddress,string $sType, string $initalSChoice, int $initialSId,string $initialSAddrress
    ,int $hasSpecialCondition,int $hasAssistiveTech,?string $specialCondition,?string $assistiveTech,
    int $hNumber,string $subdName,string $bName,int $bCode,string $mName,int $mCode,string $pName,int $pCode, string $rName, int $rCode,
    string $fFName,string $fLName,?string $fMName,string $fEduAttainment,string $fCpNum, int $fIs4Ps,
    string $mFName,string $mLName,?string $mMName,string $mEduAttainment,string $mCpNum, int $mIs4Ps,
    string $gFName,string $gLName,?string $gMName,string $gEduAttainment,string $gCpNum, int $gIs4Ps,
    string $stuFName,string $stuLName,?string $stuMName,?string $stuSuffix,?int $lrn,int $psaNum, string $birthDate,
    int $age,string $sex,string $religion,string $natLang,int $isCultural,?string $culturalG, string $studentEmail, int $enrollStat,
    ?array $psaImageFile) : array { //F 3.5.1
        try {
            if($hasLRN === 1 && empty($lrn)) {
                return [
                    'httpcode'=>400,
                    'success'=> false,
                    'message'=> 'LRN cannot be empty if not returning or a new student',
                    'data'=> []
                ];
            }
            $isMatchingLrn = $this->postFormModel->checkLRN($lrn);
            $isMatchingPsa = $this->postFormModel->checkPSA($psaNum);
            if($isMatchingLrn) {
                return [
                    'httpcode'=> 400,
                    'success'=> false,
                    'message'=> 'LRN provided already exists',
                    'data'=> []
                ];
            }
            if($isMatchingPsa) {
                return [
                    'httpcode'=> 400,
                    'success'=> false,
                    'message'=> 'PSA number provided already exists',
                    'data'=> []
                ];
            }
            $currentYear = date('Y');
            if (($SchoolYStart < $currentYear || $SchoolYStart > ($currentYear + 1)) || ($SchoolYEnd <= $SchoolYStart || $SchoolYEnd > ($currentYear + 2)) ) {
                return [
                    'httpcode'=>400,
                    'success'=> false,
                    'message'=> 'Invalid academic year format',
                    'data'=> []
                ];
            }
            if ($LastYAttended > $currentYear) {
                return [
                    'httpcode'=>400,
                    'success'=> false,
                    'message'=> 'Last year attended is greater than current year. Please change it',
                    'data'=> []
                ];
            }
            if($hasSpecialCondition === 1 && empty($specialCondition)) {
                return [
                    'httpcode'=>400,
                    'success'=> false,
                    'message'=> 'Special condition not specified',
                    'data'=> []
                ];
            }
            if($hasAssistiveTech === 1 && empty($assistiveTech)) {
                return [
                    'httpcode'=> 400,
                    'success'=> false,
                    'message'=> 'Assistive technology not specified',
                    'data'=> []
                ];
            }
            if(empty($fFName) || empty($fLName) || empty($mFName) || empty($mLName) || empty($gFName) || empty($gLName)) {
                return [
                    'httpcode'=> 400,
                    'success'=> false,
                    'message'=> 'Make sure all first names and last names in parent information are not empty. Check Again',
                    'data'=> []
                ];
            }
            if(empty($stuFName) || empty($stuLName)) {
                $isFirstName = empty($stuFName) ? 'first name' : 'last name';
                return [
                    'httpcode'=> 400,
                    'success'=> false,
                    'message'=> 'Enrollee'.$isFirstName.'cannnot be empty. Please try again',
                    'data'=> []
                ];
            }
            $saveImage = $this->storeImage($uId, $psaImageFile);
            if(!$saveImage['success']) {
                return [
                    'httpcode'=> 400,
                    'success'=> false,
                    'message'=> $saveImage['message'],
                    'data'=> []
                ];
            }
            //get diretory if success is true
            $filename = $saveImage['filename'];
            $filePath = $saveImage['filepath'];
            //attempt enrollee insert
            $insertEnrollee = $this->postFormModel->insert_enrollee($uId, $schoolYStart,$schoolYEnd,$hasLRN,$enrollGLevel,$lastGLevel,$lastYAttended,
            $lastSAttended,$sId,$sAddress,$sType,$initalSChoice,$initialSId,$initialSAddrress,
            $hasSpecialCondition,$hasAssistiveTech,$specialCondition,$assistiveTech,
            $hNumber,$subdName,$bName,$bCode,$mName,$mCode,$pName,$pCode,$rName,$rCode,
            $fFName,$fLName,$fMName,$fEduAttainment,$fCpNum,$fIs4Ps,
            $mFName,$mLName,$mMName,$mEduAttainment,$mCpNum,$mIs4Ps,
            $gFName,$gLName,$gMName,$gEduAttainment,$gCpNum,$gIs4Ps,
            $stuFName,$stuLName,$stuMName,$stuSuffix,$lrn,$psaNum,$birthDate,$age,$sex,$religion,
            $natLang,$isCultural,$culturalG,$studentEmail,$enrollStat,$filename,$filePath);
            if($insertEnrollee) {
                return [
                    'httpcode'=> 201,
                    'success'=> true,
                    'message'=> 'Enrollment form submitted successfully',
                    'data'=> []
                ];
            }
            else {
                return [
                    'httpcode'=>500,
                    'success'=> false,
                    'message'=> 'Enrollment form failed to submit',
                    'data'=> []
                ];
            }
        }
        catch(DatabaseException $e) {
            return [
                'httpcode'=>500,
                'success'=> false,
                'message'=> 'There was a problem on our side: ' .$e->getMessage(),
                'error_code'=> $e->getCode(),
                'error_message'=> $e->getPrevious()->getMessage(),
                'data'=> []
            ];
        }
        catch(Exception $e) {
            return [
                'httpcode'=> 500,
                'success'=> false,
                'message'=> 'There was an unexpected problem: ' .$e->getMessage(),
                'data'=> []
            ];
        }
    }
    public function apiUpdateEnrolleeInfo(int $userId, ?array $formData) : array { //F 3.5.2
        try {
            $enrolleeId = !empty($formData['enrolleeId']) ? (int) $formData['enrolleeId'] : null;
            if(is_null($formData['enrolleeId'])) {
                return [
                    'httpcode'=> 400,
                    'success'=> false,
                    'message'=> 'Enrollee ID is invalid',
                    'data'=> []
                ];
            }
            if(empty($formData)) {
                return [
                    'httpcode'=> 500,
                    'success'=> false,
                    'message'=> 'The form recieved is empty',
                    'data'=> []
                ];
            }
            $allData = $this->associateFormData($formData);
            if(empty($allData)) {
                return [
                    'httpcode'=> 500,
                    'success'=> false,
                    'message'=> 'Failed to process form data',
                    'data'=> []
                ];
            }
            $requiredFields = ['first_name', 'last_name', 'lrn', 'birthdate', 'sex'];
            $missingFields = [];
            foreach ($requiredFields as $field) {
                if (is_null($allData[$field])) {
                    $missingFields[] = $field;
                }
            }
            if(!empty($missingFields)) {
                return [
                    'httpcode'=> 409,
                    'success'=> false,
                    'message'=> 'Required fields are misssing',
                    'data'=> []
                ];
            }
            $isMatchingLrn = $this->postFormModel->checkLRN($allData['lrn'],$enrolleeId);
            $isMatchingPsa = $this->postFormModel->checkPSA($allData['psa'], $enrolleeId);
            $psaImage = $formData['psa_image'] ?? null;
            $psaData = $this->updateImage($userId, $enrolleeId, $psaImage);
            if(!$psaData['success']) {
                return [
                    'httpcode'=> 500,
                    'success'=> false,
                    'message'=> $psaData['message']. 'is this directory modifiable: ' .$psaData['isWritable'],
                    'data'=> []
                ];
            }
            if($psaData['isUpload'] ?? true) {
                $allData['psa_image'] = [
                    'filename'=> $psaData['filename'],
                    'filepath'=> $psaData['filepath']
                ];   
            }
            $insertData = $this->enrolleesModel->updateEnrolleeInformation($enrolleeId, $allData);
            if(!$insertData) {
                return [
                    'httpcode'=> 500,
                    'success'=> false,
                    'message'=> 'Failed to execute enrollee update',
                    'data'=> []
                ];
            }
            $setTransactionStatus = $this->enrolleeModel->setResubmitStatus($enrolleeId);
            return [
                'httpcode'=> 201,
                'success'=> true,
                'message'=> 'Successfully updated enrollee information',
                'data'=> $insertData
            ];
        }
        catch(DatabaseException $e) {
            return [
                'httpcode'=>500,
                'success'=> false,
                'message'=> 'There was a problem on our side: ' .$e->getMessage(),
                'error_code'=> $e->getCode(),
                'error_message'=> $e->getPrevious()->getMessage(),
                'data'=> []
            ];
        }
        catch(Exception $e) {
            return [
                'httpcode'=> 500,
                'success'=> false,
                'message'=> 'There was an unexpected problem: ' .$e->getMessage(),
                'data'=> []
            ];
        }
    }
    //HELPERS
    private function associateFormData(?array $postData) : array { //F 3.6.1
        try {
            $lrn = isset($postData['lrn']) ? (int) trim($postData['lrn']) : null;
            $psa = isset($postData['psa']) ? (int) trim($postData['psa']) : null;
            $formData = [
                'first_name' => $postData['first_name'] ?? null,
                'last_name' => $postData['last_name'] ?? null,
                'middle_name' => $postData['middle_name'] ?? null,
                'extension' => $postData['extension'] ?? null,
                'lrn' => $lrn,  // Add trim to remove any whitespace
                'psa' =>$psa,  // Add trim to remove any whitespace
                'age' => $postData['age'] ?? null,
                'birthdate' => $postData['birthdate'] ?? null,
                'sex' => $postData['sex'] ?? null,
                'religion' => $postData['religion'] ?? null,
                'native_language' => $postData['native_language'] ?? null,
                'belongs_in_cultural_group' => $postData['belongs_in_cultural_group'] ?? 0,
                'cultural_group' => $postData['cultural_group'] ?? null,
                'email_address' => $postData['email_address'] ?? null,
                'enrolling_grade_level' => $postData['enrolling_grade_level'] ?? null,
                'last_grade_level' => $postData['last_grade_level'] ?? null,
                'last_year_attended' => $postData['last_year_attended'] ?? null,
                'last_school_attended' => $postData['last_school_attended'] ?? null,
                'school_id' => $postData['school_id'] ?? null,
                'school_address' => $postData['school_address'] ?? null,
                'school_type' => $postData['school_type'] ?? null,
                'region' => $postData['region'] ?? null,
                'region_name' => $postData['region_name'] ?? null,
                'province' => $postData['province'] ?? null,
                'province_name' => $postData['province_name'] ?? null,
                'city-municipality' => $postData['city-municipality'] ?? null,
                'city_municipality_name' => $postData['city_municipality_name'] ?? null,
                'barangay' => $postData['barangay'] ?? null,
                'barangay_name' => $postData['barangay_name'] ?? null,
                'subdivision' => $postData['subdivision'] ?? null,
                'house_number' => $postData['house_number'] ?? null,
                'has_a_special_condition' => $postData['has_a_special_condition'] ?? 0,
                'special_condition' => $postData['special_condition'] ?? null,
                'has_assistive_technology' => $postData['has_assistive_technology'] ?? 0,
                'assistive_technology' => $postData['assistive_technology'] ?? null
            ];
            // Add parent information
            $formData['parent_information'] = [
                'Father' => [
                    'first_name' => $postData['father_first_name'] ?? null,
                    'middle_name' => $postData['father_middle_name'] ?? null,
                    'last_name' => $postData['father_last_name'] ?? null,
                    'educational_attainment' => $postData['father_educational_attainment'] ?? null,
                    'contact_number' => $postData['father_contact_number'] ?? null,
                    'if_4ps' => $postData['father_4ps_member'] ?? 0
                ],
                'Mother' => [
                    'first_name' => $postData['mother_first_name'] ?? null,
                    'middle_name' => $postData['mother_middle_name'] ?? null,
                    'last_name' => $postData['mother_last_name'] ?? null,
                    'educational_attainment' => $postData['mother_educational_attainment'] ?? null,
                    'contact_number' => $postData['mother_contact_number'] ?? null,
                    'if_4ps' => $postData['mother_4ps_member'] ?? 0
                ],
                'Guardian' => [
                    'first_name' => $postData['guardian_first_name'] ?? null,
                    'middle_name' => $postData['guardian_middle_name'] ?? null,
                    'last_name' => $postData['guardian_last_name'] ?? null,
                    'educational_attainment' => $postData['guardian_educational_attainment'] ?? null,
                    'contact_number' => $postData['guardian_contact_number'] ?? null,
                    'if_4ps' => $postData['guardian_4ps_member'] ?? 0
                ]
            ];
            return $formData;
        }
        catch(Exception $e) {
            return [
                'success'=> false,
                'message'=> 'There was an unexpected problem'
            ];
        }
    }
    private function updateImage(int $userId, int $enrolleeId, ?string $filename) : array { //F 3.6.2
        try {
            if(empty($filename)) {
                return [
                    'success'=> true,
                    'message'=> 'No image sent; will not replace old one',
                    'filename'=> null,
                    'filepath'=> null,
                    'isUpload'=> false
                ];
            }
            $relPath = '../../../ImageUploads/'.date('Y').'/';
            $uploadDirectory = __DIR__ . '/'. $relPath;
            if(!is_dir($uploadDirectory)) {
                if(!mkdir($uploadDirectory,0777,true)) {
                    return [
                        'success'=> false,
                        'message'=> 'Failed to submit image. Image folder storage not found'
                    ];
                }
            }
            $base64 = preg_replace('#^data:image/\w+;base64,#i', '', $filename);
            $base64 = str_replace(' ', '+', $base64);
            $imageData = base64_decode($base64, true);
            $imageInfo = getimagesizefromstring($imageData);
            $allowedFileTypes = ['image/jpeg','image/png'];
            if(!$imageInfo) {
                return [
                    'success'=> false,
                    'message'=> 'Not an image'
                ];
            }
            if(!in_array( $imageInfo['mime'],$allowedFileTypes)) {
                return [
                    'success'=> false,
                    'message'=> 'File type not allowed. Must be jpg, jpeg, or png'
                ];
            }
            $extension = ($imageInfo['mime'] === 'image/png') ? 'png' :'jpg';
            $time = time();
            $imageRandomString = bin2hex(random_bytes(5));
            $uniqName = "{$userId}-{$time}-{$imageRandomString}.{$extension}"; //filename
            $filepath = $relPath . $uniqName;
            if(!file_put_contents($filepath, $imageData)) {
                return [
                    'success'=> false,
                    'message'=> 'Failed to send image to file'
                ];
            }
            $oldImage = $this->enrolleesModel->getPSAImageData($enrolleeId); //get old image data
            if(!empty($oldImage) && isset($oldImage['directory']) && file_exists($oldImage['directory'])) {
                if(!@unlink($oldImage['directory'])) {
                    error_log("Failed to delete filename: " .$oldImage['directory'] . "\n",3, __DIR__ . '/../../errorLogs.txt');
                }
            }
            return [
                'success'=> true,
                'message'=> 'Image file successfully saved',
                'filename'=> $uniqName,
                'filepath'=> $filepath,
                'isUpload'=> true
            ];
        }
        catch(DatabaseException $e) {
            return [
                'success'=> false,
                'message'=> 'There was a problem during image data fetch: ' .$e->getMessage(),
                'error_code'=> $e->getCode(),
                'error_message'=> $e->getPrevious()->getMessage()
            ];
        }
        catch(Exception $e) {
            return [
                'success'=> false,
                'message'=> 'There was an unexpected problem in updating image' 
            ];
        }
    }
    private function storeImage(int $userId, ?array $fileName) : array { //F 3.6.3
        try {
            if(empty($fileName)) {
                return [
                    'success'=> false,
                    'message'=> 'PSA image file empty'
                ];
            }
            if(!isset($fileName['name']) || !isset($fileName['tmp_name']) || !isset($fileName['type'])) {
                return [
                    'success'=> false,
                    'message'=> 'file sent is not an image'
                ];
            }
            if (!isset($fileName['error']) || $fileName['error'] !== UPLOAD_ERR_OK) {
                return [
                    'success'=> false,
                    'message'=> 'Error during file upload'
                ];
            }
            //handle directory
            $relPath = '../../../ImageUploads/'.date('Y').'/'; // use relative path for database for now
            $uploadDirectory = __DIR__ . '/'. $relPath;
            if(!is_dir($uploadDirectory)) {
                if(!mkdir($uploadDirectory,0777,true)) {
                    return [
                        'success'=> false,
                        'message'=> 'Failed to submit image. Image folder storage not found'
                    ];
                }
            }     
            //extract file name
            $image = $fileName['name'];
            $imageTmpName = $fileName['tmp_name'];
            //check valid file types
            $extractImageExtension = explode('.',$image);
            $imageExtension = strtolower(end($extractImageExtension));
            $allowedFileTypes = ['jpg','jpeg','png'];
            if(!in_array($imageExtension,$allowedFileTypes)) {
                return [
                    'success'=> false,
                    'message'=> 'File type not allowed. Must be jpg, jpeg, or png'
                ];
            }
            $imageTime = time();
            $imageRandString = bin2hex(random_bytes(5));
            $imageCustomFileName = $userId .'-'. $imageTime.'-'.$imageRandString;
            //db stored values
            $imageFileName = $imageCustomFileName .'.'. $imageExtension;
            $imageFilePath = $relPath . $imageFileName;
            if(!move_uploaded_file($imageTmpName, $imageFilePath)) {
                return [
                    'success'=> false,
                    'message'=> 'Failed to store the image to directory'
                ];
            }
            return [
                'success'=> true,
                'message'=> 'Image stored successfully',
                'filename'=> $imageFileName,
                'filepath'=> $imageFilePath
            ];
        }
        catch(Exception $e) {
            return [
                'success'=> false,
                'message'=> 'There was an unexpected problem in storing the image'
            ];
        }
    }
    private function checkValidEmails() : bool {
        
    }
    //VIEW
}