<?php
declare(strict_types=1);
require_once __DIR__ . '/../models/adminEnrolleesModel.php';
require_once __DIR__ . '/../../core/tableDataTemplate.php';
require_once __DIR__ . '/../controller/adminEnrolleesController.php';
class adminEnrolleeInfo {
    protected $tableTemplate;
    protected $enrolleeId;
    protected $controller;
    //ENROLLEE PROPERTIES
    protected $personalInfo;
    protected $educInfo;
    protected $educBackground;
    protected $parentInfo;
    protected $disability;
    protected $address;
    protected $psaDIR;
    //GLOBAL ERROR FLAG
    protected $isGlobalError = false;
    protected $globalError = '';
    public function __construct() {
       $this->tableTemplate = new tableCreator();
       $this->enrolleeId = isset($_GET['id']) ? (int)$_GET['id'] : null;
       $this->controller = new adminEnrolleesController();
       $this->init();
    }
    private function init() {
        try {
            $response = $this->controller->viewEnrolleeInfo($this->enrolleeId);
            if(!$response['success']) {
                $this->isGlobalError = true;
                $this->globalError = $response['message'];
                return;
            }
            $this->personalInfo = $response['personal_info'];
            $this->educInfo = $response['educ_info'];
            $this->educBackground = $response['educ_bg'];
            $this->parentInfo = $response['parent_info'];
            $this->address = $response['address'];
            $this->disability = $response['disability_info'];
            $this->psaDIR = $response['psa_dir'];
        }
        catch(IdNotFoundException $e) {
            $this->isGlobalError = true;
            $this->globalError = $e->getMessage();
        }
    }
    public function displayGlobalError():void {
        if($this->isGlobalError) {
            echo '<div class="error-message">'.$this->globalError.'</div>';
        }
    }
    public function displayEnrolleePersonalInfo() {
        try {
            if($this->isGlobalError) {
                return;
            }
            $data = $this->personalInfo;
            $addr = $this->address;
            if(!$data['success'] || !$addr['success']) {
                $message = isset($data['message']) ? $data['message'] : $addr['message'];
                echo '<div class="error-message">'.$message.'</div>';
                return;
            }
            if(($data['success'] && empty($data['data'])) || ($addr['success'] && empty($addr['data']))) {
                $message = isset($data['message']) ? $data['message'] : $addr['message'];
                echo '<div class="error-message">'.$message.'</div>';
                return;
            }
            $rows = $data['data'];
            $address = $addr['data'];
            $culutralGroup = ($rows['If_Cultural'] == 1) ? $rows['Cultural_Group'] : 'Walang katutubong grupo';
            $lrn = !is_null($rows['Learner_Reference_Number']) ? $rows['Learner_Reference_Number']:"No LRN"  ; 
            $sex = !empty($rows['Sex']) ? $rows['Sex'] : 'No Biological sex provided';
            $completeAddress = $address['House_Number'] .' ' .$address['Subd_Name']
                    . '. ' .$address['Brgy_Name']. ', ' .$address['Municipality_Name'] . ', '
                    . $address['Province_Name'] . ' ' . $address['Region'];
            echo '<table class="modal-table"></tbody>';
            echo $this->tableTemplate->returnVerticalTables(
                ['Numero ng Sertipiko ng Kapanganakan','Learner Reference Number','Apelyido','Pangalan','Panggitna','Petsa ng Kapanganakan',
                'Edad','Kasarian', 'Kabilang sa katutubong grupo','Kinagisnang wika','Relihiyon','Email Address', 'Buong Address'],
                [$rows['Psa_Number'], $lrn,$rows['Student_Last_Name'],$rows['Student_First_Name'],$rows['Student_Middle_Name'],$rows['Birth_Date'],
                $rows['Age'], $sex, $culutralGroup,$rows['Native_Language'],$rows['Religion'],$rows['Student_Email'],$completeAddress],
                'personal-info'
            ); 
            echo '</tbody></table>';   
        }      
        catch(Throwable $t) {
            echo '<div class="error-message"> There was a syntax problem. Please wait for this to be fixed </div>';
        }
    }  
    public function displayEnrolleeEducationalInfo() {
        try {
            if($this->isGlobalError) {
                return;
            }
            $data = $this->educInfo;
            if(!$data['success']) {
                echo '<div class="error-message">'.$data['message'].'</div>';
                return;
            }
            if($data['success'] && empty($data['data'])) {
                echo '<div class="error-message">'.$data['message'].'</div>';
                return;
            }
            $rows = $data['data']; 
            $acadYear = $rows['School_Year_Start'] . '-' . $rows['School_Year_End'];
            $lastGradeLevel = !is_null($rows['L_Grade_Level']) ? $rows['L_Grade_Level'] : 'No Last Grade Level';
            echo '<table class="modal-table"></tbody>';
            echo $this->tableTemplate->returnVerticalTables(
                ['taong panuruan','Baitang na nais ipatala','Huling baitang na natapos','Huling natapos na taon'],
                [$acadYear,$rows['E_Grade_Level'],$lastGradeLevel,$rows['Last_Year_Attended']],
                'educational-info'
            );   
            echo '</tbody></table>';     
        }
        catch(Throwable $t) {
            echo '<div class="error-message"> There was a syntax problem. Please wait for this to be fixed </div>';
        }
    } 
    public function displayEnrolleeEducationalBackground() {
        try {
            if($this->isGlobalError) {
                return;
            }
            $data = $this->educBackground;
            if(!$data['success']) {
                echo '<div class="error-message">'.$data['message'].'</div>';
                return;
            }
            if($data['success'] && empty($data['data'])) {
                echo '<div class="error-message">'.$data['message'].'</div>';
                return;
            }
            $rows = $data['data'];
            echo '<table class="modal-table"></tbody>';
            echo $this->tableTemplate->returnVerticalTables(
                ['Huling Paaralan na Pinasukan', 'ID ng huling Paaralan', 'Address ng huling Paaralan',
                'Nais na Paaralan', 'ID ng nais na Paaralan', 'Address ng nais na Paaralan'],
                [$rows['Last_School_Attended'],$rows['School_Id'],$rows['School_Address'],
                $rows['Initial_School_Choice'],$rows['Initial_School_Id'],$rows['Initial_School_Address']],
                'educational-background'
            );
            echo '</tbody></table>';
        }
        catch(Throwable $t) {
            echo '<div class="error-message"> There was a syntax problem. Please wait for this to be fixed </div>';
        }
    }
    public function displayDisabledInfo() {
        try {
            if($this->isGlobalError) {
                return;
            }
            $data = $this->disability;
            if(!$data['success']) {
                echo '<div class="error-message">'.$data['message'].'</div>';
                return;
            }
            if($data['success'] && empty($data['data'])) {
                echo '<div class="error-message">'.$data['message'].'</div>';
                return;
            }
            $rows = $data['data'];
            $specialCondition = ($rows['Have_Special_Condition'] == 1 || !is_null($rows['Special_Condition'])) ? $rows['Special_Condition'] : 'None';
            $assistiveTech = ($rows['Have_Assistive_Tech'] == 1 || !is_null($rows['Assistive_Tech'])) ? $rows['Assistive_Tech'] : 'None';
            echo '<table class="modal-table"></tbody>';
            echo $this->tableTemplate->returnVerticalTables(
                ['May espesyal na kondisyon','Assistive technology'],
                [ $specialCondition,$assistiveTech],
                'disability-info'
            );
            echo '</tbody></table>';
        }
        catch(Throwable $t) {
            echo '<div class="error-message"> There was a syntax problem. Please wait for this to be fixed </div>';
        }
    }
    public function displayParentInfo() {
        try {
            if($this->isGlobalError) {
                return;
            }
            $data = $this->parentInfo;
            if(!$data['success']) {
                echo '<div class="error-message">'.$data['message'].'</div>';
                return;
            }
            if($data['success'] && empty($data['data'])) {
                echo '<div class="error-message">'.$data['message'].'</div>';
                return;
            }
            echo '<table class="modal-table"></tbody>';
            foreach($data['data'] as $rows) {
                $if4ps = ($rows['If_4Ps'] == 1) ? 'Oo' : 'Hindi';
                echo $this->tableTemplate->returnVerticalTables(
                    ['Relasyon', 'Apleyido','Pangalan','Panggitna','Educational attainment','Numero ng telepono', 'Kabilang sa 4ps'],
                    [$rows['Parent_Type'],$rows['Last_Name'] ,$rows['First_Name'] ,$rows['Middle_Name'],$rows['Educational_Attainment'],$rows['Contact_Number'],$if4ps],
                    'parent-info'
                );
            }
            echo '</tbody></table>';
        }
        catch(Throwable $t) {
            echo '<div class="error-message"> There was a syntax problem. Please wait for this to be fixed </div>';
        }
    }
    public function displayPsaImg() {
        try{
            if($this->isGlobalError) {
                return;
            }
            $data = $this->psaDIR;
            if(!$data['success']) {
                echo '<div class="error-message">'.$data['message'].'</div>';
                return;
            }
            if($data['success'] && empty($data['data'])) {
                echo '<div class="error-message">'.$data['message'].'</div>';
                return;
            }
            $imgDIR = !is_null($data['data']) ? '<img src="'.htmlspecialchars($data['data']).'" alt="PSA IMAGE">' : 'NO PSA IMAGE FOUND!';

            echo '<div class="img-container">'.$imgDIR.'</div>';
        }
        catch(Throwable $t) {
            echo '<div class="error-message"> There was a syntax problem. Please wait for this to be fixed </div>';
        }
    }
}