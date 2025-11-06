<?php
require_once __DIR__ . '/../core/dbconnection.php';

class userEditFormModel {
    private $conn;

    public function __construct() {
        $db = new Connect();
        $this->conn = $db->getConnection();
    }
    public function getEnrolleeData($enrolleeId) {
        try {
            // First get the enrollee's basic information
            $sql = "SELECT e.*,
                    ei.If_LRN_Returning,
                    ei.Enrolling_Grade_Level,
                    ei.Last_Grade_Level,
                    ei.Last_Year_Attended,
                    eb.*,
                    ea.*,
                    ds.*,
                    pd.filename as psa_image_filename,
                    pd.directory as psa_image_path
                FROM enrollee AS e
                JOIN educational_information AS ei ON e.Educational_Information_ID = ei.Educational_Information_ID
                JOIN educational_background AS eb ON e.Educational_Background_ID = eb.Educational_Background_ID
                JOIN enrollee_address AS ea ON e.Enrollee_Address_ID = ea.Enrollee_Address_ID
                JOIN disabled_student AS ds ON e.Disabled_Student_ID = ds.Disabled_Student_ID
                LEFT JOIN Psa_directory AS pd ON e.Psa_Image_Id = pd.Psa_Image_Id
                WHERE e.Enrollee_ID = :enrolleeId";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':enrolleeId', $enrolleeId);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                return null;
            }

            // Get parent information
            $parentSql = "SELECT pi.*, ep.Relationship
                         FROM enrollee_parents ep
                         JOIN parent_information pi ON ep.Parent_Id = pi.Parent_Id
                         WHERE ep.Enrollee_Id = :enrolleeId";
            
            $parentStmt = $this->conn->prepare($parentSql);
            $parentStmt->bindParam(':enrolleeId', $enrolleeId);
            $parentStmt->execute();
            $parents = $parentStmt->fetchAll(PDO::FETCH_ASSOC);

            // Organize parent information by relationship
            $parentInfo = [];
            foreach ($parents as $parent) {
                $relationship = strtolower($parent['Relationship']);
                $parentInfo[$relationship] = [
                    'first_name' => $parent['First_Name'],
                    'middle_name' => $parent['Middle_Name'],
                    'last_name' => $parent['Last_Name'],
                    'educational_attainment' => $parent['Educational_Attainment'],
                    'contact_number' => $parent['Contact_Number'],
                    'if_4ps' => $parent['If_4Ps']
                ];
            }

            // Add parent information to result
            $result['parent_information'] = $parentInfo;
            
            return $result;
        }
        catch (PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    public function updateEnrolleeData($enrolleeId, $data) {
        try {
            $this->conn->beginTransaction();
            error_log("Starting transaction for enrollee ID: " . $enrolleeId);
            // Convert and validate LRN and PSA numbers
            $lrn = !empty($data['lrn']) ? filter_var($data['lrn'], FILTER_VALIDATE_INT) : null;
            $psa = !empty($data['psa']) ? filter_var($data['psa'], FILTER_VALIDATE_INT) : null;

            error_log("Converted LRN: " . var_export($lrn, true));
            error_log("Converted PSA: " . var_export($psa, true));
            // 1. Update enrollee table
            $enrolleeSQL = "UPDATE enrollee SET 
                Student_First_Name = :firstName,
                Student_Last_Name = :lastName,
                Student_Middle_Name = :middleName,
                Student_Extension = :extension,
                Learner_Reference_Number = :lrn,
                Psa_Number = :psa,
                Age = :age,
                Birth_Date = :birthdate,
                Sex = :sex,
                Religion = :religion,
                Native_Language = :nativeLanguage,
                If_Cultural = :ifCultural,
                Cultural_Group = :culturalGroup,
                Student_Email = :email
                WHERE Enrollee_ID = :enrolleeId";

            $enrolleeStmt = $this->conn->prepare($enrolleeSQL);
            $enrolleeResult = $enrolleeStmt->execute([
                ':firstName' => $data['first_name'],
                ':lastName' => $data['last_name'],
                ':middleName' => $data['middle_name'],
                ':extension' => $data['extension'],
                ':lrn' => $lrn,
                ':psa' => $psa,
                ':age' => $data['age'],
                ':birthdate' => $data['birthdate'],
                ':sex' => $data['sex'],
                ':religion' => $data['religion'],
                ':nativeLanguage' => $data['native_language'],
                ':ifCultural' => $data['belongs_in_cultural_group'],
                ':culturalGroup' => $data['cultural_group'],
                ':email' => $data['email_address'],
                ':enrolleeId' => $enrolleeId
            ]);
            if (!$enrolleeResult) {
                error_log("Failed to update enrollee table: " . json_encode($enrolleeStmt->errorInfo()));
                throw new PDOException("Failed to update enrollee table");
            }
            error_log("Successfully updated enrollee table");
            // 2. Update educational_information table
            $eduInfoSQL = "UPDATE educational_information ei 
                JOIN enrollee e ON e.Educational_Information_ID = ei.Educational_Information_ID
                SET 
                ei.Enrolling_Grade_Level = :enrollingGrade,
                ei.Last_Grade_Level = :lastGrade,
                ei.Last_Year_Attended = :lastYear
                WHERE e.Enrollee_ID = :enrolleeId";

            $eduInfoStmt = $this->conn->prepare($eduInfoSQL);
            $eduInfoResult = $eduInfo;

            if (!$eduInfoResult) {
                error_log("Failed to update educational_information table: " . json_encode($eduInfoStmt->errorInfo()));
                throw new PDOException("Failed to update educational information");
            }
            error_log("Successfully updated educational_information table");

            // 3. Update educational_background table
            $eduBgSQL = "UPDATE educational_background eb 
                JOIN enrollee e ON e.Educational_Background_ID = eb.Educational_Background_ID
                SET 
                eb.Last_School_Attended = :lastSchool,
                eb.School_Id = :schoolId,
                eb.School_Address = :schoolAddress,
                eb.School_Type = :schoolType
                WHERE e.Enrollee_ID = :enrolleeId";

            $eduBgStmt = $this->conn->prepare($eduBgSQL);
            $eduBgResult = $eduBgStmt->execute([
                ':lastSchool' => $data['last_school_attended'],
                ':schoolId' => $data['school_id'],
                ':schoolAddress' => $data['school_address'],
                ':schoolType' => $data['school_type'],
                ':enrolleeId' => $enrolleeId
            ]);

            if (!$eduBgResult) {
                error_log("Failed to update educational_background table: " . json_encode($eduBgStmt->errorInfo()));
                throw new PDOException("Failed to update educational background");
            }
            error_log("Successfully updated educational_background table");

            // 4. Update enrollee_address table
            $addressSQL = "UPDATE enrollee_address AS ea 
                JOIN enrollee AS e ON e.Enrollee_Address_ID = ea.Enrollee_Address_ID
                SET 
                ea.Region_Code = :region,
                ea.Region = :regionName,
                ea.Province_Code = :province,
                ea.Province_Name = :provinceName,
                ea.Municipality_Code = :municipality,
                ea.Municipality_Name = :municipalityName,
                ea.Brgy_Code = :barangay,
                ea.Brgy_Name = :barangayName,
                ea.Subd_Name = :subdivision,
                ea.House_Number = :houseNumber
                WHERE e.Enrollee_ID = :enrolleeId";

            $addressStmt = $this->conn->prepare($addressSQL);
            $addressResult = $addressStmt->execute([
                ':region' => $data['region'],
                ':regionName' => $data['region_name'],
                ':province' => $data['province'],
                ':provinceName' => $data['province_name'],
                ':municipality' => $data['city-municipality'],
                ':municipalityName' => $data['city_municipality_name'],
                ':barangay' => $data['barangay'],
                ':barangayName' => $data['barangay_name'],
                ':subdivision' => $data['subdivision'],
                ':houseNumber' => $data['house_number'],
                ':enrolleeId' => $enrolleeId
            ]);

            if (!$addressResult) {
                error_log("Failed to update enrollee_address table: " . json_encode($addressStmt->errorInfo()));
                throw new PDOException("Failed to update address information");
            }
            error_log("Successfully updated enrollee_address table");

            // 5. Update disabled_student table
            $disabledSQL = "UPDATE disabled_student ds 
                JOIN enrollee e ON e.Disabled_Student_ID = ds.Disabled_Student_ID
                SET 
                ds.Have_Special_Condition = :hasSpecialCondition,
                ds.Special_Condition = :specialCondition,
                ds.Have_Assistive_Tech = :hasAssistiveTech,
                ds.Assistive_Tech = :assistiveTech
                WHERE e.Enrollee_ID = :enrolleeId";

            $disabledStmt = $this->conn->prepare($disabledSQL);
            $disabledResult = $disabledStmt->execute([
                ':hasSpecialCondition' => $data['has_a_special_condition'],
                ':specialCondition' => $data['special_condition'],
                ':hasAssistiveTech' => $data['has_assistive_technology'],
                ':assistiveTech' => $data['assistive_technology'],
                ':enrolleeId' => $enrolleeId
            ]);

            if (!$disabledResult) {
                error_log("Failed to update disabled_student table: " . json_encode($disabledStmt->errorInfo()));
                throw new PDOException("Failed to update disability information");
            }
            error_log("Successfully updated disabled_student table");

            // Update parent information
            foreach (['father', 'mother', 'guardian'] as $relationship) {
                if (isset($data['parent_information'][$relationship])) {
                    $parentInfo = $data['parent_information'][$relationship];
                    error_log("Processing " . $relationship . " information: " . json_encode($parentInfo));
                    
                    $parentSQL = "UPDATE parent_information pi
                                JOIN enrollee_parents ep ON pi.Parent_Id = ep.Parent_Id
                                SET 
                                pi.First_Name = :firstName,
                                pi.Middle_Name = :middleName,
                                pi.Last_Name = :lastName,
                                pi.Educational_Attainment = :educationalAttainment,
                                pi.Contact_Number = :contactNumber,
                                pi.If_4Ps = :if4ps
                                WHERE ep.Enrollee_Id = :enrolleeId 
                                AND ep.Relationship = :relationship";

                    $parentStmt = $this->conn->prepare($parentSQL);
                    $parentResult = $parentStmt->execute([
                        ':firstName' => $parentInfo['first_name'],
                        ':middleName' => $parentInfo['middle_name'],
                        ':lastName' => $parentInfo['last_name'],
                        ':educationalAttainment' => $parentInfo['educational_attainment'],
                        ':contactNumber' => $parentInfo['contact_number'],
                        ':if4ps' => $parentInfo['if_4ps'],
                        ':enrolleeId' => $enrolleeId,
                        ':relationship' => ucfirst($relationship)
                    ]);

                    if (!$parentResult) {
                        error_log("Failed to update " . $relationship . " information: " . json_encode($parentStmt->errorInfo()));
                        throw new PDOException("Failed to update " . $relationship . " information");
                    }
                    error_log("Successfully updated " . $relationship . " information");
                }
            }

            // Handle PSA image update if present
            if (isset($data['psa_image'])) {
                error_log("Processing PSA image update: " . json_encode($data['psa_image']));
                
                // Get the current PSA image ID if any
                $currentPsaSQL = "SELECT Psa_Image_Id FROM enrollee WHERE Enrollee_ID = :enrolleeId";
                $currentPsaStmt = $this->conn->prepare($currentPsaSQL);
                $currentPsaStmt->execute([':enrolleeId' => $enrolleeId]);
                $currentPsaId = $currentPsaStmt->fetchColumn();
                
                error_log("Current PSA Image ID: " . var_export($currentPsaId, true));

                // First insert into Psa_directory
                $psaSQL = "INSERT INTO Psa_directory (filename, directory) VALUES (:filename, :directory)";
                $psaStmt = $this->conn->prepare($psaSQL);
                $psaResult = $psaStmt->execute([
                    ':filename' => $data['psa_image']['filename'],
                    ':directory' => $data['psa_image']['filepath']
                ]);

                if (!$psaResult) {
                    error_log("Failed to insert PSA directory record: " . json_encode($psaStmt->errorInfo()));
                    throw new PDOException("Failed to update PSA image directory");
                }
                
                // Get the new PSA image ID
                $newPsaImageId = $this->conn->lastInsertId();
                error_log("New PSA Image ID: " . $newPsaImageId);
                
                // Update the enrollee table with the new PSA image ID
                $updatePsaSQL = "UPDATE enrollee SET Psa_Image_Id = :psaImageId WHERE Enrollee_ID = :enrolleeId";
                $updatePsaStmt = $this->conn->prepare($updatePsaSQL);
                $updatePsaResult = $updatePsaStmt->execute([
                    ':psaImageId' => $newPsaImageId,
                    ':enrolleeId' => $enrolleeId
                ]);

                if (!$updatePsaResult) {
                    error_log("Failed to update enrollee PSA image ID: " . json_encode($updatePsaStmt->errorInfo()));
                    throw new PDOException("Failed to update enrollee PSA image reference");
                }
                error_log("Successfully updated PSA image information");

                // Delete the old PSA directory entry if it exists
                if ($currentPsaId) {
                    $deletePsaSQL = "DELETE FROM Psa_directory WHERE Psa_Image_Id = :oldPsaId";
                    $deletePsaStmt = $this->conn->prepare($deletePsaSQL);
                    $deletePsaResult = $deletePsaStmt->execute([':oldPsaId' => $currentPsaId]);
                    
                    if (!$deletePsaResult) {
                        error_log("Warning: Failed to delete old PSA directory entry: " . json_encode($deletePsaStmt->errorInfo()));
                    } else {
                        error_log("Successfully deleted old PSA directory entry");
                    }
                }
            }

            $this->conn->commit();
            error_log("Successfully committed all changes");
            
            $resubmitResult = $this->setResubmitStatus($enrolleeId);
            if (!$resubmitResult['success']) {
                error_log("Warning: Failed to set resubmit status: " . json_encode($resubmitResult));
            }
            
            return ['success' => true, 'message' => 'Enrollment data updated successfully'];
        } catch (PDOException $e) {
            error_log("Error in updateEnrolleeData: " . $e->getMessage());
            error_log("Rolling back transaction");
            $this->conn->rollBack();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function testSingleUpdate($enrolleeId, $firstName) {
        try {
            $sql = "UPDATE enrollee SET Student_First_Name = :firstName WHERE Enrollee_ID = :enrolleeId";
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([
                ':firstName' => $firstName,
                ':enrolleeId' => $enrolleeId
            ]);
            
            error_log("Test update result: " . ($result ? "success" : "failed"));
            error_log("Test update error info: " . json_encode($stmt->errorInfo()));
            
            return ['success' => $result, 'message' => $result ? 'Test update successful' : 'Test update failed'];
        } catch (PDOException $e) {
            error_log("Test update error: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
