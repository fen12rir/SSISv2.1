<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../core/dbconnection.php';

class OTPHandler extends Connect {
    private $pdo;
    
    public function __construct() {
        parent::__construct();
        $this->pdo = $this->getConnection();
    }

    private function cleanPhoneNumber($phone) {
        $cleaned = preg_replace('/[^0-9]/', '', $phone);
        
        if (substr($cleaned, 0, 2) === '09') {
            return $cleaned;
        } else if (substr($cleaned, 0, 2) === '63') {
            return '0' . substr($cleaned, 2);
        }
        
        return $cleaned;
    }

    private function findUserByPhone($phone) {
        $cleaned = $this->cleanPhoneNumber($phone);
        
        $staffStmt = $this->pdo->prepare("
            SELECT u.User_Id, u.User_Type, s.Staff_Id, 
                   s.Staff_Last_Name AS Last_Name, 
                   s.Staff_First_Name AS First_Name, 
                   s.Staff_Middle_Name AS Middle_Name,
                   s.Staff_Contact_Number AS Contact_Number, 
                   'staff' as source_table
            FROM users u
            INNER JOIN staffs s ON u.Staff_Id = s.Staff_Id
            WHERE s.Staff_Contact_Number = :phone AND u.Staff_Id IS NOT NULL
        ");
        $staffStmt->execute(['phone' => $cleaned]);
        $staffResult = $staffStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($staffResult) {
            return $staffResult;
        }
        
        $userStmt = $this->pdo->prepare("
            SELECT u.User_Id, u.User_Type, r.Registration_Id, 
                   r.Last_Name, r.First_Name, r.Middle_Name,
                   r.Contact_Number, 'registration' as source_table
            FROM users u
            INNER JOIN registrations r ON u.Registration_Id = r.Registration_Id
            WHERE r.Contact_Number = :phone AND u.Registration_Id IS NOT NULL
        ");
        $userStmt->execute(['phone' => $cleaned]);
        $userResult = $userStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($userResult) {
            return $userResult;
        }
        
        return null;
    }

    private function generateOTP() {
        return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    private function storeOTP($userId, $otp, $token) {
        $expiryTime = date('Y-m-d H:i:s', strtotime('+5 minutes'));
        
        $deleteStmt = $this->pdo->prepare("DELETE FROM otp_verification WHERE User_Id = :user_id");
        $deleteStmt->execute(['user_id' => $userId]);
        
        $stmt = $this->pdo->prepare("
            INSERT INTO otp_verification (User_Id, OTP_Code, Token, Expiry_Time, Created_At) 
            VALUES (:user_id, :otp, :token, :expiry, NOW())
        ");
        
        return $stmt->execute([
            'user_id' => $userId,
            'otp' => $otp,
            'token' => $token,
            'expiry' => $expiryTime
        ]);
    }

    private function sendOTPSMS($phone, $userData, $otp) {
        try {
            $gatewayUrl = "http://192.168.1.168:8080/message";
            $username = "sms";
            $password = "KVs6RP-9";

            $cleanedPhone = $this->cleanPhoneNumber($phone);
            if (substr($cleanedPhone, 0, 2) === '09') {
                $cleanedPhone = '63' . substr($cleanedPhone, 1);
            }
            
            $fullName = trim($userData['First_Name'] . ' ' . ($userData['Middle_Name'] ?? '') . ' ' . $userData['Last_Name']);
            $message = "Hello $fullName! Your OTP for password reset is: $otp. Valid for 5 minutes. Do not share this code with anyone.";
            
            $data = [
                "message" => $message,
                "phoneNumbers" => ["+$cleanedPhone"],
                "simNumber" => 1
            ];

            $ch = curl_init($gatewayUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                curl_close($ch);
                return ['success' => false];
            }

            curl_close($ch);

            $responseData = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return ['success' => false];
            }

            if (!isset($responseData['state']) || $responseData['state'] !== 'Pending') {
                return ['success' => false];
            }
            
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false];
        }
    }

    public function requestOTP($phone) {
        try {
            $userData = $this->findUserByPhone($phone);
            
            if (!$userData) {
                return [
                    'success' => false,
                    'message' => 'No account found with this phone number'
                ];
            }

            $otp = $this->generateOTP();
            $token = bin2hex(random_bytes(32));
            
            if (!$this->storeOTP($userData['User_Id'], $otp, $token)) {
                return [
                    'success' => false,
                    'message' => 'Failed to generate OTP. Please try again.'
                ];
            }

            $smsResult = $this->sendOTPSMS($phone, $userData, $otp);
            
            if (!$smsResult['success']) {
                return [
                    'success' => false,
                    'message' => 'Failed to send OTP via SMS. Please try again.'
                ];
            }

            return [
                'success' => true,
                'message' => 'OTP sent successfully to your mobile number',
                'token' => $token,
                'user_type' => $userData['User_Type']
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred. Please try again later.'
            ];
        }
    }

    public function verifyOTP($phone, $otp, $token) {
        try {
            $userData = $this->findUserByPhone($phone);
            
            if (!$userData) {
                return [
                    'success' => false,
                    'message' => 'Invalid request'
                ];
            }

            $stmt = $this->pdo->prepare("
                SELECT OTP_Code, Expiry_Time, Is_Used 
                FROM otp_verification 
                WHERE User_Id = :user_id AND Token = :token
            ");
            $stmt->execute([
                'user_id' => $userData['User_Id'],
                'token' => $token
            ]);
            $otpData = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$otpData) {
                return [
                    'success' => false,
                    'message' => 'Invalid or expired OTP session'
                ];
            }
            
            if ($otpData['Is_Used']) {
                return [
                    'success' => false,
                    'message' => 'OTP already used'
                ];
            }

            if (strtotime($otpData['Expiry_Time']) < time()) {
                return [
                    'success' => false,
                    'message' => 'OTP has expired'
                ];
            }

            if ($otpData['OTP_Code'] !== $otp) {
                return [
                    'success' => false,
                    'message' => 'Invalid OTP'
                ];
            }

            $updateStmt = $this->pdo->prepare("
                UPDATE otp_verification 
                SET Is_Used = 1 
                WHERE User_Id = :user_id AND Token = :token
            ");
            $updateStmt->execute([
                'user_id' => $userData['User_Id'],
                'token' => $token
            ]);

            return [
                'success' => true,
                'message' => 'OTP verified successfully'
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred during verification'
            ];
        }
    }
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';

    $otpHandler = new OTPHandler();

    switch ($action) {
        case 'request_otp':
            $phone = $input['phone_number'] ?? '';
            echo json_encode($otpHandler->requestOTP($phone));
            break;

        case 'verify_otp':
            $phone = $input['phone_number'] ?? '';
            $otp = $input['otp'] ?? '';
            $token = $input['token'] ?? '';
            echo json_encode($otpHandler->verifyOTP($phone, $otp, $token));
            break;

        default:
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action'
            ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Server error occurred'
    ]);
}
?>
