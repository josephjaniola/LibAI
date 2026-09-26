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
        $dueItems = $borrowModel->getDueOrOverdueBorrowed();
        $adminRecipients = [MAIL_FROM_ADDRESS];
        foreach ((new Admin_model())->getAll() as $admin) {
            if (!empty($admin['email'])) {
                $adminRecipients[] = $admin['email'];
            }
        }
        $adminRecipients = array_values(array_unique(array_filter(array_map('trim', $adminRecipients))));
        $updated = 0;

        foreach ($dueItems as $item) {
            $isOverdue = strtotime($item['due_date']) < time();
            if ($isOverdue) {
                $borrowModel->markOverdue($item['id']);
            }
            $book = (new Book_model())->findById($item['book_id']);
            $borrowerName = $item['borrower_name'];
            $borrowerEmail = $item['borrower_email'];
            $updated++;

            $recipients = $adminRecipients;
            if (!empty($borrowerEmail)) {
                $recipients[] = $borrowerEmail;
            }
            $recipients = array_values(array_unique(array_filter(array_map('trim', $recipients))));

            foreach ($recipients as $recipient) {
                try {
                    $mailer = new Mailer();
                    $subject = APP_NAME . ($isOverdue ? ' - Overdue Notice' : ' - Book Due Notice');
                    $body = $isOverdue
                        ? "<p>Overdue book alert for <strong>" . e($borrowerName) . "</strong>.</p><p>Book: <strong>" . e($book['title']) . "</strong><br>Due date: " . e($item['due_date']) . "</p><p>This book is overdue. Please return it as soon as possible to avoid fines.</p>"
                        : "<p>Book due reminder for <strong>" . e($borrowerName) . "</strong>.</p><p>You have a book due for return: <strong>" . e($book['title']) . "</strong><br>Due date: " . e($item['due_date']) . "</p><p>Please return the book on time.</p>";
                    $mailer->send($recipient, $subject, $body);
                } catch (Exception $ex) {
                    $db = Database::getInstance();
                    $stmt = $db->prepare('INSERT INTO activity_logs (user_type, user_ref_id, action, detail) VALUES (:ut, :uid, :act, :det)');
                    $stmt->execute([':ut'=>'system', ':uid'=>0, ':act'=>'mail_error', ':det'=>$ex->getMessage()]);
                }
            }

            (new Notification_model())->create([
                'user_type' => $item['borrower_type'],
                'user_ref_id' => intval($item['borrower_ref_id']),
                'title' => $isOverdue ? 'Overdue Notice' : 'Book Due Reminder',
                'message' => $isOverdue ? "Your borrowed book {$book['title']} is overdue since {$item['due_date']}." : "You have a book due for return: {$book['title']} on {$item['due_date']}.",
                'type' => $isOverdue ? 'overdue' : 'due'
            ]);
        }

        $_SESSION['flash'] = "$updated due or overdue loan(s) processed and notifications sent.";
        redirect(BASE_URL . '/?url=reports/index');
    }

    private function ensureLibrarianOrAdmin()
    {
        if (empty($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['admin','librarian'])) {
            redirect(BASE_URL . '/?url=auth/login');
        }
    }
}
