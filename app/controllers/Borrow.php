<?php
class Borrow extends Controller
{
    private function ensureLibrarian()
    {
        if (empty($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['librarian','admin'])) {
            redirect(BASE_URL . '/?url=auth/login');
        }
    }

    public function index()
    {
        $this->ensureLibrarian();
        // Borrow page: search borrower then scan book
        $this->view('librarian/borrow/index');
    }

    public function create()
    {
        $this->ensureLibrarian();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect(BASE_URL . '/?url=borrow/index');
        $csrf = $_POST['_csrf'] ?? '';
        if (!verify_csrf_token($csrf)) { $_SESSION['flash_error'] = 'Invalid CSRF token.'; redirect(BASE_URL . '/?url=borrow/index'); }

        if (($_POST['action'] ?? '') === 'ready') {
            $this->prepareReservationForPickup();
        }

        $borrower_type = $_POST['borrower_type'];
        $borrower_ref_id = trim((string) ($_POST['borrower_ref_id'] ?? ''));
        $rfid = trim((string) ($_POST['rfid_uid'] ?? ''));
        $due_date = $_POST['due_date'] ?? date('Y-m-d H:i:s', strtotime('+14 days'));

        if (!in_array($borrower_type, ['student', 'faculty'], true) || $borrower_ref_id === '') {
            $_SESSION['flash_error'] = 'Please enter and look up a valid student or faculty borrower.';
            redirect(BASE_URL . '/?url=borrow/index');
        }

        if ($rfid === '') {
            $_SESSION['flash_error'] = 'Please scan or enter a valid book RFID.';
            redirect(BASE_URL . '/?url=borrow/index');
        }

        $book = (new Book_model())->findByRfid($rfid);
        if (!$book) {
            $_SESSION['flash_error'] = 'No book was found with that RFID.';
            redirect(BASE_URL . '/?url=borrow/index');
        }

        $activeReservation = (new Reservation_model())->findActive($book['id']);
        $bookIsReadyForPickup = $book['status'] === 'ready' || ($activeReservation && $activeReservation['status'] === 'ready');

        if (!in_array($book['status'], ['available', 'reserved', 'ready'], true)) {
            $_SESSION['flash_error'] = 'This book cannot be borrowed because its status is ' . $book['status'] . '.';
            redirect(BASE_URL . '/?url=borrow/index');
        }

        if ($book['status'] !== 'available' && !$bookIsReadyForPickup) {
            $_SESSION['flash_error'] = 'This reservation must be accepted and marked Ready to Pick Up first.';
            redirect(BASE_URL . '/?url=borrow/index');
        }

        if ($bookIsReadyForPickup) {
            if (!$activeReservation) {
                $_SESSION['flash_error'] = 'This ready book is not linked to an active reservation.';
                redirect(BASE_URL . '/?url=borrow/index');
            }
            if ($activeReservation['borrower_type'] !== $borrower_type || (string) $activeReservation['borrower_ref_id'] !== (string) $borrower_ref_id) {
                $_SESSION['flash_error'] = 'This book is reserved for a different borrower.';
                redirect(BASE_URL . '/?url=borrow/index');
            }
        }

        // lookup borrower name/email/phone
        if ($borrower_type === 'student') {
            $u = (new Student_model())->findByStudentIdOrEmail($borrower_ref_id);
            $name = $u ? ($u['firstname'].' '.$u['lastname']) : '';
            $email = $u['email'] ?? null;
            $phone = $u['mobile'] ?? null;
        } else {
            $u = (new Faculty_model())->findByFacultyIdOrEmail($borrower_ref_id);
            $name = $u ? ($u['firstname'].' '.$u['lastname']) : '';
            $email = $u['email'] ?? null;
            $phone = $u['mobile'] ?? null;
        }

        if (!$u) {
            $_SESSION['flash_error'] = 'Borrower not found. Check the student or faculty ID/email.';
            redirect(BASE_URL . '/?url=borrow/index');
        }

        $borrowModel = new Borrow_model();
        $txId = $borrowModel->createTransaction([
            'borrower_type'=>$borrower_type,
            'borrower_ref_id'=>$borrower_ref_id,
            'borrower_name'=>$name,
            'borrower_email'=>$email,
            'borrower_phone'=>$phone,
            'book_id'=>$book['id'],
            'rfid_uid'=>$rfid,
            'due_date'=>$due_date
        ]);

        // update book status
        (new Book_model())->updateById($book['id'], ['status' => 'borrowed']);

        if ($activeReservation) {
            (new Reservation_model())->markBorrowed($activeReservation['id']);
        }

        // send borrow confirmation email if possible
        if (!empty($email)) {
            try {
                $mailer = new Mailer();
                $subject = APP_NAME . ' - Borrow Confirmation';
                $body = "<p>Dear " . e($name) . ",</p><p>You have borrowed: <strong>" . e($book['title']) . "</strong>.</p><p>Borrow Date: " . date('Y-m-d H:i:s') . "<br>Due Date: " . e($due_date) . "</p>";
                $mailer->send($email, $subject, $body);
            } catch (Exception $ex) {
                $db = Database::getInstance();
                $stmt = $db->prepare('INSERT INTO activity_logs (user_type, user_ref_id, action, detail) VALUES (:ut, :uid, :act, :det)');
                $stmt->execute([':ut'=>'system', ':uid'=>0, ':act'=>'mail_error', ':det'=>$ex->getMessage()]);
            }
        }

        (new Notification_model())->create([
            'user_type' => $borrower_type,
            'user_ref_id' => (int) $u['id'],
            'title' => 'Borrow Confirmed',
            'message' => "You have borrowed {$book['title']} and it is due on {$due_date}.",
            'type' => 'borrow'
        ]);

        $_SESSION['flash'] = 'Borrow successful.';
        redirect(BASE_URL . '/?url=borrow/index');
    }

    private function prepareReservationForPickup()
    {
        $borrowerType = $_POST['borrower_type'] ?? '';
        $borrowerRefId = trim($_POST['borrower_ref_id'] ?? '');
        $rfid = trim($_POST['rfid_uid'] ?? '');
        $book = (new Book_model())->findByRfid($rfid);

        if (!$borrowerType || !$borrowerRefId || !$book) {
            $_SESSION['flash_error'] = 'Enter the borrower details and a valid book RFID first.';
            redirect(BASE_URL . '/?url=borrow/index');
        }

        $reservation = (new Reservation_model())->findActive($book['id']);
        if (!$reservation) {
            $_SESSION['flash_error'] = 'No active reservation was found for this book.';
            redirect(BASE_URL . '/?url=borrow/index');
        }

        if ($reservation['borrower_type'] !== $borrowerType || (string) $reservation['borrower_ref_id'] !== (string) $borrowerRefId) {
            $_SESSION['flash_error'] = 'This book is reserved for a different borrower.';
            redirect(BASE_URL . '/?url=borrow/index');
        }

        if ($reservation['status'] !== 'approved') {
            $_SESSION['flash_error'] = 'Accept this reservation first, then mark it Ready to Pick Up.';
            redirect(BASE_URL . '/?url=borrow/index');
        }

        redirect(BASE_URL . '/?url=reservation/ready/' . (int) $reservation['id']);
    }

    public function return()
    {
        $this->ensureLibrarian();
        $this->view('librarian/borrow/return');
    }

    public function confirmReturn()
    {
        $this->ensureLibrarian();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect(BASE_URL . '/?url=borrow/return');
        $csrf = $_POST['_csrf'] ?? '';
        if (!verify_csrf_token($csrf)) { $_SESSION['flash_error'] = 'Invalid CSRF token.'; redirect(BASE_URL . '/?url=borrow/return'); }

        $rfid = trim((string) ($_POST['rfid_uid'] ?? ''));
        $borrowModel = new Borrow_model();
        $tx = $borrowModel->findActiveByRfid($rfid);
        if (!$tx) { $_SESSION['flash_error'] = 'No active borrow found for this RFID. Scan or type the exact book RFID.'; redirect(BASE_URL . '/?url=borrow/return'); }

        $borrowModel->markReturned($tx['id']);
        (new Book_model())->updateById($tx['book_id'], ['status' => 'available']);

        $reservation = (new Reservation_model())->findBorrowedByBook($tx['book_id']);
        if ($reservation) {
            (new Reservation_model())->markReturned($reservation['id']);
        }

        // send return confirmation email
        if (!empty($tx['borrower_email'])) {
            try {
                $mailer = new Mailer();
                $subject = APP_NAME . ' - Return Confirmation';
                $body = "<p>Dear " . e($tx['borrower_name']) . ",</p><p>Your returned book: <strong>" . e((new Book_model())->findById($tx['book_id'])['title']) . "</strong> has been processed.</p><p>Return Date: " . date('Y-m-d H:i:s') . "</p>";
                $mailer->send($tx['borrower_email'], $subject, $body);
            } catch (Exception $ex) {
                $db = Database::getInstance();
                $stmt = $db->prepare('INSERT INTO activity_logs (user_type, user_ref_id, action, detail) VALUES (:ut, :uid, :act, :det)');
                $stmt->execute([':ut'=>'system', ':uid'=>0, ':act'=>'mail_error', ':det'=>$ex->getMessage()]);
            }
        }

        (new Notification_model())->create([
            'user_type' => $tx['borrower_type'],
            'user_ref_id' => intval($tx['borrower_ref_id']),
            'title' => 'Return Processed',
            'message' => "Your return of " . ((new Book_model())->findById($tx['book_id'])['title']) . " has been processed.",
            'type' => 'return'
        ]);

        $_SESSION['flash'] = 'Book returned successfully.';
        redirect(BASE_URL . '/?url=borrow/return');
    }

    // Simple endpoint to lookup borrower by id/email
    public function lookupBorrower()
    {
        header('Content-Type: application/json');
        $type = $_GET['type'] ?? null;
        $id = $_GET['id'] ?? null;
        if (!$type || !$id) { echo json_encode(['ok'=>false,'error'=>'Missing parameters']); return; }
        if ($type === 'student') {
            $u = (new Student_model())->findByStudentIdOrEmail($id);
        } else {
            $u = (new Faculty_model())->findByFacultyIdOrEmail($id);
        }
        if ($u) echo json_encode(['ok'=>true,'user'=>$u]); else echo json_encode(['ok'=>false,'error'=>'Not found']);
    }
}
