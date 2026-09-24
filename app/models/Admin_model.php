<?php
class Admin_model extends Model
{
    protected $table = 'admins';

    public function findByUsernameOrEmail($identifier)
    {
        $sql = 'SELECT * FROM admins WHERE username = :identifier OR email = :identifier2 LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':identifier' => $identifier, ':identifier2' => $identifier]);
        return $stmt->fetch();
    }

    public function findByEmailOrPhone($identifier)
    {
        $sql = 'SELECT * FROM admins WHERE email = :identifier OR username = :identifier2 LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':identifier' => $identifier, ':identifier2' => $identifier]);
        return $stmt->fetch();
    }

    public function getById($id)
    {
        $sql = 'SELECT * FROM admins WHERE id = :id LIMIT 1';
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

        $sql = 'UPDATE admins SET ' . implode(', ', $fields) . ' WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function getAll()
    {
        $stmt = $this->db->query('SELECT * FROM admins ORDER BY id ASC');
        return $stmt->fetchAll();
    }
}
