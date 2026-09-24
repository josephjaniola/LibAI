<?php
class AdminActivity extends Controller
{
    private function ensureAdmin()
    {
        if (empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') redirect(BASE_URL.'/?url=auth/login');
    }

    public function index()
    {
        $this->ensureAdmin();
        $logs = (new ActivityLog_model())->getRecent(200);
        $this->view('admin/activity/index', ['logs'=>$logs]);
    }
}
