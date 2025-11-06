<?php

declare(strict_types =1);

require_once __DIR__ .'/../core/dbconnection.php';
require_once __DIR__ . '/../Exceptions/DatabaseException.php';

class getGradeLevels {
    protected $conn;
    
    public function __construct() {
        $db = new Connect();
        $this->conn = $db->getConnection();
    }
    //MODEL
    private function gradeLevelquery() : array {
        try {
            $sql = "SELECT * FROM grade_level";
            $stmt = $this->conn->prepare($sql);
            $stmt-> execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;
        }
        catch(PDOException $e) {
            throw new DatabaseException('Failed to fetch grade levels');
        }
    }
    //CONTROLLER
    private function returnValues() : array {
        try {
            $data = $this->gradeLevelQuery();

            if(empty($data)) {
                return [
                    'success'=> false,
                    'message'=> 'Grade levels are empty',
                    'data'=> []
                ];
            }
            return [
                'success'=> true,
                'message'=> 'Grade levels successfully fetched',
                'data'=> $data
            ];
        }
        catch(DatabaseConnectionException $e) {
            return [
                'success'=> false,
                'message'=> 'Database error: ' . $e->getMessage(),
                'data'=>[
                    ['label'=> 'Kinder I', 'value'=>1],
                    ['label'=> 'Kinder II', 'value'=>2],
                    ['label'=> 'Grade 1', 'value'=>3],
                    ['label'=> 'Grade 2', 'value'=>4],
                    ['label'=> 'Grade 3', 'value'=>5],
                    ['label'=> 'Grade 4', 'value'=>6],
                    ['label'=> 'Grade 5', 'value'=>7],
                    ['label'=> 'Grade 6', 'value'=>8],
                ]
            ]; 
        }
        catch(DatabaseException $e) {
            return [
                'success'=> false,
                'message'=> 'Database error: ' . $e->getMessage(),
                'data'=>[
                    ['label'=> 'Kinder I', 'value'=>1],
                    ['label'=> 'Kinder II', 'value'=>2],
                    ['label'=> 'Grade 1', 'value'=>3],
                    ['label'=> 'Grade 2', 'value'=>4],
                    ['label'=> 'Grade 3', 'value'=>5],
                    ['label'=> 'Grade 4', 'value'=>6],
                    ['label'=> 'Grade 5', 'value'=>7],
                    ['label'=> 'Grade 6', 'value'=>8],
                ]
            ];        
        }
        catch(Exception $e) {
            return [
                'success'=> false,
                'message'=>'Error: '.$e->getMessage(),
                'data'=> [
                    ['label'=> 'Kinder I', 'value'=>1],
                    ['label'=> 'Kinder II', 'value'=>2],
                    ['label'=> 'Grade 1', 'value'=>3],
                    ['label'=> 'Grade 2', 'value'=>4],
                    ['label'=> 'Grade 3', 'value'=>5],
                    ['label'=> 'Grade 4', 'value'=>6],
                    ['label'=> 'Grade 5', 'value'=>7],
                    ['label'=> 'Grade 6', 'value'=>8],
                ]
            ];
        }
        catch(Throwable $t) {
            return [
                'success'=> false,
                'message'=>'Error: '.$t->getMessage(),
                'data'=> [
                    ['label'=> 'Kinder I', 'value'=>1],
                    ['label'=> 'Kinder II', 'value'=>2],
                    ['label'=> 'Grade 1', 'value'=>3],
                    ['label'=> 'Grade 2', 'value'=>4],
                    ['label'=> 'Grade 3', 'value'=>5],
                    ['label'=> 'Grade 4', 'value'=>6],
                    ['label'=> 'Grade 5', 'value'=>7],
                    ['label'=> 'Grade 6', 'value'=>8],
                ]
            ];
        }
    }
    public function createSelectValues() {
        try {
            $data = $this->returnValues();
            if(!$data['success']) {
                echo '<p>' .$data['message']. '</p>';
                foreach($data['data'] as $label => $value) {
                    echo '<option value='.$value.'>' .htmlspecialchars($label). '</option>';
                }
            }
            else {
                $gradeLevels = $data['data'];
                foreach($gradeLevels as $rows) {
                    echo '<option value=' . $rows['Grade_Level_Id'] .'>' . 
                    htmlspecialchars($rows['Grade_Level'])  . '</option>';
                }
            }
        }
        catch(DatabaseConnectionException $e) {
            echo '<div class="error-mesage">'.htmlspecialchars($e->getMessage()).'</div>';
        }
        catch(DatabaseException $e) {
            echo '<div class="error-mesage">'.htmlspecialchars($e->getMessage()).'</div>';
        }
        
        catch(Exception $e) {
            echo '<div class="error-message">Error: '. htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
    public function createCheckBoxes() {
        try {
            $data = $this->returnValues();
            if(!$data['success']) {
                echo '<p>'.$data['message'].'</p>';
                foreach($data['data'] as $label => $value) {
                    echo '<div class="input-container">
                        </label><input type="checkbox" name="levels[]" value="'. $value .'">'
                    .htmlspecialchars($label).' </label>
                    </div>';
                }
            }
            else {
                $gradeLevels = $data['data'];
                foreach($gradeLevels as $rows) {
                    echo '<div class="input-container">
                        </label><input type="checkbox" name="levels[]" value="'. $rows['Grade_Level_Id'] .'">'
                    .htmlspecialchars($rows['Grade_Level']).' </label>
                    </div>';
                }
            }
        }
        catch(DatabaseConnectionException $e) {
            echo '<div class="error-mesage">'.htmlspecialchars($e->getMessage()).'</div>';
        }
        catch(DatabaseException $e) {
            echo '<div class="error-mesage">'.htmlspecialchars($e->getMessage()).'</div>';
        }
        catch(Exception $e) {
            echo '<input type="text" name="manual-grade">';
        }
    }
}