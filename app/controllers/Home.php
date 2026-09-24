<?php
class Home extends Controller
{
    public function index()
    {
        if (empty($_SESSION['user_role'])) {
            $this->view('home/landing', ['title' => 'LibAI | Library AI Management']);
            return;
        }

        $db = Database::getInstance();
        $role = $_SESSION['user_role'];
        $userId = $_SESSION['user_id'] ?? null;
        $stats = [];
        $recommendations = [];

        if ($role === 'admin') {
            $stats['total_books'] = $db->query('SELECT COUNT(*) FROM books')->fetchColumn();
            $stats['available_books'] = $db->query("SELECT COUNT(*) FROM books WHERE status = 'available'")->fetchColumn();
            $stats['borrowed_books'] = $db->query("SELECT COUNT(*) FROM books WHERE status = 'borrowed'")->fetchColumn();
            $stats['overdue_books'] = $db->query("SELECT COUNT(*) FROM borrow_transactions WHERE status = 'overdue' OR (status = 'borrowed' AND due_date < NOW())")->fetchColumn();
            $stats['lost_books'] = $db->query("SELECT COUNT(*) FROM books WHERE status = 'lost'")->fetchColumn();
            $stats['damaged_books'] = $db->query("SELECT COUNT(*) FROM books WHERE status = 'damaged'")->fetchColumn();
            $stats['total_students'] = $db->query('SELECT COUNT(*) FROM students')->fetchColumn();
            $stats['total_faculty'] = $db->query('SELECT COUNT(*) FROM faculty')->fetchColumn();
            $stats['total_librarians'] = $db->query('SELECT COUNT(*) FROM librarians')->fetchColumn();
            $stats['total_rfid_tags'] = $db->query('SELECT COUNT(*) FROM books WHERE rfid_uid IS NOT NULL')->fetchColumn();
            $stats['unread_notifications'] = $db->query("SELECT COUNT(*) FROM notifications WHERE user_type='admin' AND is_read = 0")->fetchColumn();
            $stats['monthly_borrowing'] = $db->query("SELECT DATE_FORMAT(borrow_date, '%b %Y') AS period, COUNT(*) AS total FROM borrow_transactions GROUP BY period ORDER BY MIN(borrow_date) DESC LIMIT 6")->fetchAll();
        } elseif ($role === 'librarian') {
            $stats['borrowed_today'] = $db->query("SELECT COUNT(*) FROM borrow_transactions WHERE DATE(borrow_date) = CURDATE()")->fetchColumn();
            $stats['returned_today'] = $db->query("SELECT COUNT(*) FROM borrow_transactions WHERE DATE(return_date) = CURDATE() AND status = 'returned'")->fetchColumn();
            $stats['available_books'] = $db->query("SELECT COUNT(*) FROM books WHERE status = 'available'")->fetchColumn();
            $stats['overdue_books'] = $db->query("SELECT COUNT(*) FROM borrow_transactions WHERE status = 'overdue'")->fetchColumn();
            $stats['pending_reservations'] = $db->query("SELECT COUNT(*) FROM reservations WHERE status = 'pending'")->fetchColumn();
            $stats['new_books'] = $db->query("SELECT COUNT(*) FROM books WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn();
            $stats['inventory_total'] = $db->query('SELECT COUNT(*) FROM books')->fetchColumn();
            $stats['unread_notifications'] = $db->query("SELECT COUNT(*) FROM notifications WHERE user_type='librarian' AND is_read = 0")->fetchColumn();
            $stats['daily_activity'] = $db->query("SELECT DATE_FORMAT(borrow_date, '%b %d') AS period, COUNT(*) AS total FROM borrow_transactions WHERE borrow_date >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) GROUP BY period ORDER BY borrow_date ASC")->fetchAll();
        } elseif (in_array($role, ['student', 'faculty'], true)) {
            $borrowerType = $role;
            $borrowerRefId = null;

            if ($borrowerType === 'student') {
                $student = (new Student_model())->getById($userId);
                $borrowerRefId = $student['student_id'] ?? null;
            } else {
                $faculty = (new Faculty_model())->getById($userId);
                $borrowerRefId = $faculty['faculty_id'] ?? null;
            }

            $stmt = $db->prepare('SELECT COUNT(*) FROM borrow_transactions WHERE borrower_type = :type AND borrower_ref_id = :uid AND status IN ("borrowed","overdue")');
            $stmt->execute([':type' => $borrowerType, ':uid' => $borrowerRefId]);
            $stats['borrowed_books'] = $stmt->fetchColumn();

            $stmt = $db->prepare('SELECT COUNT(*) FROM borrow_transactions WHERE borrower_type = :type AND borrower_ref_id = :uid AND status = "borrowed" AND due_date <= DATE_ADD(NOW(), INTERVAL 3 DAY)');
            $stmt->execute([':type' => $borrowerType, ':uid' => $borrowerRefId]);
            $stats['due_soon'] = $stmt->fetchColumn();

            $stmt = $db->prepare('SELECT COUNT(*) FROM borrow_transactions WHERE borrower_type = :type AND borrower_ref_id = :uid AND (status = "overdue" OR (status = "borrowed" AND due_date < NOW()))');
            $stmt->execute([':type' => $borrowerType, ':uid' => $borrowerRefId]);
            $stats['overdue_books'] = $stmt->fetchColumn();

            $stmt = $db->prepare('SELECT COUNT(*) FROM borrow_transactions WHERE borrower_type = :type AND borrower_ref_id = :uid AND status = "returned"');
            $stmt->execute([':type' => $borrowerType, ':uid' => $borrowerRefId]);
            $stats['history_count'] = $stmt->fetchColumn();

            $stats['available_books'] = $db->query("SELECT COUNT(*) FROM books WHERE status = 'available'")->fetchColumn();
            $stats['new_arrivals'] = $db->query("SELECT COUNT(*) FROM books WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn();
            $stmt = $db->prepare('SELECT COUNT(*) FROM notifications WHERE user_type = :type AND user_ref_id = :uid AND is_read = 0');
            $stmt->execute([':type' => $borrowerType, ':uid' => $userId]);
            $stats['unread_notifications'] = $stmt->fetchColumn();
            $stmt = $db->prepare('SELECT COUNT(*) FROM reservations WHERE borrower_type = :type AND borrower_ref_id = :uid AND status IN ("pending","approved","ready")');
            $stmt->execute([':type' => $borrowerType, ':uid' => $borrowerRefId]);
            $stats['reserved_books'] = $stmt->fetchColumn();

            $recommendations = (new Recommendation_model())->recommendForUser($borrowerType, $userId, 6);

            if ($role === 'faculty') {
                $stats['research_materials'] = $db->query('SELECT COUNT(*) FROM books WHERE year_published >= DATE_SUB(NOW(), INTERVAL 5 YEAR)')->fetchColumn();
            }
        }

        $data = [
            'title' => 'Dashboard',
            'stats' => $stats,
            'recommendations' => $recommendations
        ];

        $this->view('home/index', $data);
    }

    public function contact()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . '/?url=home/landing');
        }

        $csrf = $_POST['_csrf'] ?? '';
        if (!verify_csrf_token($csrf)) {
            $_SESSION['flash_error'] = 'Invalid CSRF token.';
            redirect(BASE_URL . '/?url=home/landing');
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if ($name === '' || $email === '' || $message === '') {
            $_SESSION['flash_error'] = 'Please complete all contact fields.';
            redirect(BASE_URL . '/?url=home/landing');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash_error'] = 'Please enter a valid email address.';
            redirect(BASE_URL . '/?url=home/landing');
        }

        $admins = (new Admin_model())->getAll();
        if (empty($admins)) {
            $_SESSION['flash_error'] = 'No admin is available to receive your message right now.';
            redirect(BASE_URL . '/?url=home/landing');
        }

        $subject = APP_NAME . ' Contact Request';
        $body = '<p>You have a new contact request from the landing page.</p>' .
            '<p><strong>Name:</strong> ' . e($name) . '<br>' .
            '<strong>Email:</strong> ' . e($email) . '</p>' .
            '<p><strong>Message:</strong><br>' . nl2br(e($message)) . '</p>';

        foreach ($admins as $admin) {
            (new Notification_model())->create([
                'user_type' => 'admin',
                'user_ref_id' => intval($admin['id']),
                'title' => 'Landing page contact request',
                'message' => "From: {$name} ({$email})\n\n{$message}",
                'type' => 'contact'
            ]);

            if (!empty($admin['email'])) {
                try {
                    $mailer = new Mailer();
                    $mailer->send($admin['email'], $subject, $body);
                } catch (Exception $ex) {
                    $db = Database::getInstance();
                    $stmt = $db->prepare('INSERT INTO activity_logs (user_type, user_ref_id, action, detail) VALUES (:ut, :uid, :act, :det)');
                    $stmt->execute([
                        ':ut' => 'system',
                        ':uid' => 0,
                        ':act' => 'mail_error',
                        ':det' => 'Contact mail error: ' . $ex->getMessage()
                    ]);
                }
            }
        }

        $_SESSION['flash'] = 'Your message has been sent to admin. We will respond as soon as possible.';
        redirect(BASE_URL . '/?url=home/landing');
    }
}
