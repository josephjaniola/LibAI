<?php
class Overdue extends Controller
{
    public function process()
    {
        $this->ensureLibrarianOrAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . '/?url=reports/index');
        }

        $csrf = $_POST['_csrf'] ?? '';
        if (!verify_csrf_token($csrf)) {
            $_SESSION['flash_error'] = 'Invalid CSRF token.';
            redirect(BASE_URL . '/?url=reports/index');
        }

        $borrowModel = new Borrow_model();
        $overdueItems = $borrowModel->getOverdueBorrowed();
        $updated = 0;

        foreach ($overdueItems as $item) {
            $borrowModel->markOverdue($item['id']);
            $book = (new Book_model())->findById($item['book_id']);
            $borrowerName = $item['borrower_name'];
            $borrowerEmail = $item['borrower_email'];
            $updated++;

            if (!empty($borrowerEmail)) {
                try {
                    $mailer = new Mailer();
                    $subject = APP_NAME . ' - Overdue Notice';
                    $body = "<p>Dear " . e($borrowerName) . ",</p><p>Your borrowed book <strong>" . e($book['title']) . "</strong> is overdue since " . e($item['due_date']) . "</p><p>Please return it as soon as possible to avoid fines.</p>";
                    $mailer->send($borrowerEmail, $subject, $body);
                } catch (Exception $ex) {
                    $db = Database::getInstance();
                    $stmt = $db->prepare('INSERT INTO activity_logs (user_type, user_ref_id, action, detail) VALUES (:ut, :uid, :act, :det)');
                    $stmt->execute([':ut'=>'system', ':uid'=>0, ':act'=>'mail_error', ':det'=>$ex->getMessage()]);
                }
            }

            (new Notification_model())->create([
                'user_type' => $item['borrower_type'],
                'user_ref_id' => intval($item['borrower_ref_id']),
                'title' => 'Overdue Notice',
                'message' => "Your borrowed book {$book['title']} is overdue since {$item['due_date']}.",
                'type' => 'overdue'
            ]);
        }

        $_SESSION['flash'] = "$updated overdue loan(s) marked and notifications sent.";
        redirect(BASE_URL . '/?url=reports/index');
    }

    private function ensureLibrarianOrAdmin()
    {
        if (empty($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['admin','librarian'])) {
            redirect(BASE_URL . '/?url=auth/login');
        }
    }
}
