<?php
// Common helper functions (CSRF, sanitize, redirect)
if (!function_exists('generate_csrf_token')) {
    function generate_csrf_token()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(16));
        }
        return $_SESSION['_csrf_token'];
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

        $digits = preg_replace('/\D/', '', $phone);
        if ($digits === '') {
            return '';
        }

        if (preg_match('/^09\d{9}$/', $digits)) {
            return '+63' . substr($digits, 1);
        }

        if (preg_match('/^639\d{9}$/', $digits)) {
            return '+' . $digits;
        }

        return '+' . $digits;
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
            'BEED' => 'BEED',
            'BSED' => 'BSED',
            'IT' => 'IT',
            'CRIMINOLOGY' => 'CRIMINOLOGY'
        ];
    }
}

if (!function_exists('isPhilippinesMobileNumber')) {
    function isPhilippinesMobileNumber($number)
    {
        return preg_match('/^(?:09[0-9]{9}|\+639[0-9]{9})$/D', (string) $number) === 1;
    }
}

if (!function_exists('getProfilePictureUrl')) {
    function getProfilePictureUrl($picture)
    {
        $picture = trim((string) $picture);
        if ($picture === '') {
            return '';
        }

        if (preg_match('/^https?:\/\//i', $picture)) {
            return $picture;
        }

        return rtrim(BASE_URL, '/') . '/' . ltrim($picture, '/');
    }
}

if (!function_exists('getNameInitials')) {
    function getNameInitials($name)
    {
        $parts = preg_split('/\s+/', trim((string) $name), -1, PREG_SPLIT_NO_EMPTY);
        if (!$parts) {
            return 'U';
        }

        $words = [$parts[0]];
        if (count($parts) > 1) {
            $words[] = $parts[count($parts) - 1];
        }

        $initials = '';
        foreach ($words as $word) {
            if (preg_match('/^./u', $word, $match)) {
                $initials .= $match[0];
            }
        }

        return function_exists('mb_strtoupper') ? mb_strtoupper($initials) : strtoupper($initials);
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
