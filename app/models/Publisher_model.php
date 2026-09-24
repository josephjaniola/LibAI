<?php
class Publisher_model extends Model
{
    public function getAll()
    {
        $stmt = $this->db->query('SELECT * FROM publishers ORDER BY name');
        return $stmt->fetchAll();
    }

    public function findOrCreate($name)
    {
        $stmt = $this->db->prepare('SELECT * FROM publishers WHERE name = :name LIMIT 1');
        $stmt->execute([':name' => $name]);
        $row = $stmt->fetch();
        if ($row) return $row['id'];
        $stmt = $this->db->prepare('INSERT INTO publishers (name) VALUES (:name)');
        $stmt->execute([':name' => $name]);
        return $this->db->lastInsertId();
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare('SELECT * FROM publishers WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function update($id, $name)
    {
        $stmt = $this->db->prepare('UPDATE publishers SET name = :name WHERE id = :id');
        return $stmt->execute([':name' => $name, ':id' => $id]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare('DELETE FROM publishers WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
