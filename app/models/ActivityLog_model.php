<?php
class ActivityLog_model extends Model
{
    /**
     * Log an activity. $user may be numeric (user id) or null.
     * Uses session role for user_type when available.
     */
    public function log($user = null, $action, $detail = null)
    {
        $user_type = $_SESSION['user_role'] ?? null;
        $user_ref_id = is_numeric($user) ? (int)$user : null;
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $stmt = $this->db->prepare('INSERT INTO activity_logs (user_type, user_ref_id, action, detail, ip_address, created_at) VALUES (:ut, :uid, :act, :det, :ip, NOW())');
        $stmt->execute([':ut' => $user_type, ':uid' => $user_ref_id, ':act' => $action, ':det' => $detail, ':ip' => $ip]);
        return $this->db->lastInsertId();
    }

    public function getRecent($limit = 200)
    {
        $stmt = $this->db->prepare('SELECT * FROM activity_logs ORDER BY created_at DESC LIMIT :lim');
        $stmt->bindValue(':lim', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
