<?php
class SMSFailureException extends Exception {} //Domain specific exception
class SendPassword {
    /**
     * Cleans and formats a Philippine mobile number
     * @param string $phoneNumber The phone number to clean (e.g., 09123456789)
     * @return string The cleaned phone number in international format (e.g., 639123456789)
     */
    private function cleanPhoneNumber($phoneNumber) {
        $cleaned = preg_replace('/[^0-9]/', '', $phoneNumber);
        if (substr($cleaned, 0, 2) === '09') {
            $cleaned = '63' . substr($cleaned, 1);
        }
        if (substr($cleaned, 0, 2) !== '63') {
            $cleaned = '63' . $cleaned;
        }
        return $cleaned;
    }

    public function send_password($Last_Name, $First_Name, $Middle_Name, $Recipient_Contact_Number, $User_Password) {
        $gatewayUrl = "http://192.168.1.168:8080/message";
        $username = "sms";
        $password = "KVs6RP-9";

        $Cleaned_Contact_Number = $this->cleanPhoneNumber($Recipient_Contact_Number);
        
        $data = [
            "message" => "Hello $Last_Name, $First_Name $Middle_Name! Your password is $User_Password. Please keep this password safe and don't share it with anyone",
            "phoneNumbers" => ["+$Cleaned_Contact_Number"],
            "simNumber" => 1
        ];

        $ch = curl_init($gatewayUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            throw new SMSFailureException("cURL Error: " . curl_error($ch));
        }

        curl_close($ch);

        $responseData = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new SMSFailureException("Invalid response from SMS gateway");
        }

        // Check if the message was accepted
        if (!isset($responseData['state']) || $responseData['state'] !== 'Pending') {
            throw new SMSFailureException("SMS gateway rejected the message");
        }
        return true;
    }
}
?>
