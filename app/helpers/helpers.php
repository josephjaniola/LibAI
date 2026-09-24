<?php
// Common helper functions (CSRF, sanitize, redirect)
if (!function_exists('generate_csrf_token')) {
    function generate_csrf_token()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $token = bin2hex(random_bytes(16));
        $_SESSION['_csrf_token'] = $token;
        return $token;
    }
}

if (!function_exists('verify_csrf_token')) {
    function verify_csrf_token($token)
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        return isset($_SESSION['_csrf_token']) && hash_equals($_SESSION['_csrf_token'], $token);
    }
}

if (!function_exists('e')) {
    function e($str)
    {
        return htmlspecialchars((string) $str, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('redirect')) {
    function redirect($url)
    {
        header('Location: ' . $url);
        exit;
    }
}

if (!function_exists('normalize_phone_number')) {
    function normalize_phone_number($phone)
    {
        if (!is_string($phone)) {
            $phone = (string) $phone;
        }

        $phone = trim($phone);
        if ($phone === '') {
            return '';
        }

        $digits = preg_replace('/[^0-9+]/', '', $phone);
        if ($digits === '') {
            return '';
        }

        if (strpos($digits, '+') === false && strlen($digits) >= 10) {
            $digits = '+' . ltrim($digits, '+');
        }

        return $digits;
    }
}

if (!function_exists('is_valid_phone_number')) {
    function is_valid_phone_number($phone)
    {
        $normalized = normalize_phone_number($phone);
        if ($normalized === '') {
            return false;
        }

        return preg_match('/^\+[1-9]\d{8,14}$/', $normalized) === 1;
    }
}

if (!function_exists('hash_otp_code')) {
    function hash_otp_code($code)
    {
        return hash_hmac('sha256', (string) $code, APP_OTP_SECRET);
    }
}

if (!function_exists('getCourseOptions')) {
    function getCourseOptions()
    {
        return [
            'HM' => 'HM',
            'TM' => 'TM',
            'EDUC' => 'EDUC',
            'IT' => 'IT',
            'CRIMINOLOGY' => 'CRIMINOLOGY'
        ];
    }
}

if (!function_exists('seedCourseCategories')) {
    function seedCourseCategories()
    {
        $catModel = new Category_model();
        $existing = $catModel->getAll();
        $existingNames = array_column($existing, 'name');
        foreach (array_keys(getCourseOptions()) as $courseName) {
            if (!in_array($courseName, $existingNames, true)) {
                $catModel->create($courseName, 'Course category for ' . $courseName);
            }
        }
        return $catModel->getAll();
    }
}
