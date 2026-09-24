<?php
class AdminAuthors extends Controller
{
    private function ensureAdmin()
    {
        if (empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') redirect(BASE_URL.'/?url=auth/login');
    }

    public function index()
    {
        $this->ensureAdmin();
        $authors = (new Author_model())->getAll();
        $this->view('admin/authors/index', ['authors'=>$authors]);
    }

    public function add()
    {
        $this->ensureAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf_token($_POST['_csrf'] ?? '')) return $this->view('admin/authors/add',['error'=>'Invalid CSRF token']);
            $name = strip_tags(trim($_POST['name'] ?? ''));
            $name = mb_substr($name,0,255);
            if ($name==='') return $this->view('admin/authors/add',['error'=>'Name required']);
            $aid = (new Author_model())->findOrCreate($name);
            (new ActivityLog_model())->log($_SESSION['user_id'] ?? null, 'author.create', json_encode(['id'=>$aid,'name'=>$name]));
            $_SESSION['flash'] = 'Author added';
            redirect(BASE_URL.'/?url=adminauthors/index');
        }
        $this->view('admin/authors/add');
    }

    public function edit($id = null)
    {
        $this->ensureAdmin();
        $id = $id ?? ($_GET['id'] ?? null);
        if (!$id) redirect(BASE_URL.'/?url=adminauthors/index');
        $model = new Author_model();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf_token($_POST['_csrf'] ?? '')) return $this->view('admin/authors/edit',['error'=>'Invalid CSRF token','author'=>$model->getById($id)]);
            $name = strip_tags(trim($_POST['name'] ?? ''));
            $name = mb_substr($name,0,255);
            if ($name==='') return $this->view('admin/authors/edit',['error'=>'Name required','author'=>$model->getById($id)]);
            $model->update($id,$name);
            (new ActivityLog_model())->log($_SESSION['user_id'] ?? null, 'author.update', json_encode(['id'=>$id,'name'=>$name]));
            $_SESSION['flash'] = 'Author updated';
            redirect(BASE_URL.'/?url=adminauthors/index');
        }
        $a = $model->getById($id);
        $this->view('admin/authors/edit',['author'=>$a]);
    }

    public function delete()
    {
        $this->ensureAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect(BASE_URL.'/?url=adminauthors/index');
        if (!verify_csrf_token($_POST['_csrf'] ?? '')) { $_SESSION['flash_error']='Invalid CSRF token.'; redirect(BASE_URL.'/?url=adminauthors/index'); }
        $id = $_POST['id'] ?? null;
        if ($id) {
            (new Author_model())->delete($id);
            (new ActivityLog_model())->log($_SESSION['user_id'] ?? null, 'author.delete', json_encode(['id'=>$id]));
            $_SESSION['flash'] = 'Author deleted';
        }
        redirect(BASE_URL.'/?url=adminauthors/index');
    }
}
