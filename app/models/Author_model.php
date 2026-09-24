<?php
class Author_model extends Model
{
    public function getAll()
    {
        $stmt = $this->db->query('SELECT * FROM authors ORDER BY name');
        return $stmt->fetchAll();
    }

    public function findOrCreate($name)
    {
        $stmt = $this->db->prepare('SELECT * FROM authors WHERE name = :name LIMIT 1');
        $stmt->execute([':name' => $name]);
        $row = $stmt->fetch();
        if ($row) return $row['id'];
        $stmt = $this->db->prepare('INSERT INTO authors (name) VALUES (:name)');
        $stmt->execute([':name' => $name]);
        return $this->db->lastInsertId();
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare('SELECT * FROM authors WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function update($id, $name)
    {
        $stmt = $this->db->prepare('UPDATE authors SET name = :name WHERE id = :id');
        return $stmt->execute([':name' => $name, ':id' => $id]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare('DELETE FROM authors WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
