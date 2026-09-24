<?php
class Register extends Controller
{
    public function index()
    {
        $this->view('register/index');
    }

    public function student()
    {
        $googleName = $_SESSION['google_signup_name'] ?? '';
        $nameParts = $googleName !== '' ? preg_split('/\s+/', trim($googleName), -1, PREG_SPLIT_NO_EMPTY) : [];

        $prefill = [
            'email' => $_SESSION['google_signup_email'] ?? '',
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

            $studentModel = new Student_model();
            if ($studentModel->existsByStudentIdOrEmail($_POST['student_id'], $_POST['email'] ?? null)) {
                $data = ['error' => 'Student ID or email already exists.'];
                return $this->view('register/student', array_merge($prefill, $data));
            }

            $profilePath = null;
            if (!empty($_FILES['profile_picture']['name'])) {
                $upload = $this->handleUpload($_FILES['profile_picture']);
                if (!$upload['ok']) {
                    return $this->view('register/student', array_merge($prefill, ['error' => $upload['error']]));
                }
                $profilePath = $upload['path'];
            }

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

            unset($_SESSION['google_signup_email'], $_SESSION['google_signup_name']);
            $_SESSION['flash'] = 'Registration successful. You may now login.';
            redirect(BASE_URL . '/?url=auth/login');
        }

        $this->view('register/student', $prefill);
    }

    public function faculty()
    {
        $googleName = $_SESSION['google_signup_name'] ?? '';
        $nameParts = $googleName !== '' ? preg_split('/\s+/', trim($googleName), -1, PREG_SPLIT_NO_EMPTY) : [];

        $prefill = [
            'email' => $_SESSION['google_signup_email'] ?? '',
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

            $facultyModel = new Faculty_model();
            if ($facultyModel->existsByFacultyIdOrEmail($_POST['faculty_id'], $_POST['email'] ?? null)) {
                $data = ['error' => 'Faculty ID or email already exists.'];
                return $this->view('register/faculty', array_merge($prefill, $data));
            }

            $profilePath = null;
            if (!empty($_FILES['profile_picture']['name'])) {
                $upload = $this->handleUpload($_FILES['profile_picture']);
                if (!$upload['ok']) {
                    return $this->view('register/faculty', array_merge($prefill, ['error' => $upload['error']]));
                }
                $profilePath = $upload['path'];
            }

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

            unset($_SESSION['google_signup_email'], $_SESSION['google_signup_name']);
            $_SESSION['flash'] = 'Registration successful. You may now login.';
            redirect(BASE_URL . '/?url=auth/login');
        }

        $this->view('register/faculty', $prefill);
    }

    protected function handleUpload($file)
    {
        return parent::handleUpload($file);
    }
}
