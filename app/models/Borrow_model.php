<?php
class Borrow_model extends Model
{
    protected $table = 'borrow_transactions';

    public function createTransaction($data)
    {
        $sql = 'INSERT INTO borrow_transactions (borrower_type, borrower_ref_id, borrower_name, borrower_email, borrower_phone, book_id, rfid_uid, borrow_date, due_date, status, remarks) VALUES (:borrower_type, :borrower_ref_id, :borrower_name, :borrower_email, :borrower_phone, :book_id, :rfid_uid, :borrow_date, :due_date, :status, :remarks)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':borrower_type' => $data['borrower_type'],
            ':borrower_ref_id' => $data['borrower_ref_id'],
            ':borrower_name' => $data['borrower_name'],
            ':borrower_email' => $data['borrower_email'] ?? null,
            ':borrower_phone' => $data['borrower_phone'] ?? null,
            ':book_id' => $data['book_id'],
            ':rfid_uid' => $data['rfid_uid'] ?? null,
            ':borrow_date' => $data['borrow_date'] ?? date('Y-m-d H:i:s'),
            ':due_date' => $data['due_date'],
            ':status' => 'borrowed',
            ':remarks' => $data['remarks'] ?? null,
        ]);
        return $this->db->lastInsertId();
    }

    public function findActiveByRfid($rfid)
    {
        $rfid = trim((string) $rfid);
        if ($rfid === '') {
            return null;
        }

        $stmt = $this->db->prepare('SELECT * FROM borrow_transactions WHERE rfid_uid = :rfid AND status IN ("borrowed", "overdue") LIMIT 1');
        $stmt->execute([':rfid' => $rfid]);
        return $stmt->fetch();
    }

    public function markReturned($id, $return_date = null)
    {
        $stmt = $this->db->prepare('UPDATE borrow_transactions SET status = "returned", return_date = :rd WHERE id = :id');
        return $stmt->execute([':rd' => $return_date ?? date('Y-m-d H:i:s'), ':id' => $id]);
    }

    public function getDueOrOverdueBorrowed()
    {
        $stmt = $this->db->prepare('SELECT * FROM borrow_transactions WHERE status = "borrowed" AND due_date <= NOW()');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getDueOrOverdueWithBooks()
    {
        $stmt = $this->db->prepare('SELECT bt.*, b.title AS book_title FROM borrow_transactions bt JOIN books b ON b.id = bt.book_id WHERE bt.status = "overdue" OR (bt.status = "borrowed" AND bt.due_date <= NOW()) ORDER BY bt.due_date ASC');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getDueBorrowedWithoutNotice()
    {
        $stmt = $this->db->prepare('SELECT * FROM borrow_transactions WHERE status IN ("borrowed", "overdue") AND due_date <= NOW() AND due_notice_sent_at IS NULL ORDER BY due_date ASC');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function markDueNoticeSent($id)
    {
        $stmt = $this->db->prepare('UPDATE borrow_transactions SET due_notice_sent_at = NOW() WHERE id = :id AND due_notice_sent_at IS NULL');
        return $stmt->execute([':id' => $id]);
    }

    public function markOverdue($id)
    {
        $stmt = $this->db->prepare('UPDATE borrow_transactions SET status = "overdue" WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public function getByBorrower($borrowerType, $borrowerRefId)
    {
        $stmt = $this->db->prepare('SELECT bt.*, b.title AS book_title FROM borrow_transactions bt LEFT JOIN books b ON bt.book_id = b.id WHERE bt.borrower_type = :type AND bt.borrower_ref_id = :ref ORDER BY bt.borrow_date DESC');
        $stmt->execute([':type' => $borrowerType, ':ref' => $borrowerRefId]);
        return $stmt->fetchAll();
    }
}
