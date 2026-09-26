<?php
class OtpService
{
    public static function generateCode($length = 6)
    {
        $length = max(4, (int) $length);
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= random_int(0, 9);
        }
        return $code;
    }

    public static function sendCodeToPhone($phone, $code)
    {
        $result = self::sendMessageToPhone($phone, 'Your LibAI verification code is: ' . $code);
        if ($result['ok']) {
            $result['message'] = 'OTP sent successfully.';
        }
        return $result;
    }

    public static function sendMessageToPhone($phone, $message)
    {
        $phone = normalize_phone_number($phone);
        if ($phone === '' || !is_valid_phone_number($phone)) {
            return ['ok' => false, 'message' => 'Please enter a valid phone number in international format.'];
        }

        $sid = defined('TWILIO_SID') ? TWILIO_SID : '';
        $token = defined('TWILIO_AUTH_TOKEN') ? TWILIO_AUTH_TOKEN : '';
        $from = defined('TWILIO_FROM') ? TWILIO_FROM : '';

        if (!empty($sid) && !empty($token) && !empty($from)) {
            $payload = [
                'To' => $phone,
                'From' => $from,
                'Body' => (string) $message,
            ];

            $ch = curl_init('https://api.twilio.com/2010-04-01/Accounts/' . $sid . '/Messages.json');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_USERPWD => $sid . ':' . $token,
                CURLOPT_POSTFIELDS => http_build_query($payload),
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_TIMEOUT => 30,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode >= 200 && $httpCode < 300) {
                return ['ok' => true, 'message' => 'SMS sent successfully.'];
            }

            return ['ok' => false, 'message' => 'The configured SMS provider rejected the request. Please verify your Twilio account, phone number format, and sender number.'];
        }

        return ['ok' => false, 'message' => 'SMS is not configured. Add TWILIO_SID, TWILIO_AUTH_TOKEN, and TWILIO_FROM in your environment variables or .env file.'];
    }
}
