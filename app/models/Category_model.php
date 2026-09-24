<?php
class Category_model extends Model
{
    public function getAll()
    {
        $stmt = $this->db->query('SELECT * FROM categories ORDER BY name');
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare('SELECT * FROM categories WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function create($name, $description = null)
    {
        $stmt = $this->db->prepare('INSERT INTO categories (name, description) VALUES (:name, :desc)');
        $stmt->execute([':name' => $name, ':desc' => $description]);
        return $this->db->lastInsertId();
    }

    public function update($id, $name, $description = null)
    {
        $stmt = $this->db->prepare('UPDATE categories SET name = :name, description = :desc WHERE id = :id');
        return $stmt->execute([':name' => $name, ':desc' => $description, ':id' => $id]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare('DELETE FROM categories WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
