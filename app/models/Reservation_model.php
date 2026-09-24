<?php
class Reservation_model extends Model
{
    protected $table = 'reservations';

    public function create($data)
    {
        $sql = 'INSERT INTO reservations (book_id, rfid_uid, borrower_type, borrower_ref_id, reserved_at, status, expires_at) VALUES (:book_id, :rfid_uid, :borrower_type, :borrower_ref_id, :reserved_at, :status, :expires_at)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':book_id' => $data['book_id'],
            ':rfid_uid' => $data['rfid_uid'] ?? null,
            ':borrower_type' => $data['borrower_type'],
            ':borrower_ref_id' => $data['borrower_ref_id'],
            ':reserved_at' => $data['reserved_at'] ?? date('Y-m-d H:i:s'),
            ':status' => $data['status'] ?? 'pending',
            ':expires_at' => $data['expires_at'] ?? date('Y-m-d H:i:s', strtotime('+3 days')),
        ]);
        return $this->db->lastInsertId();
    }

    public function findActive($bookId)
    {
        $stmt = $this->db->prepare('SELECT * FROM reservations WHERE book_id = :book_id AND status IN ("pending","approved","ready") ORDER BY reserved_at ASC LIMIT 1');
        $stmt->execute([':book_id' => $bookId]);
        return $stmt->fetch();
    }

    public function markReady($id)
    {
        $stmt = $this->db->prepare('UPDATE reservations SET status = "ready" WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public function cancel($id)
    {
        $stmt = $this->db->prepare('UPDATE reservations SET status = "cancelled" WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public function getAll($status = null)
    {
        $query = 'SELECT r.*, b.title FROM reservations r JOIN books b ON r.book_id=b.id';
        $params = [];
        if ($status) {
            $query .= ' WHERE r.status = :status';
            $params[':status'] = $status;
        }
        $query .= ' ORDER BY r.reserved_at DESC';
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare('SELECT r.*, b.title FROM reservations r JOIN books b ON r.book_id=b.id WHERE r.id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function getPendingByBorrower($borrowerType, $borrowerRefId)
    {
        $stmt = $this->db->prepare('SELECT r.*, b.title FROM reservations r JOIN books b ON r.book_id=b.id WHERE r.borrower_type = :type AND r.borrower_ref_id = :ref AND r.status IN ("pending","approved","ready") ORDER BY r.reserved_at DESC');
        $stmt->execute([':type'=>$borrowerType, ':ref'=>$borrowerRefId]);
        return $stmt->fetchAll();
    }

    public function getByBorrower($borrowerType, $borrowerRefId)
    {
        $stmt = $this->db->prepare('SELECT r.*, b.title FROM reservations r JOIN books b ON r.book_id=b.id WHERE r.borrower_type = :type AND r.borrower_ref_id = :ref ORDER BY r.reserved_at DESC');
        $stmt->execute([':type'=>$borrowerType, ':ref'=>$borrowerRefId]);
        return $stmt->fetchAll();
    }
}
