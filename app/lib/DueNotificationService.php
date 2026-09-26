<?php
class DueNotificationService
{
    public function sendDueNotifications()
    {
        $borrowModel = new Borrow_model();
        $dueItems = $borrowModel->getDueBorrowedWithoutNotice();

        foreach ($dueItems as $item) {
            $isOverdue = $item['status'] === 'overdue' || strtotime($item['due_date']) < time();
            if ($isOverdue && $item['status'] === 'borrowed') {
                $borrowModel->markOverdue($item['id']);
            }

            $borrower = $item['borrower_type'] === 'student'
                ? (new Student_model())->findByStudentIdOrEmail($item['borrower_ref_id'])
                : (new Faculty_model())->findByFacultyIdOrEmail($item['borrower_ref_id']);
            $recipient = trim((string) ($item['borrower_email'] ?? ($borrower['email'] ?? '')));

            $book = (new Book_model())->findById($item['book_id']);
            $bookTitle = $book['title'] ?? 'your borrowed book';
            $borrowerName = $item['borrower_name'] ?: 'Library user';
            $message = $isOverdue
                ? "Your book {$bookTitle} is overdue. It was due {$item['due_date']}. Please return it as soon as possible."
                : "Your book {$bookTitle} is due {$item['due_date']}. Please return it on time.";
            $sentExternally = false;

            if ($recipient !== '' && filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
                try {
                    $mailer = new Mailer();
                    $sent = $mailer->send(
                        $recipient,
                        APP_NAME . ($isOverdue ? ' - Overdue Notice' : ' - Book Due Reminder'),
                        $isOverdue
                            ? '<p>Dear ' . e($borrowerName) . ',</p><p>Your book <strong>' . e($bookTitle) . '</strong> is overdue.</p><p>Due date and time: <strong>' . e($item['due_date']) . '</strong></p><p>Please return the book as soon as possible.</p>'
                            : '<p>Dear ' . e($borrowerName) . ',</p><p>You have a book due for return: <strong>' . e($bookTitle) . '</strong>.</p><p>Due date and time: <strong>' . e($item['due_date']) . '</strong></p><p>Please return the book on time.</p>'
                    );

                    if (!$sent) {
                        throw new RuntimeException('Mail transport returned false.');
                    }
                    $sentExternally = true;
                } catch (Throwable $exception) {
                    error_log('LibAI automatic due email failed: ' . $exception->getMessage());
                }
            }

            $notificationCreated = false;
            if (!empty($borrower['id'])) {
                (new Notification_model())->create([
                    'user_type' => $item['borrower_type'],
                    'user_ref_id' => (int) $borrower['id'],
                    'title' => $isOverdue ? 'Overdue Notice' : 'Book Due Reminder',
                    'message' => $message,
                    'type' => $isOverdue ? 'overdue' : 'due'
                ]);
                $notificationCreated = true;
            }

            if ($sentExternally || $notificationCreated) {
                $borrowModel->markDueNoticeSent($item['id']);
            }
        }
    }
}
