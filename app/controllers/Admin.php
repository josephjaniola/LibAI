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
                $upload = $this->handleUpload($_FILES['profile_picture']);
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
                $upload = $this->handleUpload($_FILES['profile_picture']);
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

    public function announcement()
    {
        $this->ensureAdmin();
        $db = Database::getInstance();
        $currentSettings = [];
        $settings = $db->query("SELECT `key`, `value` FROM system_settings WHERE `key` IN ('home_tv_message', 'home_tv_image', 'home_tv_until', 'home_tv_title', 'home_tv_duration_seconds')");
        foreach ($settings as $row) {
            $currentSettings[$row['key']] = $row['value'];
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrf = $_POST['_csrf'] ?? '';
            if (!verify_csrf_token($csrf)) {
                return $this->view('admin/announcement/index', ['error' => 'Invalid CSRF token.', 'announcement' => $currentSettings]);
            }

            $message = trim((string) ($_POST['message'] ?? ''));
            $title = trim((string) ($_POST['title'] ?? 'Admin Announcement'));
            $durationValue = max(1, (int) ($_POST['duration_value'] ?? 30));
            $durationUnit = $_POST['duration_unit'] ?? 'seconds';
            $unitMap = ['seconds' => 1, 'minutes' => 60, 'hours' => 3600];
            $durationSeconds = $durationValue * ($unitMap[$durationUnit] ?? 1);
            $uploadedImagePath = $currentSettings['home_tv_image'] ?? null;

            if (!empty($_FILES['announcement_image']['name'])) {
                $upload = $this->handleAnnouncementUpload($_FILES['announcement_image']);
                if (!$upload['ok']) {
                    return $this->view('admin/announcement/index', ['error' => $upload['error'], 'announcement' => $currentSettings]);
                }
                $uploadedImagePath = $upload['path'];
            }

            if ($message === '' && empty($uploadedImagePath)) {
                return $this->view('admin/announcement/index', ['error' => 'Please write a message or upload an image.', 'announcement' => $currentSettings]);
            }

            $this->saveSystemSetting('home_tv_title', $title === '' ? 'Admin Announcement' : $title);
            $this->saveSystemSetting('home_tv_message', $message);
            $this->saveSystemSetting('home_tv_image', $uploadedImagePath);
            $this->saveSystemSetting('home_tv_duration_seconds', (string) $durationSeconds);
            $this->saveSystemSetting('home_tv_until', date('Y-m-d H:i:s', time() + $durationSeconds));

            $this->sendAnnouncementEmail($title === '' ? 'Admin Announcement' : $title, $message, $uploadedImagePath);

            $_SESSION['tv_announcement'] = $message;
            $_SESSION['tv_announcement_expires_at'] = time() + $durationSeconds;
            $_SESSION['flash'] = 'Homepage announcement updated. It will return to the library gallery after ' . $durationValue . ' ' . rtrim($durationUnit, 's') . (($durationValue > 1) ? 's' : '') . '.';
            redirect(BASE_URL . '/?url=admin/announcement');
        }

        $this->view('admin/announcement/index', ['announcement' => $currentSettings]);
    }

    private function saveSystemSetting($key, $value)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('INSERT INTO system_settings (`key`, `value`) VALUES (:key, :value) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)');
        $stmt->execute([':key' => $key, ':value' => $value]);
    }

    private function sendAnnouncementEmail($title, $message, $imagePath = null)
    {
        $admins = (new Admin_model())->getAll();
        $recipients = [];

        foreach ($admins as $admin) {
            if (!empty($admin['email'])) {
                $recipients[] = $admin['email'];
            }
        }

        if (empty($recipients)) {
            $recipients[] = MAIL_FROM_ADDRESS;
        }

        $recipients = array_values(array_unique(array_filter(array_map('trim', $recipients))));
        if (empty($recipients)) {
            return;
        }

        $body = '<h3>' . e($title) . '</h3>';
        $body .= '<p>' . nl2br(e($message)) . '</p>';

        if (!empty($imagePath)) {
            $body .= '<p><img src="' . e(APP_BASE_URL . '/' . $imagePath) . '" alt="Announcement image" style="max-width: 500px; width: 100%; border-radius: 10px;" /></p>';
        }

        foreach ($recipients as $recipient) {
            try {
                $mailer = new Mailer();
                $mailer->send($recipient, 'LibAI Announcement: ' . $title, $body);
            } catch (Exception $ex) {
                error_log('LibAI announcement email failed: ' . $ex->getMessage());
            }
        }
    }

    private function handleAnnouncementUpload($file)
    {
        $allowed = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        if ($file['error'] !== UPLOAD_ERR_OK) return ['ok' => false, 'error' => 'Upload error.'];
        if ($file['size'] > 4 * 1024 * 1024) return ['ok' => false, 'error' => 'Image too large (max 4MB).'];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        if (!in_array($mime, $allowed)) return ['ok' => false, 'error' => 'Invalid image type.'];

        $ext = match ($mime) {
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => 'jpg',
        };

        $destDir = __DIR__ . '/../../uploads';
        if (!is_dir($destDir)) mkdir($destDir, 0755, true);

        $name = 'announcement_' . uniqid() . '.' . $ext;
        $dest = $destDir . '/' . $name;
        if (!move_uploaded_file($file['tmp_name'], $dest)) return ['ok' => false, 'error' => 'Failed to move uploaded file.'];

        return ['ok' => true, 'path' => 'uploads/' . $name];
    }
}
