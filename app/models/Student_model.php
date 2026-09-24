<?php
class Student_model extends Model
{
    protected $table = 'students';

    public function findByStudentIdOrEmail($identifier)
    {
        $sql = 'SELECT * FROM students WHERE student_id = :identifier OR email = :identifier2 LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':identifier' => $identifier, ':identifier2' => $identifier]);
        return $stmt->fetch();
    }

    public function findByEmailOrPhone($identifier)
    {
        $sql = 'SELECT * FROM students WHERE email = :identifier OR mobile = :identifier2 OR student_id = :identifier3 LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':identifier' => $identifier,
            ':identifier2' => $identifier,
            ':identifier3' => $identifier,
        ]);
        return $stmt->fetch();
    }

    public function existsByStudentIdOrEmail($student_id, $email)
    {
        $sql = 'SELECT COUNT(*) as cnt FROM students WHERE student_id = :sid OR email = :em';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':sid' => $student_id, ':em' => $email]);
        $row = $stmt->fetch();
        return $row && $row['cnt'] > 0;
    }

    public function create($data)
    {
        $sql = 'INSERT INTO students (student_id, firstname, middlename, lastname, course, year_level, email, mobile, password, profile_picture) VALUES (:student_id, :firstname, :middlename, :lastname, :course, :year_level, :email, :mobile, :password, :profile_picture)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':student_id' => $data['student_id'],
            ':firstname' => $data['firstname'],
            ':middlename' => $data['middlename'] ?? null,
            ':lastname' => $data['lastname'],
            ':course' => $data['course'] ?? null,
            ':year_level' => $data['year_level'] ?? null,
            ':email' => $data['email'] ?? null,
            ':mobile' => $data['mobile'] ?? null,
            ':password' => $data['password'],
            ':profile_picture' => $data['profile_picture'] ?? null,
        ]);
        return $this->db->lastInsertId();
    }

    public function getById($id)
    {
        $sql = 'SELECT * FROM students WHERE id = :id LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function updateById($id, $data)
    {
        $fields = [];
        $params = [':id' => $id];
        if (isset($data['student_id'])) { $fields[] = 'student_id = :student_id'; $params[':student_id'] = $data['student_id']; }
        if (isset($data['firstname'])) { $fields[] = 'firstname = :firstname'; $params[':firstname'] = $data['firstname']; }
        if (isset($data['middlename'])) { $fields[] = 'middlename = :middlename'; $params[':middlename'] = $data['middlename']; }
        if (isset($data['lastname'])) { $fields[] = 'lastname = :lastname'; $params[':lastname'] = $data['lastname']; }
        if (isset($data['course'])) { $fields[] = 'course = :course'; $params[':course'] = $data['course']; }
        if (isset($data['year_level'])) { $fields[] = 'year_level = :year_level'; $params[':year_level'] = $data['year_level']; }
        if (isset($data['email'])) { $fields[] = 'email = :email'; $params[':email'] = $data['email']; }
        if (isset($data['mobile'])) { $fields[] = 'mobile = :mobile'; $params[':mobile'] = $data['mobile']; }
        if (isset($data['password'])) { $fields[] = 'password = :password'; $params[':password'] = $data['password']; }
        if (isset($data['profile_picture'])) { $fields[] = 'profile_picture = :profile_picture'; $params[':profile_picture'] = $data['profile_picture']; }

        if (empty($fields)) return false;

        $sql = 'UPDATE students SET ' . implode(', ', $fields) . ' WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function getAll()
    {
        $stmt = $this->db->query('SELECT * FROM students ORDER BY created_at DESC');
        return $stmt->fetchAll();
    }
}
