<?php
class Book_model extends Model
{
    protected $table = 'books';

    private function normalizeRfidUid($value)
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }

    private function normalizeBookData(array $data): array
    {
        if (array_key_exists('rfid_uid', $data)) {
            $data['rfid_uid'] = $this->normalizeRfidUid($data['rfid_uid']);
        }

        if (array_key_exists('status', $data)) {
            $status = trim((string) $data['status']);
            $data['status'] = $status === '' ? 'available' : $status;
        }

        return $data;
    }

    public function findByRfid($rfid)
    {
        $rfid = $this->normalizeRfidUid($rfid);
        if ($rfid === null) {
            return null;
        }

        $sql = 'SELECT * FROM books WHERE rfid_uid = :rfid LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':rfid' => $rfid]);
        return $stmt->fetch();
    }

    public function findById($id)
    {
        $sql = 'SELECT * FROM books WHERE id = :id LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function getAvailable()
    {
        $sql = 'SELECT b.*, c.name AS category_name, p.name AS publisher_name FROM books b LEFT JOIN categories c ON b.category_id=c.id LEFT JOIN publishers p ON b.publisher_id=p.id WHERE b.status = "available" ORDER BY b.created_at DESC';
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getAll()
    {
        $sql = 'SELECT b.*, c.name AS category_name, p.name AS publisher_name FROM books b LEFT JOIN categories c ON b.category_id=c.id LEFT JOIN publishers p ON b.publisher_id=p.id ORDER BY b.created_at DESC';
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function create($data)
    {
        $data = $this->normalizeBookData($data);
        $sql = 'INSERT INTO books (title, subtitle, isbn, accession_number, call_number, edition, volume, pages, category_id, language, shelf_location, year_published, description, keywords, remarks, cover_image, publisher_id, date_received, rfid_uid, status) VALUES (:title, :subtitle, :isbn, :accession_number, :call_number, :edition, :volume, :pages, :category_id, :language, :shelf_location, :year_published, :description, :keywords, :remarks, :cover_image, :publisher_id, :date_received, :rfid_uid, :status)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':title'=>$data['title'], ':subtitle'=>$data['subtitle'] ?? null, ':isbn'=>$data['isbn'] ?? null,
            ':accession_number'=>$data['accession_number'] ?? null, ':call_number'=>$data['call_number'] ?? null, ':edition'=>$data['edition'] ?? null,
            ':volume'=>$data['volume'] ?? null, ':pages'=>$data['pages'] ?? null, ':category_id'=>$data['category_id'] ?? null,
            ':language'=>$data['language'] ?? null, ':shelf_location'=>$data['shelf_location'] ?? null, ':year_published'=>$data['year_published'] ?? null,
            ':description'=>$data['description'] ?? null, ':keywords'=>$data['keywords'] ?? null, ':remarks'=>$data['remarks'] ?? null,
            ':cover_image'=>$data['cover_image'] ?? null, ':publisher_id'=>$data['publisher_id'] ?? null, ':date_received'=>$data['date_received'] ?? null,
            ':rfid_uid'=>$data['rfid_uid'] ?? null, ':status'=>$data['status'] ?? 'available'
        ]);
        return $this->db->lastInsertId();
    }

    public function updateById($id, $data)
    {
        $data = $this->normalizeBookData($data);
        $fields = [];
        $params = [':id' => $id];
        foreach ($data as $k => $v) {
            $fields[] = "$k = :$k";
            $params[":".$k] = $v;
        }
        if (empty($fields)) return false;
        $sql = 'UPDATE books SET ' . implode(', ', $fields) . ' WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function deleteById($id)
    {
        $stmt = $this->db->prepare('DELETE FROM books WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
