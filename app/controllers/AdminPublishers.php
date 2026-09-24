<?php
class AdminPublishers extends Controller
{
    private function ensureAdmin()
    {
        if (empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') redirect(BASE_URL.'/?url=auth/login');
    }

    public function index()
    {
        $this->ensureAdmin();
        $pubs = (new Publisher_model())->getAll();
        $this->view('admin/publishers/index', ['publishers'=>$pubs]);
    }

    public function add()
    {
        $this->ensureAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf_token($_POST['_csrf'] ?? '')) return $this->view('admin/publishers/add',['error'=>'Invalid CSRF token']);
            $name = strip_tags(trim($_POST['name'] ?? ''));
            $name = mb_substr($name,0,255);
            if ($name==='') return $this->view('admin/publishers/add',['error'=>'Name required']);
            $pid = (new Publisher_model())->findOrCreate($name);
            (new ActivityLog_model())->log($_SESSION['user_id'] ?? null, 'publisher.create', json_encode(['id'=>$pid,'name'=>$name]));
            $_SESSION['flash'] = 'Publisher added';
            redirect(BASE_URL.'/?url=adminpublishers/index');
        }
        $this->view('admin/publishers/add');
    }

    public function edit($id = null)
    {
        $this->ensureAdmin();
        $id = $id ?? ($_GET['id'] ?? null);
        if (!$id) redirect(BASE_URL.'/?url=adminpublishers/index');
        $model = new Publisher_model();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf_token($_POST['_csrf'] ?? '')) return $this->view('admin/publishers/edit',['error'=>'Invalid CSRF token','publisher'=>$model->getById($id)]);
            $name = strip_tags(trim($_POST['name'] ?? ''));
            $name = mb_substr($name,0,255);
            if ($name==='') return $this->view('admin/publishers/edit',['error'=>'Name required','publisher'=>$model->getById($id)]);
            $model->update($id,$name);
            (new ActivityLog_model())->log($_SESSION['user_id'] ?? null, 'publisher.update', json_encode(['id'=>$id,'name'=>$name]));
            $_SESSION['flash'] = 'Publisher updated';
            redirect(BASE_URL.'/?url=adminpublishers/index');
        }
        $p = $model->getById($id);
        $this->view('admin/publishers/edit',['publisher'=>$p]);
    }

    public function delete()
    {
        $this->ensureAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect(BASE_URL.'/?url=adminpublishers/index');
        if (!verify_csrf_token($_POST['_csrf'] ?? '')) { $_SESSION['flash_error']='Invalid CSRF token.'; redirect(BASE_URL.'/?url=adminpublishers/index'); }
        $id = $_POST['id'] ?? null;
        if ($id) {
            (new Publisher_model())->delete($id);
            (new ActivityLog_model())->log($_SESSION['user_id'] ?? null, 'publisher.delete', json_encode(['id'=>$id]));
            $_SESSION['flash'] = 'Publisher deleted';
        }
        redirect(BASE_URL.'/?url=adminpublishers/index');
    }
}
