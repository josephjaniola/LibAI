<?php
class Book extends Controller
{
    private function ensureAuthenticated()
    {
        if (empty($_SESSION['user_role'])) {
            redirect(BASE_URL . '/?url=auth/login');
        }
    }

    private function ensureLibrarianOrAdmin()
    {
        if (empty($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['admin','librarian'])) {
            redirect(BASE_URL . '/?url=auth/login');
        }
    }

    private function ensureStudentOrFaculty()
    {
        if (empty($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['student', 'faculty'], true)) {
            redirect(BASE_URL . '/?url=auth/login');
        }
    }

    public function index()
    {
        $this->ensureAuthenticated();
        $model = new Book_model();
        $books = $model->getAll();
        $userReservations = [];

        if (in_array($_SESSION['user_role'] ?? '', ['student', 'faculty'], true)) {
            $role = $_SESSION['user_role'];
            $userId = $_SESSION['user_id'] ?? null;
            $borrowerRefId = null;

            if ($role === 'student') {
                $user = (new Student_model())->getById($userId);
                $borrowerRefId = $user['student_id'] ?? null;
            } else {
                $user = (new Faculty_model())->getById($userId);
                $borrowerRefId = $user['faculty_id'] ?? null;
            }

            $idsToCheck = array_unique(array_filter([$borrowerRefId, (string) ($userId ?? ''), (int) ($userId ?? 0)]));
            foreach ($idsToCheck as $candidateId) {
                if ($candidateId === '' || $candidateId === null) {
                    continue;
                }
                foreach ((new Reservation_model())->getPendingByBorrower($role, $candidateId) as $reservation) {
                    $userReservations[(int) $reservation['book_id']] = $reservation;
                }
            }
        }

        $this->view('admin/books/index', ['books' => $books, 'userReservations' => $userReservations]);
    }

    public function reserve($id = null)
    {
        $this->ensureStudentOrFaculty();
        if (!$id) {
            redirect(BASE_URL . '/?url=book/index');
        }

        $book = (new Book_model())->findById((int) $id);
        if (!$book) {
            $_SESSION['flash_error'] = 'Book not found.';
            redirect(BASE_URL . '/?url=book/index');
        }

        if ($book['status'] !== 'available') {
            $_SESSION['flash_error'] = 'This book is not available for reservation.';
            redirect(BASE_URL . '/?url=book/index');
        }

        $role = $_SESSION['user_role'];
        $userId = $_SESSION['user_id'] ?? null;
        $user = $role === 'student' ? (new Student_model())->getById($userId) : (new Faculty_model())->getById($userId);
        $borrowerRefId = $role === 'student' ? ($user['student_id'] ?? '') : ($user['faculty_id'] ?? '');

        if ($borrowerRefId === '') {
            $_SESSION['flash_error'] = 'Your account is missing a valid student or faculty ID.';
            redirect(BASE_URL . '/?url=book/index');
        }

        $existing = (new Reservation_model())->getPendingByBorrower($role, $borrowerRefId);
        foreach ($existing as $reservation) {
            if ((int) $reservation['book_id'] === (int) $book['id']) {
                $_SESSION['flash_error'] = 'You already have an active reservation for this book.';
                redirect(BASE_URL . '/?url=profile/history');
            }
        }

        $expiresAt = date('Y-m-d H:i:s', strtotime('+3 days'));
        (new Reservation_model())->create([
            'book_id' => $book['id'],
            'rfid_uid' => $book['rfid_uid'] ?? null,
            'borrower_type' => $role,
            'borrower_ref_id' => $borrowerRefId,
            'reserved_at' => date('Y-m-d H:i:s'),
            'status' => 'pending',
            'expires_at' => $expiresAt,
        ]);

        (new Book_model())->updateById($book['id'], ['status' => 'reserved']);

        $userEmail = $user['email'] ?? null;
        $userName = ($user['firstname'] ?? '') . ' ' . ($user['lastname'] ?? '');
        if (!empty($userEmail)) {
            try {
                $mailer = new Mailer();
                $subject = APP_NAME . ' - Reservation Confirmation';
                $body = "<p>Dear " . e(trim($userName)) . ",</p><p>You reserved <strong>" . e($book['title']) . "</strong>.</p><p>Reservation expires: " . e($expiresAt) . "</p>";
                $mailer->send($userEmail, $subject, $body);
            } catch (Exception $ex) {
                // log silently if mail fails
            }
        }

        (new Notification_model())->create([
            'user_type' => $role,
            'user_ref_id' => (int) $userId,
            'title' => 'Book Reserved',
            'message' => "You reserved {$book['title']}. Please collect it before {$expiresAt}.",
            'type' => 'reservation'
        ]);

        $_SESSION['flash'] = 'Book reserved successfully. Please collect it before ' . $expiresAt . '.';
        redirect(BASE_URL . '/?url=profile/history');
    }

    public function add()
    {
        $this->ensureLibrarianOrAdmin();
        $categories = seedCourseCategories();
        $publishers = (new Publisher_model())->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrf = $_POST['_csrf'] ?? '';
            if (!verify_csrf_token($csrf)) return $this->view('admin/books/add', ['error' => 'Invalid CSRF token.', 'categories'=>$categories,'publishers'=>$publishers]);

            $uploadPath = null;
            if (!empty($_FILES['cover_image']['name'])) {
                $up = $this->handleUpload($_FILES['cover_image']);
                if (!$up['ok']) return $this->view('admin/books/add', ['error'=>$up['error'],'categories'=>$categories,'publishers'=>$publishers]);
                $uploadPath = $up['path'];
            }

            $bookModel = new Book_model();
            $bookModel->create([
                'title'=>$_POST['title'], 'subtitle'=>$_POST['subtitle'] ?? null, 'isbn'=>$_POST['isbn'] ?? null,
                'accession_number'=>$_POST['accession_number'] ?? null, 'call_number'=>$_POST['call_number'] ?? null, 'edition'=>$_POST['edition'] ?? null,
                'volume'=>$_POST['volume'] ?? null, 'pages'=>$_POST['pages'] ?? null, 'category_id'=>$_POST['category_id'] ?? null,
                'language'=>$_POST['language'] ?? null, 'shelf_location'=>$_POST['shelf_location'] ?? null, 'year_published'=>$_POST['year_published'] ?? null,
                'description'=>$_POST['description'] ?? null, 'keywords'=>$_POST['keywords'] ?? null, 'remarks'=>$_POST['remarks'] ?? null,
                'cover_image'=>$uploadPath, 'publisher_id'=>$_POST['publisher_id'] ?? null, 'date_received'=>$_POST['date_received'] ?? null,
                'rfid_uid'=>$_POST['rfid_uid'] ?? null, 'status'=>$_POST['status'] ?? 'available'
            ]);

            $_SESSION['flash'] = 'Book added.';
            redirect(BASE_URL . '/?url=book/index');
        }

        $this->view('admin/books/add', ['categories'=>$categories,'publishers'=>$publishers]);
    }

    public function edit($id = null)
    {
        $this->ensureLibrarianOrAdmin();
        if (!$id) return redirect(BASE_URL . '/?url=book/index');
        $bookModel = new Book_model();
        $book = $bookModel->findById($id);
        $categories = seedCourseCategories();
        $publishers = (new Publisher_model())->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrf = $_POST['_csrf'] ?? '';
            if (!verify_csrf_token($csrf)) return $this->view('admin/books/edit', ['error'=>'Invalid CSRF token.','book'=>$book,'categories'=>$categories,'publishers'=>$publishers]);

            $data = [];
            foreach (['title','subtitle','isbn','accession_number','call_number','edition','volume','pages','category_id','language','shelf_location','year_published','description','keywords','remarks','publisher_id','date_received','rfid_uid','status'] as $f) {
                if (isset($_POST[$f])) $data[$f] = $_POST[$f] === '' ? null : $_POST[$f];
            }

            if (!empty($_FILES['cover_image']['name'])) {
                $up = $this->handleUpload($_FILES['cover_image']);
                if (!$up['ok']) return $this->view('admin/books/edit', ['error'=>$up['error'],'book'=>$book,'categories'=>$categories,'publishers'=>$publishers]);
                $data['cover_image'] = $up['path'];
            }

            $bookModel->updateById($id, $data);
            $_SESSION['flash'] = 'Book updated.';
            redirect(BASE_URL . '/?url=book/index');
        }

        $this->view('admin/books/edit', ['book'=>$book,'categories'=>$categories,'publishers'=>$publishers]);
    }

    public function delete($id = null)
    {
        $this->ensureLibrarianOrAdmin();
        if (!$id) return redirect(BASE_URL . '/?url=book/index');

        $bookModel = new Book_model();
        try {
            $deleted = $bookModel->deleteById($id);
        } catch (PDOException $ex) {
            $deleted = false;
            $error = $ex->getMessage();
        }

        if ($deleted) {
            $_SESSION['flash'] = 'Book deleted.';
        } else {
            // If the book is referenced by borrow history, archive it instead.
            $bookModel->updateById($id, ['status' => 'archived']);
            $_SESSION['flash'] = 'Book could not be deleted because it is linked to borrow history. It has been archived instead.';
        }

        redirect(BASE_URL . '/?url=book/index');
    }

    // RFID scan endpoint: returns JSON book info by UID
    public function scanRfid()
    {
        header('Content-Type: application/json');
        $uid = $_GET['uid'] ?? null;
        if (!$uid) { echo json_encode(['ok'=>false,'error'=>'No UID provided']); return; }
        $book = (new Book_model())->findByRfid($uid);
        if ($book) {
            echo json_encode(['ok'=>true,'book'=>$book]);
        } else {
            echo json_encode(['ok'=>false,'error'=>'Book not found']);
        }
    }
}
