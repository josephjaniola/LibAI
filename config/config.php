<?php
// Load environment variables from a local .env file if present.
$dotenvFile = __DIR__ . '/../.env';
if (file_exists($dotenvFile)) {
    $lines = file($dotenvFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (is_array($lines)) {
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || strpos($line, '#') === 0) {
                continue;
            }
            if (strpos($line, '=') === false) {
                continue;
            }
            [$name, $value] = array_map('trim', explode('=', $line, 2));
            $name = trim($name);
            $value = trim($value);
            if (($value[0] ?? '') === '"' && substr($value, -1) === '"') {
                $value = substr($value, 1, -1);
            }
            if (($value[0] ?? '') === "'" && substr($value, -1) === "'") {
                $value = substr($value, 1, -1);
            }
            $_ENV[$name] = $value;
            putenv($name . '=' . $value);
        }
    }
}

function libai_env($key, $default = '')
{
    $value = $_ENV[$key] ?? getenv($key);
    if ($value === false || $value === null || $value === '') {
        return $default;
    }
    return $value;
}

date_default_timezone_set(libai_env('APP_TIMEZONE', 'Asia/Manila'));

// Basic configuration for LibAI
define('BASE_URL', libai_env('BASE_URL', '/LIBAI'));
define('APP_NAME', libai_env('APP_NAME', 'LibAI'));
$httpProtocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$httpHost = $_SERVER['HTTP_HOST'] ?? 'localhost';
define('APP_BASE_URL', libai_env('APP_BASE_URL', $httpProtocol . '://' . $httpHost . BASE_URL));

// Database configuration - update before importing SQL
define('DB_HOST', libai_env('DB_HOST', '127.0.0.1'));
define('DB_NAME', libai_env('DB_NAME', 'libai'));
define('DB_USER', libai_env('DB_USER', 'root'));
define('DB_PASS', libai_env('DB_PASS', ''));

// OpenAI configuration - set your real key in the server environment or here
if (!defined('OPENAI_API_KEY')) {
    define('OPENAI_API_KEY', libai_env('OPENAI_API_KEY', ''));
}
if (!defined('OPENAI_MODEL')) {
    define('OPENAI_MODEL', libai_env('OPENAI_MODEL', 'gpt-4o-mini'));
}

// Security and auth settings
if (!defined('APP_ENV')) {
    define('APP_ENV', libai_env('APP_ENV', 'production'));
}
if (!defined('APP_OTP_SECRET')) {
    define('APP_OTP_SECRET', libai_env('APP_OTP_SECRET', 'libai-dev-secret-change-me'));
}
if (!defined('OTP_CODE_TTL_SECONDS')) {
    define('OTP_CODE_TTL_SECONDS', (int) libai_env('OTP_CODE_TTL_SECONDS', '300'));
}
if (!defined('OTP_RESEND_SECONDS')) {
    define('OTP_RESEND_SECONDS', (int) libai_env('OTP_RESEND_SECONDS', '30'));
}
if (!defined('OTP_MAX_ATTEMPTS')) {
    define('OTP_MAX_ATTEMPTS', (int) libai_env('OTP_MAX_ATTEMPTS', '5'));
}
if (!defined('OTP_MAX_REQUESTS_PER_WINDOW')) {
    define('OTP_MAX_REQUESTS_PER_WINDOW', (int) libai_env('OTP_MAX_REQUESTS_PER_WINDOW', '3'));
}
if (!defined('OTP_RATE_LIMIT_WINDOW_SECONDS')) {
    define('OTP_RATE_LIMIT_WINDOW_SECONDS', (int) libai_env('OTP_RATE_LIMIT_WINDOW_SECONDS', '300'));
}

// Twilio / SMS configuration for OTP login (optional)
define('TWILIO_SID', libai_env('TWILIO_SID', ''));
define('TWILIO_AUTH_TOKEN', libai_env('TWILIO_AUTH_TOKEN', ''));
define('TWILIO_FROM', libai_env('TWILIO_FROM', ''));

// Google OAuth configuration
define('GOOGLE_CLIENT_ID', libai_env('GOOGLE_CLIENT_ID', ''));
define('GOOGLE_CLIENT_SECRET', libai_env('GOOGLE_CLIENT_SECRET', ''));
define('GOOGLE_REDIRECT_URI', libai_env('GOOGLE_REDIRECT_URI', APP_BASE_URL . '/?url=auth/googleCallback'));

// Email configuration - update with your SMTP settings
define('MAIL_HOST', libai_env('MAIL_HOST', 'smtp.example.com'));
define('MAIL_USERNAME', libai_env('MAIL_USERNAME', 'user@example.com'));
define('MAIL_PASSWORD', libai_env('MAIL_PASSWORD', 'secret'));
define('MAIL_PORT', (int) libai_env('MAIL_PORT', '587'));
define('MAIL_FROM_ADDRESS', libai_env('MAIL_FROM_ADDRESS', 'noreply@example.com'));
define('MAIL_FROM_NAME', APP_NAME);

// Paths
define('APP_PATH', __DIR__ . '/../app');
define('UPLOADS_PATH', __DIR__ . '/../uploads');

// Start session
if (session_status() === PHP_SESSION_NONE) {
    $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}
