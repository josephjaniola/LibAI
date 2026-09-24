<?php
class Admin extends Controller
{
    private function ensureAdmin()
    {
        if (empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            redirect(BASE_URL . '/?url=auth/login');
        }
    }

    public function index()
    {
        $this->ensureAdmin();
        redirect(BASE_URL . '/?url=admin/librarians');
    }

    public function librarians()
    {
        $this->ensureAdmin();
        $model = new Librarian_model();
        $librarians = $model->getAll();
        $this->view('admin/librarians/index', ['librarians' => $librarians]);
    }

    public function addLibrarian()
    {
        $this->ensureAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrf = $_POST['_csrf'] ?? '';
            if (!verify_csrf_token($csrf)) return $this->view('admin/librarians/add', ['error' => 'Invalid CSRF token.']);

            $required = ['librarian_id','email','password','confirm_password','firstname','lastname'];
            foreach ($required as $f) if (empty($_POST[$f])) return $this->view('admin/librarians/add', ['error' => 'Please fill required fields.']);
            if ($_POST['password'] !== $_POST['confirm_password']) return $this->view('admin/librarians/add', ['error' => 'Passwords do not match.']);

            $model = new Librarian_model();
            if ($model->existsByIdOrEmail($_POST['librarian_id'], $_POST['email'])) return $this->view('admin/librarians/add', ['error' => 'Librarian ID or email already exists.']);

            $profilePath = null;
            if (!empty($_FILES['profile_picture']['name'])) {
                $upload = (new Register())->handleUpload($_FILES['profile_picture']);
                if (!$upload['ok']) return $this->view('admin/librarians/add', ['error' => $upload['error']]);
                $profilePath = $upload['path'];
            }

            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $model->create([
                'librarian_id' => $_POST['librarian_id'],
                'username' => $_POST['username'] ?? null,
                'email' => $_POST['email'],
                'password' => $password,
                'firstname' => $_POST['firstname'],
                'middlename' => $_POST['middlename'] ?? null,
                'lastname' => $_POST['lastname'],
                'mobile' => $_POST['mobile'] ?? null,
                'profile_picture' => $profilePath
            ]);

            $_SESSION['flash'] = 'Librarian added.';
            redirect(BASE_URL . '/?url=admin/librarians');
        }
        $this->view('admin/librarians/add');
    }

    public function editLibrarian($id = null)
    {
        $this->ensureAdmin();
        if (!$id) return redirect(BASE_URL . '/?url=admin/librarians');
        $model = new Librarian_model();
        $librarian = $model->getById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrf = $_POST['_csrf'] ?? '';
            if (!verify_csrf_token($csrf)) return $this->view('admin/librarians/edit', ['error' => 'Invalid CSRF token.', 'librarian' => $librarian]);

            $data = [];
            if (!empty($_POST['username'])) $data['username'] = $_POST['username'];
            if (!empty($_POST['email'])) $data['email'] = $_POST['email'];
            if (!empty($_POST['firstname'])) $data['firstname'] = $_POST['firstname'];
            if (isset($_POST['middlename'])) $data['middlename'] = $_POST['middlename'];
            if (!empty($_POST['lastname'])) $data['lastname'] = $_POST['lastname'];
            if (!empty($_POST['mobile'])) $data['mobile'] = $_POST['mobile'];
            if (!empty($_POST['password'])) $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);

            if (!empty($_FILES['profile_picture']['name'])) {
                $upload = (new Register())->handleUpload($_FILES['profile_picture']);
                if (!$upload['ok']) return $this->view('admin/librarians/edit', ['error' => $upload['error'], 'librarian' => $librarian]);
                $data['profile_picture'] = $upload['path'];
            }

            $model->updateById($id, $data);
            $_SESSION['flash'] = 'Librarian updated.';
            redirect(BASE_URL . '/?url=admin/librarians');
        }

        $this->view('admin/librarians/edit', ['librarian' => $librarian]);
    }

    public function deleteLibrarian($id = null)
    {
        $this->ensureAdmin();
        if (!$id) return redirect(BASE_URL . '/?url=admin/librarians');
        $model = new Librarian_model();
        $model->deleteById($id);
        $_SESSION['flash'] = 'Librarian removed.';
        redirect(BASE_URL . '/?url=admin/librarians');
    }
}
