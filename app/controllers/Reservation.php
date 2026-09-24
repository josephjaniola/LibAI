<?php
class Reservation extends Controller
{
    private function ensureLibrarianOrAdmin()
    {
        if (empty($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['admin','librarian'])) {
            redirect(BASE_URL . '/?url=auth/login');
        }
    }

    public function index()
    {
        $this->ensureLibrarianOrAdmin();
        $reservations = (new Reservation_model())->getAll();
        $this->view('admin/reservations/index', ['reservations' => $reservations]);
    }

    public function createForm()
    {
        $this->ensureLibrarianOrAdmin();
        $books = (new Book_model())->getAvailable();
        $this->view('admin/reservations/form', ['books' => $books]);
    }

    public function create()
    {
        $this->ensureLibrarianOrAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect(BASE_URL . '/?url=reservation/index');
        $csrf = $_POST['_csrf'] ?? '';
        if (!verify_csrf_token($csrf)) {
            $_SESSION['flash_error'] = 'Invalid CSRF token.';
            redirect(BASE_URL . '/?url=reservation/createForm');
        }

        $borrower_type = $_POST['borrower_type'] ?? '';
        $borrower_ref_id = trim($_POST['borrower_ref_id'] ?? '');
        $book_id = intval($_POST['book_id'] ?? 0);

        if (!$borrower_type || !$borrower_ref_id || !$book_id) {
            $_SESSION['flash_error'] = 'Please provide borrower type, borrower reference, and book.';
            redirect(BASE_URL . '/?url=reservation/createForm');
        }

        $book = (new Book_model())->findById($book_id);
        if (!$book) {
            $_SESSION['flash_error'] = 'Book not found.';
            redirect(BASE_URL . '/?url=reservation/createForm');
        }
        if ($book['status'] !== 'available') {
            $_SESSION['flash_error'] = 'Book is not available for reservation.';
            redirect(BASE_URL . '/?url=reservation/createForm');
        }

        $name = '';
        $email = null;
        if ($borrower_type === 'student') {
            $u = (new Student_model())->findByStudentIdOrEmail($borrower_ref_id);
            $name = $u ? ($u['firstname'].' '.$u['lastname']) : $borrower_ref_id;
            $email = $u['email'] ?? null;
        } else {
            $u = (new Faculty_model())->findByFacultyIdOrEmail($borrower_ref_id);
            $name = $u ? ($u['firstname'].' '.$u['lastname']) : $borrower_ref_id;
            $email = $u['email'] ?? null;
        }

        $reservationId = (new Reservation_model())->create([
            'book_id' => $book_id,
            'rfid_uid' => $book['rfid_uid'] ?? null,
            'borrower_type' => $borrower_type,
            'borrower_ref_id' => $borrower_ref_id,
            'reserved_at' => date('Y-m-d H:i:s'),
            'status' => 'pending',
            'expires_at' => date('Y-m-d H:i:s', strtotime('+3 days'))
        ]);

        (new Book_model())->updateById($book_id, ['status' => 'reserved']);

        if (!empty($email)) {
            try {
                $mailer = new Mailer();
                $subject = APP_NAME . ' - Reservation Confirmation';
                $body = "<p>Dear " . e($name) . ",</p><p>Your reservation for <strong>" . e($book['title']) . "</strong> has been recorded.</p><p>Reservation expires: " . date('Y-m-d H:i:s', strtotime('+3 days')) . "</p>";
                $mailer->send($email, $subject, $body);
            } catch (Exception $ex) {
                $db = Database::getInstance();
                $stmt = $db->prepare('INSERT INTO activity_logs (user_type, user_ref_id, action, detail) VALUES (:ut, :uid, :act, :det)');
                $stmt->execute([':ut'=>'system', ':uid'=>0, ':act'=>'mail_error', ':det'=>$ex->getMessage()]);
            }
        }

        (new Notification_model())->create([
            'user_type' => $borrower_type,
            'user_ref_id' => is_numeric($borrower_ref_id) ? intval($borrower_ref_id) : 0,
            'title' => 'Reservation Created',
            'message' => "Your reservation for {$book['title']} is pending and will expire on " . date('Y-m-d H:i:s', strtotime('+3 days')) . ".",
            'type' => 'reservation'
        ]);

        $_SESSION['flash'] = 'Reservation recorded.';
        redirect(BASE_URL . '/?url=reservation/index');
    }

    public function ready($id = null)
    {
        $this->ensureLibrarianOrAdmin();
        if (!$id) redirect(BASE_URL . '/?url=reservation/index');

        $reservation = (new Reservation_model())->getById($id);
        if (!$reservation) {
            $_SESSION['flash_error'] = 'Reservation not found.';
            redirect(BASE_URL . '/?url=reservation/index');
        }

        (new Reservation_model())->markReady($id);

        $borrowerEmail = null;
        $borrowerName = $reservation['borrower_ref_id'];
        if ($reservation['borrower_type'] === 'student') {
            $u = (new Student_model())->findByStudentIdOrEmail($reservation['borrower_ref_id']);
            $borrowerEmail = $u['email'] ?? null;
            $borrowerName = $u ? ($u['firstname'].' '.$u['lastname']) : $borrowerName;
        } else {
            $u = (new Faculty_model())->findByFacultyIdOrEmail($reservation['borrower_ref_id']);
            $borrowerEmail = $u['email'] ?? null;
            $borrowerName = $u ? ($u['firstname'].' '.$u['lastname']) : $borrowerName;
        }

        if (!empty($borrowerEmail)) {
            try {
                $mailer = new Mailer();
                $subject = APP_NAME . ' - Reservation Ready for Pickup';
                $body = "<p>Dear " . e($borrowerName) . ",</p><p>Your reserved book <strong>" . e($reservation['title']) . "</strong> is now ready for pickup.</p><p>Please collect it within 2 days.</p>";
                $mailer->send($borrowerEmail, $subject, $body);
            } catch (Exception $ex) {
                $db = Database::getInstance();
                $stmt = $db->prepare('INSERT INTO activity_logs (user_type, user_ref_id, action, detail) VALUES (:ut, :uid, :act, :det)');
                $stmt->execute([':ut'=>'system', ':uid'=>0, ':act'=>'mail_error', ':det'=>$ex->getMessage()]);
            }
        }

        (new Notification_model())->create([
            'user_type' => $reservation['borrower_type'],
            'user_ref_id' => intval($reservation['borrower_ref_id']),
            'title' => 'Reservation Ready',
            'message' => "Your reservation for {$reservation['title']} is ready for pickup.",
            'type' => 'reservation_ready'
        ]);

        $_SESSION['flash'] = 'Reservation marked ready.';
        redirect(BASE_URL . '/?url=reservation/index');
    }

    public function cancel($id = null)
    {
        $this->ensureLibrarianOrAdmin();
        if (!$id) redirect(BASE_URL . '/?url=reservation/index');

        $reservation = (new Reservation_model())->getById($id);
        if (!$reservation) {
            $_SESSION['flash_error'] = 'Reservation not found.';
            redirect(BASE_URL . '/?url=reservation/index');
        }

        (new Reservation_model())->cancel($id);
        (new Book_model())->updateById($reservation['book_id'], ['status' => 'available']);

        $_SESSION['flash'] = 'Reservation cancelled.';
        redirect(BASE_URL . '/?url=reservation/index');
    }
}
