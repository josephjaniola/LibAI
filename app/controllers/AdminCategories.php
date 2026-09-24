<?php
class AdminCategories extends Controller
{
    private function ensureAdmin()
    {
        if (empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') redirect(BASE_URL.'/?url=auth/login');
    }

    public function index()
    {
        $this->ensureAdmin();
        $cats = (new Category_model())->getAll();
        $this->view('admin/categories/index', ['categories'=>$cats]);
    }

    public function add()
    {
        $this->ensureAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf_token($_POST['_csrf'] ?? '')) return $this->view('admin/categories/add',['error'=>'Invalid CSRF token']);
            $name = strip_tags(trim($_POST['name'] ?? ''));
            $name = mb_substr($name,0,255);
            $desc = isset($_POST['description']) ? strip_tags($_POST['description']) : null;
            if ($name==='') return $this->view('admin/categories/add',['error'=>'Name required']);
            $catId = (new Category_model())->create($name,$desc);
            (new ActivityLog_model())->log($_SESSION['user_id'] ?? null, 'category.create', json_encode(['id'=>$catId,'name'=>$name]));
            $_SESSION['flash'] = 'Category added';
            redirect(BASE_URL.'/?url=admincategories/index');
        }
        $this->view('admin/categories/add');
    }

    public function edit($id = null)
    {
        $this->ensureAdmin();
        $id = $id ?? ($_GET['id'] ?? null);
        if (!$id) redirect(BASE_URL.'/?url=admincategories/index');
        $model = new Category_model();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf_token($_POST['_csrf'] ?? '')) return $this->view('admin/categories/edit',['error'=>'Invalid CSRF token','category'=>$model->getById($id)]);
            $name = strip_tags(trim($_POST['name'] ?? ''));
            $name = mb_substr($name,0,255);
            $desc = isset($_POST['description']) ? strip_tags($_POST['description']) : null;
            if ($name==='') return $this->view('admin/categories/edit',['error'=>'Name required','category'=>$model->getById($id)]);
            $model->update($id,$name,$desc);
            (new ActivityLog_model())->log($_SESSION['user_id'] ?? null, 'category.update', json_encode(['id'=>$id,'name'=>$name]));
            $_SESSION['flash'] = 'Category updated';
            redirect(BASE_URL.'/?url=admincategories/index');
        }
        $cat = $model->getById($id);
        $this->view('admin/categories/edit',['category'=>$cat]);
    }

    public function delete()
    {
        $this->ensureAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect(BASE_URL.'/?url=admincategories/index');
        if (!verify_csrf_token($_POST['_csrf'] ?? '')) { $_SESSION['flash_error']='Invalid CSRF token.'; redirect(BASE_URL.'/?url=admincategories/index'); }
        $id = $_POST['id'] ?? null;
        if ($id) {
            (new Category_model())->delete($id);
            (new ActivityLog_model())->log($_SESSION['user_id'] ?? null, 'category.delete', json_encode(['id'=>$id]));
            $_SESSION['flash'] = 'Category deleted';
        }
        redirect(BASE_URL.'/?url=admincategories/index');
    }
}
