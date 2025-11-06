<?php 

declare(strict_types=1);
require_once __DIR__ . '/../controller/adminSectionsController.php';
require_once __DIR__ . '/../controller/adminViewSectionController.php';
require_once __DIR__ . '/../../core/tableDataTemplate.php';

class adminViewSectionView {

    protected $conn;
    protected $sectionsController;
    protected $viewSectionController;
    protected $tableTemplate;
    protected $id;

    public function __construct() {
        $db = new Connect();
        $this->tableTemplate = new TableCreator();
        $this->conn = $db->getConnection();
        $this->sectionsController = new adminSectionsController();
        $this->viewSectionController = new adminViewSectionController();
        if(isset($_GET['section_id'])) $this->id = (int)$_GET['section_id'];
    }

    public function displaySectionStudents() {
        try {
            $response = $this->sectionsController->viewSectionDetails($this->id);

            if(!$response['success']) {
                echo '<table><tr class="error-message">
                        <td>'.$response['message'].'</td>
                    </tr></table>';
            }
            else {
                $maleData = $response['data']['male'];
                $femaleData = $response['data']['female'];
                if(!$maleData['success']) {
                    echo '<td>' .htmlspecialchars($maleData['message']).'</td>';
                }
                if(!$femaleData['success']) {
                    echo '<td>'.htmlspecialchars($femaleData['message']).'</td>';
                }
                $maleCount   =  count($maleData['students']);
                $femaleCount = count($femaleData['students']);
                $higherIndex = max($maleCount, $femaleCount);
                if($higherIndex > 0) {
                    echo '<table class="students-list">';
                    echo "<tr><thead><th>Male</th><th>Female</th></thead></tr>";
                    echo '<tbody>';
                    for ($i = 0; $i < $higherIndex; $i++) {
                        echo '<tr>';
                        echo $this->renderCell($maleData['students'][$i] ?? null, $i);
                        echo $this->renderCell($femaleData['students'][$i] ?? null, $i);
                        echo '</tr>';
                    }
                    echo '</tbody></table>';
                }
            }
        }
        catch(Exception $e) {
            echo '<table><tr class="error-message">
                <td>'.$e->getMessage().'</td>
            </tr></table>';
        }
    }
    private function renderCell($student, $index) {
        $lastName =  htmlspecialchars($student['Student_Last_Name'] ?? '');
        $firstName = htmlspecialchars($student['Student_First_Name'] ?? '');
        $middleName =  htmlspecialchars($student['Student_Middle_Name'] ?? '');

        $fullName = '<span>'. ($index+1) . '. </span>' . $lastName .', '. $firstName .' '. $middleName;

        $isNotNullName = (!empty($lastName) || !empty($firstName)) ? $fullName : '';
        echo '<td>' 
                 . $isNotNullName .
            '</td>';
    }
    public function displaySectionName() {
        try {
            $sectionName = $this->sectionsController->viewSectionName($this->id);

            if(!$sectionName['success']) {
                echo $sectionName['message'];
            }
            else {
                echo htmlspecialchars($sectionName['data']['Grade_Level']) . ' ' . htmlspecialchars($sectionName['data']['Section_Name']);
            }
        }
        catch(Exception  $e) {
            echo 'Something went wrong. Please try again Later.';
        }
    }
    public function displayAdviserName() {
        try {
            $adviserName = $this->sectionsController->viewAdviserName($this->id);

            if(!$adviserName['success']) {
                echo 'No Adviser assigned';
            }
            else {
                $lastName = htmlspecialchars($adviserName['data']['Staff_Last_Name'] ?? '');
                $firstName = htmlspecialchars($adviserName['data']['Staff_First_Name'] ?? '');
                $middleName = htmlspecialchars($adviserName['data']['Staff_Middle_Name'] ?? '');
                $title = !empty($middleName) && strpos(strtolower($middleName), 'mrs') !== false ? 'Mrs.' : 'Mr.';
                echo $title . ' ' . $firstName . ' ' . $lastName;
            }

        }
        catch(Exception  $e) {
            echo 'Something went wrong. Please try again Later.';
        }
    }
    public function displaySubjectDetails() {
        try {
            $subjectDetails = $this->viewSectionController->viewSectionSubjectDetails($this->id);

            if(!$subjectDetails['success']) {
                echo '<p class="no-data">' . htmlspecialchars($subjectDetails['message']) . '</p>';
            }
            else {
                echo '<table class="subject-details-table">';
                echo $this->tableTemplate->returnHorizontalTitles([
                    'Subject',
                    'Teacher'
                ],
                'subject-details-head');
                echo '<tbody>';
                foreach($subjectDetails['data'] as $subjects) {
                    $subjectName = !empty($subjects['Subject_Name']) ? (string)$subjects['Subject_Name'] : 'No subject name';
                    $teacherName = !empty($subjects['Staff_Last_Name']) && !empty($subjects['Staff_First_Name']) 
                        ? (string)$subjects['Staff_Last_Name'] . ', ' . (string)$subjects['Staff_First_Name'] 
                        : 'No teacher assigned';

                    echo $this->tableTemplate->returnHorizontalRows([
                        $subjectName, $teacherName
                    ],'subject-details-body');
                }
                echo '</tbody>';
                echo '</table>';
                echo '<p class="subjects-summary">' . count($subjectDetails['data']) . ' subjects total</p>';
            }
        }
        catch(Exception $e) {
            echo '<p class="no-data">There was an unexpected problem. Please try again later.</p>';
        }
    }
    
    public function displaySectionStats() {
        try {
            $stats = $this->sectionsController->viewSectionStats($this->id);
            
            if(!$stats['success'] || empty($stats['data'])) {
                echo '<div class="stats-cards">';
                echo '<div class="stat-card"><div class="stat-icon students-icon"></div><div class="stat-value">0</div><div class="stat-label">In this section</div></div>';
                echo '<div class="stat-card"><div class="stat-icon boys-icon">👦</div><div class="stat-value">0</div><div class="stat-label">Male students</div></div>';
                echo '<div class="stat-card"><div class="stat-icon girls-icon">👧</div><div class="stat-value">0</div><div class="stat-label">Female students</div></div>';
                echo '<div class="stat-card"><div class="stat-icon subjects-icon">📚</div><div class="stat-value">0</div><div class="stat-label">Classes per week</div></div>';
                echo '</div>';
                return;
            }
            
            $data = $stats['data'];
            $totalStudents = (int)($data['Total_Students'] ?? 0);
            $boys = (int)($data['Boys'] ?? 0);
            $girls = (int)($data['Girls'] ?? 0);
            $subjects = (int)($data['Total_Subjects'] ?? 0);
            
            echo '<div class="stats-cards">';
            echo '<div class="stat-card"><div class="stat-icon students-icon"></div><div class="stat-value">' . $totalStudents . '</div><div class="stat-label">In this section</div></div>';
            echo '<div class="stat-card"><div class="stat-icon boys-icon">👦</div><div class="stat-value">' . $boys . '</div><div class="stat-label">Male students</div></div>';
            echo '<div class="stat-card"><div class="stat-icon girls-icon">👧</div><div class="stat-value">' . $girls . '</div><div class="stat-label">Female students</div></div>';
            echo '<div class="stat-card"><div class="stat-icon subjects-icon">📚</div><div class="stat-value">' . $subjects . '</div><div class="stat-label">Classes per week</div></div>';
            echo '</div>';
        }
        catch(Exception $e) {
            echo '<div class="stats-cards"><p>Error loading stats</p></div>';
        }
    }
    
    public function displayAllStudentsTable() {
        try {
            $response = $this->sectionsController->viewSectionAllStudents($this->id);
            
            if(!$response['success'] || empty($response['data'])) {
                echo '<p class="no-data">No students found in this section.</p>';
                return;
            }
            
            $students = $response['data'];
            $totalStudents = count($students);
            $displayLimit = 6; // Show 6 students per page as in the image
            $displayedStudents = array_slice($students, 0, $displayLimit);
            
            echo '<table class="students-table">';
            echo $this->tableTemplate->returnHorizontalTitles([
                'Name',
                'Email',
                'LRN',
                'Status'
            ], 'students-table-head');
            echo '<tbody>';
            
            foreach($displayedStudents as $student) {
                $lastName = htmlspecialchars((string)($student['Student_Last_Name'] ?? ''));
                $firstName = htmlspecialchars((string)($student['Student_First_Name'] ?? ''));
                $middleName = htmlspecialchars((string)($student['Student_Middle_Name'] ?? ''));
                $fullName = trim($lastName . ', ' . $firstName . ' ' . $middleName);
                
                $email = htmlspecialchars((string)($student['Student_Email'] ?? 'N/A'));
                $lrn = htmlspecialchars((string)($student['Learner_Reference_Number'] ?? 'N/A'));
                
                // Map numeric status to text
                $statusNum = (int)($student['Student_Status'] ?? 1);
                $statusLabels = [
                    1 => 'Active',
                    2 => 'Inactive',
                    3 => 'Dropped'
                ];
                $statusText = $statusLabels[$statusNum] ?? 'Unknown';
                $statusClass = 'status-' . strtolower($statusText);
                
                echo '<tr>';
                echo '<td>' . $fullName . '</td>';
                echo '<td>' . $email . '</td>';
                echo '<td>' . $lrn . '</td>';
                echo '<td><span class="status-badge ' . $statusClass . '">' . htmlspecialchars($statusText) . '</span></td>';
                echo '</tr>';
            }
            
            echo '</tbody>';
            echo '</table>';
            echo '<p class="students-summary">Showing ' . count($displayedStudents) . ' of ' . $totalStudents . ' students</p>';
        }
        catch(Exception $e) {
            echo '<p class="no-data">Error loading students: ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
    }
}