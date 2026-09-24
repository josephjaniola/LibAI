<?php
class Book extends Controller
{
    private function ensureAuthenticated()
    {
        if (empty($_SESSION['user_role'])) {
            redirect(BASE_URL . '/?url=auth/login');
        }
    }

    private function ensureLibrarianOrAdmin()
    {
        if (empty($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['admin','librarian'])) {
            redirect(BASE_URL . '/?url=auth/login');
        }
    }

    public function index()
    {
        $this->ensureAuthenticated();
        $model = new Book_model();
        $books = $model->getAll();
        $this->view('admin/books/index', ['books' => $books]);
    }

    public function add()
    {
        $this->ensureLibrarianOrAdmin();
        $categories = seedCourseCategories();
        $publishers = (new Publisher_model())->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrf = $_POST['_csrf'] ?? '';
            if (!verify_csrf_token($csrf)) return $this->view('admin/books/add', ['error' => 'Invalid CSRF token.', 'categories'=>$categories,'publishers'=>$publishers]);

            $uploadPath = null;
            if (!empty($_FILES['cover_image']['name'])) {
                $up = (new Register())->handleUpload($_FILES['cover_image']);
                if (!$up['ok']) return $this->view('admin/books/add', ['error'=>$up['error'],'categories'=>$categories,'publishers'=>$publishers]);
                $uploadPath = $up['path'];
            }

            $bookModel = new Book_model();
            $bookModel->create([
                'title'=>$_POST['title'], 'subtitle'=>$_POST['subtitle'] ?? null, 'isbn'=>$_POST['isbn'] ?? null,
                'accession_number'=>$_POST['accession_number'] ?? null, 'call_number'=>$_POST['call_number'] ?? null, 'edition'=>$_POST['edition'] ?? null,
                'volume'=>$_POST['volume'] ?? null, 'pages'=>$_POST['pages'] ?? null, 'category_id'=>$_POST['category_id'] ?? null,
                'language'=>$_POST['language'] ?? null, 'shelf_location'=>$_POST['shelf_location'] ?? null, 'year_published'=>$_POST['year_published'] ?? null,
                'description'=>$_POST['description'] ?? null, 'keywords'=>$_POST['keywords'] ?? null, 'remarks'=>$_POST['remarks'] ?? null,
                'cover_image'=>$uploadPath, 'publisher_id'=>$_POST['publisher_id'] ?? null, 'date_received'=>$_POST['date_received'] ?? null,
                'rfid_uid'=>$_POST['rfid_uid'] ?? null, 'status'=>$_POST['status'] ?? 'available'
            ]);

            $_SESSION['flash'] = 'Book added.';
            redirect(BASE_URL . '/?url=book/index');
        }

        $this->view('admin/books/add', ['categories'=>$categories,'publishers'=>$publishers]);
    }

    public function edit($id = null)
    {
        $this->ensureLibrarianOrAdmin();
        if (!$id) return redirect(BASE_URL . '/?url=book/index');
        $bookModel = new Book_model();
        $book = $bookModel->findById($id);
        $categories = seedCourseCategories();
        $publishers = (new Publisher_model())->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrf = $_POST['_csrf'] ?? '';
            if (!verify_csrf_token($csrf)) return $this->view('admin/books/edit', ['error'=>'Invalid CSRF token.','book'=>$book,'categories'=>$categories,'publishers'=>$publishers]);

            $data = [];
            foreach (['title','subtitle','isbn','accession_number','call_number','edition','volume','pages','category_id','language','shelf_location','year_published','description','keywords','remarks','publisher_id','date_received','rfid_uid','status'] as $f) {
                if (isset($_POST[$f])) $data[$f] = $_POST[$f] === '' ? null : $_POST[$f];
            }

            if (!empty($_FILES['cover_image']['name'])) {
                $up = (new Register())->handleUpload($_FILES['cover_image']);
                if (!$up['ok']) return $this->view('admin/books/edit', ['error'=>$up['error'],'book'=>$book,'categories'=>$categories,'publishers'=>$publishers]);
                $data['cover_image'] = $up['path'];
            }

            $bookModel->updateById($id, $data);
            $_SESSION['flash'] = 'Book updated.';
            redirect(BASE_URL . '/?url=book/index');
        }

        $this->view('admin/books/edit', ['book'=>$book,'categories'=>$categories,'publishers'=>$publishers]);
    }

    public function delete($id = null)
    {
        $this->ensureLibrarianOrAdmin();
        if (!$id) return redirect(BASE_URL . '/?url=book/index');

        $bookModel = new Book_model();
        try {
            $deleted = $bookModel->deleteById($id);
        } catch (PDOException $ex) {
            $deleted = false;
            $error = $ex->getMessage();
        }

        if ($deleted) {
            $_SESSION['flash'] = 'Book deleted.';
        } else {
            // If the book is referenced by borrow history, archive it instead.
            $bookModel->updateById($id, ['status' => 'archived']);
            $_SESSION['flash'] = 'Book could not be deleted because it is linked to borrow history. It has been archived instead.';
        }

        redirect(BASE_URL . '/?url=book/index');
    }

    // RFID scan endpoint: returns JSON book info by UID
    public function scanRfid()
    {
        header('Content-Type: application/json');
        $uid = $_GET['uid'] ?? null;
        if (!$uid) { echo json_encode(['ok'=>false,'error'=>'No UID provided']); return; }
        $book = (new Book_model())->findByRfid($uid);
        if ($book) {
            echo json_encode(['ok'=>true,'book'=>$book]);
        } else {
            echo json_encode(['ok'=>false,'error'=>'Book not found']);
        }
    }
}
