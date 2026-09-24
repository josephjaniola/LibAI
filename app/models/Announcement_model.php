<?php
class Announcement_model extends Model
{
    public function getLatestActive()
    {
        $stmt = $this->db->prepare('SELECT * FROM announcements WHERE is_active = 1 AND (starts_at IS NULL OR starts_at <= NOW()) AND (ends_at IS NULL OR ends_at >= NOW()) ORDER BY created_at DESC LIMIT 1');
        $stmt->execute();
        return $stmt->fetch();
    }

    public function create($data)
    {
        $stmt = $this->db->prepare('INSERT INTO announcements (title, message, created_by, starts_at, ends_at, is_active, created_at) VALUES (:title, :message, :created_by, :starts_at, :ends_at, :is_active, NOW())');
        $stmt->execute([
            ':title' => $data['title'] ?? 'Admin Announcement',
            ':message' => $data['message'] ?? '',
            ':created_by' => $data['created_by'] ?? null,
            ':starts_at' => $data['starts_at'] ?? null,
            ':ends_at' => $data['ends_at'] ?? null,
            ':is_active' => $data['is_active'] ?? 1,
        ]);

        return $this->db->lastInsertId();
    }
}
