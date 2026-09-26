<?php
class Register extends Controller
{
    public function index()
    {
        $preserveGoogleSignup = ($_GET['google_signup'] ?? '') === '1';
        $googleSignup = !empty($_SESSION['google_signup_active']) && !empty($_SESSION['google_signup_email']);
        $showGoogleNotice = $googleSignup && !empty($_SESSION['google_signup_prompt']);
        unset($_SESSION['google_signup_prompt']);
        if (!$showGoogleNotice && !$preserveGoogleSignup) {
            unset($_SESSION['google_signup_active'], $_SESSION['google_signup_email'], $_SESSION['google_signup_name'], $_SESSION['google_signup_picture']);
            $googleSignup = false;
        }
        if ($googleSignup) {
            unset($_SESSION['flash_error']);
        }

        $this->view('register/index', [
            'showGoogleNotice' => $showGoogleNotice,
            'googleSignup' => $googleSignup,
        ]);
    }

    public function student()
    {
        $googleSignup = !empty($_SESSION['google_signup_active']) && !empty($_SESSION['google_signup_email']);
        $googleName = $googleSignup ? ($_SESSION['google_signup_name'] ?? '') : '';
        $nameParts = $googleName !== '' ? preg_split('/\s+/', trim($googleName), -1, PREG_SPLIT_NO_EMPTY) : [];

        $prefill = [
            'email' => $googleSignup ? $_SESSION['google_signup_email'] : '',
            'firstname' => $nameParts[0] ?? '',
            'lastname' => $nameParts[1] ?? '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrf = $_POST['_csrf'] ?? '';
            if (!verify_csrf_token($csrf)) {
                $data = ['error' => 'Invalid CSRF token.'];
                return $this->view('register/student', array_merge($prefill, $data));
            }

            $required = ['student_id','firstname','lastname','course','password','confirm_password'];
            foreach ($required as $f) {
                if (empty($_POST[$f])) {
                    $data = ['error' => 'Please fill required fields.'];
                    return $this->view('register/student', array_merge($prefill, $data));
                }
            }

            if ($_POST['password'] !== $_POST['confirm_password']) {
                $data = ['error' => 'Passwords do not match.'];
                return $this->view('register/student', array_merge($prefill, $data));
            }

            $course = $_POST['course'] ?? null;
            if ($course === null || !array_key_exists($course, getCourseOptions())) {
                $data = ['error' => 'Please select a valid course.'];
                return $this->view('register/student', array_merge($prefill, $data));
            }

            $mobile = trim($_POST['mobile'] ?? '');
            if ($mobile !== '' && !isPhilippinesMobileNumber($mobile)) {
                $data = ['error' => 'Enter a valid Philippine mobile number starting with 09 or +639.'];
                return $this->view('register/student', array_merge($prefill, $data));
            }

            $studentModel = new Student_model();
            if ($studentModel->existsByStudentIdOrEmail($_POST['student_id'], $_POST['email'] ?? null)) {
                $data = ['error' => 'Student ID or email already exists.'];
                return $this->view('register/student', array_merge($prefill, $data));
            }

            $profilePath = $googleSignup ? ($_SESSION['google_signup_picture'] ?? null) : null;

            $passwordHash = password_hash($_POST['password'], PASSWORD_DEFAULT);

            $id = $studentModel->create([
                'student_id' => $_POST['student_id'],
                'firstname' => $_POST['firstname'],
                'middlename' => $_POST['middlename'] ?? null,
                'lastname' => $_POST['lastname'],
                'course' => $_POST['course'] ?? null,
                'year_level' => $_POST['year_level'] ?? null,
                'email' => $_POST['email'] ?? null,
                'mobile' => $_POST['mobile'] ?? null,
                'password' => $passwordHash,
                'profile_picture' => $profilePath
            ]);

            $isGoogleSignup = $googleSignup;
            $this->finalizeAuthSession('student', $id, [
                'firstname' => $_POST['firstname'],
                'lastname' => $_POST['lastname'],
                'email' => $_POST['email'] ?? null,
                'mobile' => $_POST['mobile'] ?? null,
                'profile_picture' => $profilePath,
            ], $isGoogleSignup);

            unset($_SESSION['google_signup_active'], $_SESSION['google_signup_email'], $_SESSION['google_signup_name'], $_SESSION['google_signup_picture'], $_SESSION['google_signup_prompt']);
            if ($isGoogleSignup) {
                $_SESSION['flash'] = 'Welcome! Your Google account is ready.';
                redirect(BASE_URL . '/?url=home');
            }

            $_SESSION['flash'] = 'Registration successful. You may now login.';
            redirect(BASE_URL . '/?url=auth/login');
        }

        $this->view('register/student', $prefill);
    }

    public function faculty()
    {
        $googleSignup = !empty($_SESSION['google_signup_active']) && !empty($_SESSION['google_signup_email']);
        $googleName = $googleSignup ? ($_SESSION['google_signup_name'] ?? '') : '';
        $nameParts = $googleName !== '' ? preg_split('/\s+/', trim($googleName), -1, PREG_SPLIT_NO_EMPTY) : [];

        $prefill = [
            'email' => $googleSignup ? $_SESSION['google_signup_email'] : '',
            'firstname' => $nameParts[0] ?? '',
            'lastname' => $nameParts[1] ?? '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrf = $_POST['_csrf'] ?? '';
            if (!verify_csrf_token($csrf)) {
                $data = ['error' => 'Invalid CSRF token.'];
                return $this->view('register/faculty', array_merge($prefill, $data));
            }

            $required = ['faculty_id','firstname','lastname','department','password','confirm_password'];
            foreach ($required as $f) {
                if (empty($_POST[$f])) {
                    $data = ['error' => 'Please fill required fields.'];
                    return $this->view('register/faculty', array_merge($prefill, $data));
                }
            }

            if ($_POST['password'] !== $_POST['confirm_password']) {
                $data = ['error' => 'Passwords do not match.'];
                return $this->view('register/faculty', array_merge($prefill, $data));
            }

            $department = $_POST['department'] ?? null;
            if ($department === null || !array_key_exists($department, getCourseOptions())) {
                $data = ['error' => 'Please select a valid department.'];
                return $this->view('register/faculty', array_merge($prefill, $data));
            }

            $mobile = trim($_POST['mobile'] ?? '');
            if ($mobile !== '' && !isPhilippinesMobileNumber($mobile)) {
                $data = ['error' => 'Enter a valid Philippine mobile number starting with 09 or +639.'];
                return $this->view('register/faculty', array_merge($prefill, $data));
            }

            $facultyModel = new Faculty_model();
            if ($facultyModel->existsByFacultyIdOrEmail($_POST['faculty_id'], $_POST['email'] ?? null)) {
                $data = ['error' => 'Faculty ID or email already exists.'];
                return $this->view('register/faculty', array_merge($prefill, $data));
            }

            $profilePath = $googleSignup ? ($_SESSION['google_signup_picture'] ?? null) : null;

            $passwordHash = password_hash($_POST['password'], PASSWORD_DEFAULT);

            $id = $facultyModel->create([
                'faculty_id' => $_POST['faculty_id'],
                'firstname' => $_POST['firstname'],
                'middlename' => $_POST['middlename'] ?? null,
                'lastname' => $_POST['lastname'],
                'department' => $_POST['department'] ?? null,
                'position' => $_POST['position'] ?? null,
                'email' => $_POST['email'] ?? null,
                'mobile' => $_POST['mobile'] ?? null,
                'password' => $passwordHash,
                'profile_picture' => $profilePath
            ]);

            $isGoogleSignup = $googleSignup;
            $this->finalizeAuthSession('faculty', $id, [
                'firstname' => $_POST['firstname'],
                'lastname' => $_POST['lastname'],
                'email' => $_POST['email'] ?? null,
                'mobile' => $_POST['mobile'] ?? null,
                'profile_picture' => $profilePath,
            ], $isGoogleSignup);

            unset($_SESSION['google_signup_active'], $_SESSION['google_signup_email'], $_SESSION['google_signup_name'], $_SESSION['google_signup_picture'], $_SESSION['google_signup_prompt']);
            if ($isGoogleSignup) {
                $_SESSION['flash'] = 'Welcome! Your Google account is ready.';
                redirect(BASE_URL . '/?url=home');
            }

            $_SESSION['flash'] = 'Registration successful. You may now login.';
            redirect(BASE_URL . '/?url=auth/login');
        }

        $this->view('register/faculty', $prefill);
    }

    private function finalizeAuthSession($role, $userId, $userData = [], $isGoogleSignup = false)
    {
        if (!$isGoogleSignup) {
            return false;
        }

        $table = $role === 'student' ? 'students' : 'faculty';
        $db = Database::getInstance();
        $fullName = trim((($userData['firstname'] ?? '') . ' ' . ($userData['lastname'] ?? '')));
        $providerUserId = $_SESSION['google_signup_email'] ?? ($userData['email'] ?? 'google-' . $userId);

        $db->prepare('UPDATE ' . $table . ' SET auth_provider = :auth_provider, provider_user_id = :provider_user_id, verified_email = :verified_email, verified_phone = :verified_phone, auth_name = :auth_name, profile_picture = :profile_picture, last_login_at = NOW() WHERE id = :id')->execute([
            ':auth_provider' => 'google',
            ':provider_user_id' => (string) $providerUserId,
            ':verified_email' => $userData['email'] ?? null,
            ':verified_phone' => $userData['mobile'] ?? null,
            ':auth_name' => $fullName !== '' ? $fullName : ($_SESSION['google_signup_name'] ?? 'Google User'),
            ':profile_picture' => $userData['profile_picture'] ?? null,
            ':id' => $userId,
        ]);

        $_SESSION['user_role'] = $role;
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $fullName !== '' ? $fullName : ($_SESSION['google_signup_name'] ?? 'Google User');
        $_SESSION['user_profile_picture'] = $userData['profile_picture'] ?? null;

        return true;
    }

    protected function handleUpload($file)
    {
        return parent::handleUpload($file);
    }
}
