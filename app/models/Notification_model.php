<?php
class Notification_model extends Model
{
    public function create($data)
    {
        $stmt = $this->db->prepare('INSERT INTO notifications (user_type, user_ref_id, title, message, is_read, type, created_at) VALUES (:user_type, :user_ref_id, :title, :message, :is_read, :type, NOW())');
        $stmt->execute([
            ':user_type' => $data['user_type'],
            ':user_ref_id' => $data['user_ref_id'],
            ':title' => $data['title'],
            ':message' => $data['message'],
            ':is_read' => $data['is_read'] ?? 0,
            ':type' => $data['type'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    public function getByUser($user_type, $user_ref_id)
    {
        $stmt = $this->db->prepare('SELECT * FROM notifications WHERE user_type = :user_type AND user_ref_id = :user_ref_id ORDER BY created_at DESC');
        $stmt->execute([':user_type' => $user_type, ':user_ref_id' => $user_ref_id]);
        return $stmt->fetchAll();
    }

    public function markAsRead($id)
    {
        $stmt = $this->db->prepare('UPDATE notifications SET is_read = 1 WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
