<?php
class Example_model extends Model
{
    public function getTime()
    {
        $stmt = $this->db->query('SELECT NOW() AS now');
        return $stmt->fetch();
    }
}
