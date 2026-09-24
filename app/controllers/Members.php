<?php
class Members extends Controller
{
    private function ensureStaff()
    {
        if (empty($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['librarian', 'admin'], true)) {
            redirect(BASE_URL . '/?url=home');
        }
    }

    public function index()
    {
        $this->ensureStaff();
        $students = (new Student_model())->getAll();
        $faculty = (new Faculty_model())->getAll();

        $this->view('members/index', [
            'students' => $students,
            'faculty' => $faculty,
            'role' => $_SESSION['user_role']
        ]);
    }
}
