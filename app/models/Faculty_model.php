<?php
class Faculty_model extends Model
{
    protected $table = 'faculty';

    public function findByFacultyIdOrEmail($identifier)
    {
        $sql = 'SELECT * FROM faculty WHERE faculty_id = :identifier OR email = :identifier2 LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':identifier' => $identifier, ':identifier2' => $identifier]);
        return $stmt->fetch();
    }

    public function findByEmailOrPhone($identifier)
    {
        $sql = 'SELECT * FROM faculty WHERE email = :identifier OR mobile = :identifier2 OR faculty_id = :identifier3 LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':identifier' => $identifier,
            ':identifier2' => $identifier,
            ':identifier3' => $identifier,
        ]);
        return $stmt->fetch();
    }

    public function existsByFacultyIdOrEmail($faculty_id, $email)
    {
        $sql = 'SELECT COUNT(*) as cnt FROM faculty WHERE faculty_id = :fid OR email = :em';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':fid' => $faculty_id, ':em' => $email]);
        $row = $stmt->fetch();
        return $row && $row['cnt'] > 0;
    }

    public function create($data)
    {
        $sql = 'INSERT INTO faculty (faculty_id, firstname, middlename, lastname, department, position, email, mobile, password, profile_picture) VALUES (:faculty_id, :firstname, :middlename, :lastname, :department, :position, :email, :mobile, :password, :profile_picture)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':faculty_id' => $data['faculty_id'],
            ':firstname' => $data['firstname'],
            ':middlename' => $data['middlename'] ?? null,
            ':lastname' => $data['lastname'],
            ':department' => $data['department'] ?? null,
            ':position' => $data['position'] ?? null,
            ':email' => $data['email'] ?? null,
            ':mobile' => $data['mobile'] ?? null,
            ':password' => $data['password'],
            ':profile_picture' => $data['profile_picture'] ?? null,
        ]);
        return $this->db->lastInsertId();
    }

    public function getById($id)
    {
        $sql = 'SELECT * FROM faculty WHERE id = :id LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function updateById($id, $data)
    {
        $fields = [];
        $params = [':id' => $id];
        if (isset($data['faculty_id'])) { $fields[] = 'faculty_id = :faculty_id'; $params[':faculty_id'] = $data['faculty_id']; }
        if (isset($data['firstname'])) { $fields[] = 'firstname = :firstname'; $params[':firstname'] = $data['firstname']; }
        if (isset($data['middlename'])) { $fields[] = 'middlename = :middlename'; $params[':middlename'] = $data['middlename']; }
        if (isset($data['lastname'])) { $fields[] = 'lastname = :lastname'; $params[':lastname'] = $data['lastname']; }
        if (isset($data['department'])) { $fields[] = 'department = :department'; $params[':department'] = $data['department']; }
        if (isset($data['position'])) { $fields[] = 'position = :position'; $params[':position'] = $data['position']; }
        if (isset($data['email'])) { $fields[] = 'email = :email'; $params[':email'] = $data['email']; }
        if (isset($data['mobile'])) { $fields[] = 'mobile = :mobile'; $params[':mobile'] = $data['mobile']; }
        if (isset($data['password'])) { $fields[] = 'password = :password'; $params[':password'] = $data['password']; }
        if (isset($data['profile_picture'])) { $fields[] = 'profile_picture = :profile_picture'; $params[':profile_picture'] = $data['profile_picture']; }

        if (empty($fields)) return false;

        $sql = 'UPDATE faculty SET ' . implode(', ', $fields) . ' WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function getAll()
    {
        $stmt = $this->db->query('SELECT * FROM faculty ORDER BY created_at DESC');
        return $stmt->fetchAll();
    }
}
