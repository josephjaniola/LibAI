<?php
class Register extends Controller
{
    public function index()
    {
        $this->view('register/index');
    }

    public function student()
    {
        $prefill = [
            'email' => $_SESSION['google_signup_email'] ?? '',
            'firstname' => $_SESSION['google_signup_name'] ? explode(' ', trim($_SESSION['google_signup_name']))[0] : '',
            'lastname' => $_SESSION['google_signup_name'] ? (isset(explode(' ', trim($_SESSION['google_signup_name']))[1]) ? explode(' ', trim($_SESSION['google_signup_name']))[1] : '') : '',
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
        $prefill = [
            'email' => $_SESSION['google_signup_email'] ?? '',
            'firstname' => $_SESSION['google_signup_name'] ? explode(' ', trim($_SESSION['google_signup_name']))[0] : '',
            'lastname' => $_SESSION['google_signup_name'] ? (isset(explode(' ', trim($_SESSION['google_signup_name']))[1]) ? explode(' ', trim($_SESSION['google_signup_name']))[1] : '') : '',
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

    private function handleUpload($file)
    {
        $allowed = ['image/jpeg','image/png','image/jpg'];
        if ($file['error'] !== UPLOAD_ERR_OK) return ['ok' => false, 'error' => 'Upload error.'];
        if ($file['size'] > 2 * 1024 * 1024) return ['ok' => false, 'error' => 'File too large (max 2MB).'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        if (!in_array($mime, $allowed)) return ['ok' => false, 'error' => 'Invalid file type.'];

        $ext = $mime === 'image/png' ? 'png' : 'jpg';
        $name = uniqid('prof_') . '.' . $ext;
        $destDir = __DIR__ . '/../../uploads/profiles';
        if (!is_dir($destDir)) mkdir($destDir, 0755, true);
        $dest = $destDir . '/' . $name;
        if (!move_uploaded_file($file['tmp_name'], $dest)) return ['ok' => false, 'error' => 'Failed to move uploaded file.'];
        return ['ok' => true, 'path' => 'uploads/profiles/' . $name];
    }
}
