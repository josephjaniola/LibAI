<?php
class Profile extends Controller
{
    private function ensureAuthenticated()
    {
        if (empty($_SESSION['user_role']) || empty($_SESSION['user_id'])) {
            redirect(BASE_URL . '/?url=auth/login');
        }
    }

    public function index()
    {
        $this->ensureAuthenticated();
        $role = $_SESSION['user_role'];
        $id = $_SESSION['user_id'];
        $model = $this->getModelForRole($role);
        $user = $model->getById($id);
        $this->view('profile/view', ['user' => $user, 'role' => $role]);
    }

    public function view($view = null, $data = [])
    {
        if ($view !== null) {
            return parent::view($view, $data);
        }
        return $this->index();
    }

    public function history()
    {
        $this->ensureAuthenticated();
        $role = $_SESSION['user_role'];
        $id = $_SESSION['user_id'];
        $borrowHistory = (new Borrow_model())->getByBorrower($role, $id);
        $reservations = (new Reservation_model())->getByBorrower($role, $id);
        $this->view('profile/history', ['borrowHistory' => $borrowHistory, 'reservations' => $reservations, 'role' => $role]);
    }

    public function notifications()
    {
        $this->ensureAuthenticated();
        $role = $_SESSION['user_role'];
        $id = $_SESSION['user_id'];
        $notifications = (new Notification_model())->getByUser($role, $id);
        $this->view('profile/notifications', ['notifications' => $notifications, 'role' => $role]);
    }

    public function favorites()
    {
        $this->ensureAuthenticated();
        $this->view('profile/favorites', ['role' => $_SESSION['user_role']]);
    }

    public function edit()
    {
        if (empty($_SESSION['user_role']) || empty($_SESSION['user_id'])) {
            redirect(BASE_URL . '/?url=auth/login');
        }
        $role = $_SESSION['user_role'];
        $id = $_SESSION['user_id'];
        $model = $this->getModelForRole($role);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrf = $_POST['_csrf'] ?? '';
            if (!verify_csrf_token($csrf)) {
                $this->view('profile/edit', ['error' => 'Invalid CSRF token.']);
                return;
            }

            $data = [];
            if (!empty($_POST['firstname'])) $data['firstname'] = $_POST['firstname'];
            if (isset($_POST['middlename'])) $data['middlename'] = $_POST['middlename'];
            if (!empty($_POST['lastname'])) $data['lastname'] = $_POST['lastname'];
            if (!empty($_POST['email'])) $data['email'] = $_POST['email'];
            if (!empty($_POST['mobile'])) $data['mobile'] = $_POST['mobile'];
            if (!empty($_POST['password'])) $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);

            if (!empty($_FILES['profile_picture']['name'])) {
                $upload = (new Register())->handleUpload($_FILES['profile_picture']);
                if (!$upload['ok']) {
                    $this->view('profile/edit', ['error' => $upload['error']]);
                    return;
                }
                $data['profile_picture'] = $upload['path'];
            }

            $ok = $model->updateById($id, $data);
            if ($ok) {
                $_SESSION['flash'] = 'Profile updated.';
                redirect(BASE_URL . '/?url=profile/view');
            } else {
                $this->view('profile/edit', ['error' => 'No changes saved.']);
            }
            return;
        }

        $user = $model->getById($id);
        $this->view('profile/edit', ['user' => $user, 'role' => $role]);
    }

    private function getModelForRole($role)
    {
        switch ($role) {
            case 'student': return new Student_model();
            case 'faculty': return new Faculty_model();
            case 'librarian': return new Librarian_model();
            case 'admin': return new Admin_model();
            default: return new Student_model();
        }
    }
}
