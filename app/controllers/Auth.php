<?php
class Auth extends Controller
{
    public function login($pathRole = null)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $identifier = trim($_POST['identifier'] ?? '');
            $password = $_POST['password'] ?? '';
            $csrf = $_POST['_csrf'] ?? '';

            if (!verify_csrf_token($csrf)) {
                return $this->view('auth/login', ['error' => 'Invalid CSRF token.']);
            }

            $user = null;
            $role = null;

            $adminModel = new Admin_model();
            $user = $adminModel->findByUsernameOrEmail($identifier);
            if ($user && password_verify($password, $user['password'])) {
                $role = 'admin';
            } else {
                $librarianModel = new Librarian_model();
                $user = $librarianModel->findByUsernameOrEmail($identifier);
                if ($user && password_verify($password, $user['password'])) {
                    $role = 'librarian';
                } else {
                    $studentModel = new Student_model();
                    $user = $studentModel->findByStudentIdOrEmail($identifier);
                    if ($user && password_verify($password, $user['password'])) {
                        $role = 'student';
                    } else {
                        $facultyModel = new Faculty_model();
                        $user = $facultyModel->findByFacultyIdOrEmail($identifier);
                        if ($user && password_verify($password, $user['password'])) {
                            $role = 'faculty';
                        }
                    }
                }
            }

            if ($role && $user) {
                $db = Database::getInstance();
                $table = $this->getUserTableByRole($role);
                $db->prepare('UPDATE ' . $table . ' SET last_login_at = NOW() WHERE id = :id')->execute([':id' => $user['id']]);
                $_SESSION['user_role'] = $role;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $this->getUserFullName($user);
                $this->logActivity($role, $user['id'], 'login', 'User logged in');
                $_SESSION['flash'] = 'Login successful.';
                redirect(BASE_URL);
            }

            return $this->view('auth/login', ['error' => 'Invalid credentials.']);
        }

        $this->view('auth/login', ['title' => 'Login']);
    }

    public function google()
    {
        $clientId = GOOGLE_CLIENT_ID;
        if (empty($clientId)) {
            $_SESSION['flash_error'] = 'Google OAuth is not configured. Set GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET in your environment variables.';
            redirect(BASE_URL . '/?url=auth/login');
        }

        $redirectUri = GOOGLE_REDIRECT_URI ?: APP_BASE_URL . '/?url=auth/googleCallback';
        $params = [
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'access_type' => 'online',
            'prompt' => 'select_account',
        ];

        header('Location: https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params));
        exit;
    }

    public function googleCallback()
    {
        $code = $_GET['code'] ?? '';
        if (empty($code)) {
            $_SESSION['flash_error'] = 'Google login was cancelled or did not return a valid authorization code.';
            redirect(BASE_URL . '/?url=auth/login');
        }

        $clientId = GOOGLE_CLIENT_ID;
        $clientSecret = GOOGLE_CLIENT_SECRET;
        $redirectUri = GOOGLE_REDIRECT_URI ?: APP_BASE_URL . '/?url=auth/googleCallback';

        if (empty($clientId) || empty($clientSecret)) {
            $_SESSION['flash_error'] = 'Google OAuth is not configured. Add your Google client credentials in your environment variables.';
            redirect(BASE_URL . '/?url=auth/login');
        }

        $tokenPayload = [
            'code' => $code,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code',
        ];

        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($tokenPayload),
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 30,
        ]);
        $tokenResponse = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode < 200 || $httpCode >= 300 || empty($tokenResponse)) {
            $_SESSION['flash_error'] = 'Google login failed while exchanging the authorization code.';
            redirect(BASE_URL . '/?url=auth/login');
        }

        $tokenData = json_decode($tokenResponse, true);
        if (empty($tokenData['access_token'])) {
            $_SESSION['flash_error'] = 'Google did not return an access token.';
            redirect(BASE_URL . '/?url=auth/login');
        }

        $userInfoCh = curl_init('https://openidconnect.googleapis.com/v1/userinfo?access_token=' . urlencode($tokenData['access_token']));
        curl_setopt_array($userInfoCh, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 30,
        ]);
        $userInfoResponse = curl_exec($userInfoCh);
        $userInfoCode = curl_getinfo($userInfoCh, CURLINFO_HTTP_CODE);
        curl_close($userInfoCh);

        if ($userInfoCode < 200 || $userInfoCode >= 300 || empty($userInfoResponse)) {
            $_SESSION['flash_error'] = 'Google profile information could not be retrieved.';
            redirect(BASE_URL . '/?url=auth/login');
        }

        $profile = json_decode($userInfoResponse, true);
        if (empty($profile['email']) || empty($profile['email_verified'])) {
            $_SESSION['flash_error'] = 'Google login requires a verified email address.';
            redirect(BASE_URL . '/?url=auth/login');
        }

        $email = strtolower(trim($profile['email']));
        $user = $this->findGoogleEligibleUserByEmailOrPhone($email);
        if (!$user) {
            $_SESSION['google_signup_email'] = $email;
            $_SESSION['google_signup_name'] = $profile['name'] ?? '';
            $_SESSION['flash_error'] = 'No existing student or faculty account matches that Google email. Please create an account first.';
            redirect(BASE_URL . '/?url=register');
        }

        $this->completeLogin($user['role'], $user, 'google', [
            'provider_user_id' => (string) ($profile['sub'] ?? $email),
            'verified_email' => $email,
            'verified_phone' => $user['mobile'] ?? null,
            'auth_name' => $profile['name'] ?? $this->getUserFullName($user),
            'profile_picture' => $profile['picture'] ?? ($user['profile_picture'] ?? null),
        ]);

        $_SESSION['flash'] = 'Login successful.';
        redirect(BASE_URL);
    }

    public function sendOtp()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . '/?url=auth/login');
        }

        $phone = normalize_phone_number($_POST['phone'] ?? ($_POST['identifier'] ?? ''));
        $csrf = $_POST['_csrf'] ?? '';

        if (!verify_csrf_token($csrf)) {
            return $this->view('auth/login', ['error' => 'Invalid CSRF token.']);
        }

        if ($phone === '' || !is_valid_phone_number($phone)) {
            return $this->view('auth/login', ['error' => 'Please enter a valid phone number in international format.']);
        }

        $now = time();
        $windowStart = (int) ($_SESSION['otp_window_start'] ?? 0);
        $requestCount = (int) ($_SESSION['otp_request_count'] ?? 0);
        if ($windowStart === 0) {
            $_SESSION['otp_window_start'] = $now;
        } elseif (($now - $windowStart) > OTP_RATE_LIMIT_WINDOW_SECONDS) {
            $_SESSION['otp_window_start'] = $now;
            $_SESSION['otp_request_count'] = 0;
            $requestCount = 0;
        }
        if ($requestCount >= OTP_MAX_REQUESTS_PER_WINDOW) {
            return $this->view('auth/login', ['error' => 'Too many OTP requests. Please wait a few minutes before trying again.']);
        }

        $lastSent = (int) ($_SESSION['otp_last_sent_at'] ?? 0);
        if ($lastSent > 0 && ($now - $lastSent) < OTP_RESEND_SECONDS) {
            $remaining = OTP_RESEND_SECONDS - ($now - $lastSent);
            return $this->view('auth/login', ['error' => 'Please wait ' . $remaining . ' seconds before requesting a new OTP.']);
        }

        $user = $this->findUserByPhone($phone);
        if (!$user) {
            return $this->view('auth/login', ['error' => 'No account found for that phone number.']);
        }

        $otp = OtpService::generateCode(6);
        $db = Database::getInstance();
        $db->prepare('INSERT INTO otp_codes (user_type, user_ref_id, identifier, code_hash, expires_at, attempts) VALUES (:role, :id, :identifier, :code_hash, :expires_at, 0)')->execute([
            ':role' => $user['role'],
            ':id' => $user['id'],
            ':identifier' => $phone,
            ':code_hash' => hash_otp_code($otp),
            ':expires_at' => date('Y-m-d H:i:s', $now + OTP_CODE_TTL_SECONDS),
        ]);

        $result = OtpService::sendCodeToPhone($phone, $otp);
        if (!$result['ok']) {
            return $this->view('auth/login', ['error' => $result['message']]);
        }

        $_SESSION['otp_identifier'] = $phone;
        $_SESSION['otp_user_ref_id'] = $user['id'];
        $_SESSION['otp_user_role'] = $user['role'];
        $_SESSION['otp_last_sent_at'] = $now;
        $_SESSION['otp_request_count'] = $requestCount + 1;
        $_SESSION['otp_attempts'] = 0;

        return $this->view('auth/login', ['success' => 'A 6-digit code has been sent to your phone number. Enter it below to verify your identity.']);
    }

    public function verifyOtp()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . '/?url=auth/login');
        }

        $identifier = trim($_POST['phone'] ?? ($_POST['identifier'] ?? ''));
        $otp = trim($_POST['otp'] ?? '');
        $csrf = $_POST['_csrf'] ?? '';

        if (!verify_csrf_token($csrf)) {
            return $this->view('auth/login', ['error' => 'Invalid CSRF token.']);
        }

        $phone = normalize_phone_number($identifier);
        if ($phone === '' || !is_valid_phone_number($phone)) {
            return $this->view('auth/login', ['error' => 'Please enter a valid phone number.']);
        }

        $user = $this->findUserByPhone($phone);
        if (!$user) {
            return $this->view('auth/login', ['error' => 'No account matches this phone number.']);
        }

        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM otp_codes WHERE user_type = :role AND user_ref_id = :id AND identifier = :identifier AND expires_at >= NOW() ORDER BY created_at DESC LIMIT 1');
        $stmt->execute([
            ':role' => $user['role'],
            ':id' => $user['id'],
            ':identifier' => $phone,
        ]);
        $otpEntry = $stmt->fetch();

        if (!$otpEntry) {
            return $this->view('auth/login', ['error' => 'Invalid or expired OTP code.']);
        }

        $attemptsUsed = (int) ($otpEntry['attempts'] ?? 0);
        if ($attemptsUsed >= OTP_MAX_ATTEMPTS) {
            $db->prepare('DELETE FROM otp_codes WHERE id = :id')->execute([':id' => $otpEntry['id']]);
            return $this->view('auth/login', ['error' => 'Too many incorrect OTP attempts. Please request a new code.']);
        }

        if (!hash_equals($otpEntry['code_hash'], hash_otp_code($otp))) {
            $db->prepare('UPDATE otp_codes SET attempts = attempts + 1 WHERE id = :id')->execute([':id' => $otpEntry['id']]);
            $remaining = max(0, OTP_MAX_ATTEMPTS - ($attemptsUsed + 1));
            return $this->view('auth/login', ['error' => 'Invalid OTP code. ' . $remaining . ' attempts remaining.']);
        }

        $db->prepare('DELETE FROM otp_codes WHERE id = :id')->execute([':id' => $otpEntry['id']]);
        $this->completeLogin($user['role'], $user, 'phone', [
            'provider_user_id' => (string) $user['id'],
            'verified_phone' => $phone,
            'verified_email' => $user['email'] ?? null,
            'auth_name' => $this->getUserFullName($user),
            'profile_picture' => $user['profile_picture'] ?? null,
        ]);
        unset($_SESSION['otp_identifier'], $_SESSION['otp_user_ref_id'], $_SESSION['otp_user_role'], $_SESSION['otp_last_sent_at'], $_SESSION['otp_request_count'], $_SESSION['otp_window_start'], $_SESSION['otp_attempts']);
        $_SESSION['flash'] = 'Login successful.';
        redirect(BASE_URL);
    }

    private function completeLogin($role, $user, $provider, $info = [])
    {
        $table = $this->getUserTableByRole($role);
        $db = Database::getInstance();
        $sql = 'UPDATE ' . $table . ' SET auth_provider = :auth_provider, provider_user_id = :provider_user_id, verified_email = :verified_email, verified_phone = :verified_phone, auth_name = :auth_name, profile_picture = :profile_picture, last_login_at = NOW() WHERE id = :id';
        $db->prepare($sql)->execute([
            ':auth_provider' => $provider,
            ':provider_user_id' => $info['provider_user_id'] ?? ($user['id'] . ':' . $provider),
            ':verified_email' => $info['verified_email'] ?? ($user['email'] ?? null),
            ':verified_phone' => $info['verified_phone'] ?? ($user['mobile'] ?? null),
            ':auth_name' => $info['auth_name'] ?? $this->getUserFullName($user),
            ':profile_picture' => $info['profile_picture'] ?? ($user['profile_picture'] ?? null),
            ':id' => $user['id'],
        ]);

        $_SESSION['user_role'] = $role;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $this->getUserFullName($user);
        $this->logActivity($role, $user['id'], 'login_' . $provider, 'User logged in with ' . ucfirst($provider));
    }

    private function getUserTableByRole($role)
    {
        $tables = [
            'admin' => 'admins',
            'librarian' => 'librarians',
            'student' => 'students',
            'faculty' => 'faculty',
        ];

        return $tables[$role] ?? 'admins';
    }

    private function getUserFullName($user)
    {
        if (!empty($user['fullname'])) {
            return $user['fullname'];
        }

        if (!empty($user['firstname']) || !empty($user['lastname'])) {
            return trim(($user['firstname'] ?? '') . ' ' . ($user['lastname'] ?? ''));
        }

        return $user['username'] ?? ($user['email'] ?? 'User');
    }

    private function findUserByPhone($phone)
    {
        $normalized = normalize_phone_number($phone);
        if ($normalized === '') {
            return null;
        }

        foreach (['admin' => new Admin_model(), 'librarian' => new Librarian_model(), 'student' => new Student_model(), 'faculty' => new Faculty_model()] as $role => $model) {
            $user = $model->findByEmailOrPhone($normalized);
            if ($user) {
                $user['role'] = $role;
                return $user;
            }
        }

        return null;
    }

    private function findGoogleEligibleUserByEmailOrPhone($identifier)
    {
        $identifier = trim((string) $identifier);
        if ($identifier === '') {
            return null;
        }

        foreach (['student' => new Student_model(), 'faculty' => new Faculty_model()] as $role => $model) {
            $user = $model->findByEmailOrPhone($identifier);
            if ($user) {
                $user['role'] = $role;
                return $user;
            }
        }

        return null;
    }

    private function findUserByEmailOrPhone($identifier)
    {
        $identifier = trim((string) $identifier);
        if ($identifier === '') {
            return null;
        }

        foreach (['admin' => new Admin_model(), 'librarian' => new Librarian_model(), 'student' => new Student_model(), 'faculty' => new Faculty_model()] as $role => $model) {
            $user = $model->findByEmailOrPhone($identifier);
            if ($user) {
                $user['role'] = $role;
                return $user;
            }
        }

        return null;
    }

    public function forgot()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $identifier = trim($_POST['identifier'] ?? '');
            $csrf = $_POST['_csrf'] ?? '';

            if (!verify_csrf_token($csrf)) {
                return $this->view('auth/forgot', ['error' => 'Invalid CSRF token.']);
            }

            $user = null;
            $role = null;
            $email = null;

            $adminModel = new Admin_model();
            $user = $adminModel->findByUsernameOrEmail($identifier);
            if ($user) {
                $role = 'admin';
                $email = $user['email'];
            } else {
                $librarianModel = new Librarian_model();
                $user = $librarianModel->findByUsernameOrEmail($identifier);
                if ($user) {
                    $role = 'librarian';
                    $email = $user['email'];
                } else {
                    $studentModel = new Student_model();
                    $user = $studentModel->findByStudentIdOrEmail($identifier);
                    if ($user) {
                        $role = 'student';
                        $email = $user['email'];
                    } else {
                        $facultyModel = new Faculty_model();
                        $user = $facultyModel->findByFacultyIdOrEmail($identifier);
                        if ($user) {
                            $role = 'faculty';
                            $email = $user['email'];
                        }
                    }
                }
            }

            if (!$user || !$email) {
                return $this->view('auth/forgot', ['success' => 'If this account exists, a reset link has been sent to the registered email.']);
            }

            $token = bin2hex(random_bytes(16));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
            $db = Database::getInstance();
            $stmt = $db->prepare('INSERT INTO password_resets (user_type, user_ref_id, token, expires_at) VALUES (:user_type, :user_ref_id, :token, :expires_at)');
            $stmt->execute([
                ':user_type' => $role,
                ':user_ref_id' => $user['id'],
                ':token' => hash('sha256', $token),
                ':expires_at' => $expiresAt
            ]);

            $resetUrl = BASE_URL . '/?url=auth/reset/' . $token;
            try {
                $mailer = new Mailer();
                $subject = APP_NAME . ' Password Reset Request';
                $body = '<p>Click the link below to reset your password. This link expires in 60 minutes.</p>' .
                    '<p><a href="' . e($resetUrl) . '">' . e($resetUrl) . '</a></p>';
                $mailer->send($email, $subject, $body);
            } catch (Exception $ex) {
                // ignore email failure for security reasons
            }

            return $this->view('auth/forgot', ['success' => 'If this account exists, a reset link has been sent to the registered email.']);
        }

        $this->view('auth/forgot', ['title' => 'Forgot Password']);
    }

    public function reset($token = null)
    {
        if (!$token) {
            redirect(BASE_URL . '/?url=auth/forgot');
        }

        $hashedToken = hash('sha256', $token);
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM password_resets WHERE token = :token AND expires_at >= NOW() LIMIT 1');
        $stmt->execute([':token' => $hashedToken]);
        $reset = $stmt->fetch();

        if (!$reset) {
            return $this->view('auth/reset', ['error' => 'Invalid or expired reset link.']);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password = $_POST['password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';
            $csrf = $_POST['_csrf'] ?? '';

            if (!verify_csrf_token($csrf)) {
                return $this->view('auth/reset', ['error' => 'Invalid CSRF token.', 'token' => $token]);
            }
            if (empty($password) || $password !== $confirm) {
                return $this->view('auth/reset', ['error' => 'Passwords must match.', 'token' => $token]);
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            switch ($reset['user_type']) {
                case 'admin':
                    $db->prepare('UPDATE admins SET password = :password WHERE id = :id')->execute([':password' => $hashedPassword, ':id' => $reset['user_ref_id']]);
                    break;
                case 'librarian':
                    $db->prepare('UPDATE librarians SET password = :password WHERE id = :id')->execute([':password' => $hashedPassword, ':id' => $reset['user_ref_id']]);
                    break;
                case 'student':
                    $db->prepare('UPDATE students SET password = :password WHERE id = :id')->execute([':password' => $hashedPassword, ':id' => $reset['user_ref_id']]);
                    break;
                case 'faculty':
                    $db->prepare('UPDATE faculty SET password = :password WHERE id = :id')->execute([':password' => $hashedPassword, ':id' => $reset['user_ref_id']]);
                    break;
            }

            $db->prepare('DELETE FROM password_resets WHERE id = :id')->execute([':id' => $reset['id']]);
            return $this->view('auth/reset', ['success' => 'Password updated successfully. You may now log in.', 'token' => $token]);
        }

        $this->view('auth/reset', ['title' => 'Reset Password', 'token' => $token]);
    }

    public function logout()
    {
        session_unset();
        session_destroy();
        redirect(BASE_URL);
    }

    private function logActivity($user_type, $user_ref_id, $action, $detail = null)
    {
        $db = Database::getInstance();
        $sql = 'INSERT INTO activity_logs (user_type, user_ref_id, action, detail, ip_address) VALUES (:ut, :uid, :act, :det, :ip)';
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':ut' => $user_type,
            ':uid' => $user_ref_id,
            ':act' => $action,
            ':det' => $detail,
            ':ip' => $_SERVER['REMOTE_ADDR'] ?? null
        ]);
    }
}
