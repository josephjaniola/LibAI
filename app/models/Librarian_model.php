<?php
class Librarian_model extends Model
{
    protected $table = 'librarians';

    public function findByUsernameOrEmail($identifier)
    {
        $sql = 'SELECT * FROM librarians WHERE username = :identifier OR email = :identifier2 OR librarian_id = :identifier3 LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':identifier' => $identifier, ':identifier2' => $identifier, ':identifier3' => $identifier]);
        return $stmt->fetch();
    }

    public function findByEmailOrPhone($identifier)
    {
        $sql = 'SELECT * FROM librarians WHERE email = :identifier OR mobile = :identifier2 OR username = :identifier3 OR librarian_id = :identifier4 LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':identifier' => $identifier,
            ':identifier2' => $identifier,
            ':identifier3' => $identifier,
            ':identifier4' => $identifier,
        ]);
        return $stmt->fetch();
    }

    public function existsByIdOrEmail($librarian_id, $email)
    {
        $sql = 'SELECT COUNT(*) as cnt FROM librarians WHERE librarian_id = :lid OR email = :em';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':lid' => $librarian_id, ':em' => $email]);
        $row = $stmt->fetch();
        return $row && $row['cnt'] > 0;
    }

    public function create($data)
    {
        $sql = 'INSERT INTO librarians (librarian_id, username, email, password, firstname, middlename, lastname, mobile, profile_picture) VALUES (:librarian_id, :username, :email, :password, :firstname, :middlename, :lastname, :mobile, :profile_picture)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':librarian_id' => $data['librarian_id'],
            ':username' => $data['username'] ?? null,
            ':email' => $data['email'] ?? null,
            ':password' => $data['password'],
            ':firstname' => $data['firstname'] ?? null,
            ':middlename' => $data['middlename'] ?? null,
            ':lastname' => $data['lastname'] ?? null,
            ':mobile' => $data['mobile'] ?? null,
            ':profile_picture' => $data['profile_picture'] ?? null,
        ]);
        return $this->db->lastInsertId();
    }

    public function getAll()
    {
        $sql = 'SELECT * FROM librarians ORDER BY created_at DESC';
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $sql = 'SELECT * FROM librarians WHERE id = :id LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function updateById($id, $data)
    {
        $fields = [];
        $params = [':id' => $id];
        if (isset($data['username'])) { $fields[] = 'username = :username'; $params[':username'] = $data['username']; }
        if (isset($data['email'])) { $fields[] = 'email = :email'; $params[':email'] = $data['email']; }
        if (isset($data['password'])) { $fields[] = 'password = :password'; $params[':password'] = $data['password']; }
        if (isset($data['firstname'])) { $fields[] = 'firstname = :firstname'; $params[':firstname'] = $data['firstname']; }
        if (isset($data['middlename'])) { $fields[] = 'middlename = :middlename'; $params[':middlename'] = $data['middlename']; }
        if (isset($data['lastname'])) { $fields[] = 'lastname = :lastname'; $params[':lastname'] = $data['lastname']; }
        if (isset($data['mobile'])) { $fields[] = 'mobile = :mobile'; $params[':mobile'] = $data['mobile']; }
        if (isset($data['profile_picture'])) { $fields[] = 'profile_picture = :profile_picture'; $params[':profile_picture'] = $data['profile_picture']; }

        if (empty($fields)) return false;

        $sql = 'UPDATE librarians SET ' . implode(', ', $fields) . ' WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function deleteById($id)
    {
        $sql = 'DELETE FROM librarians WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
