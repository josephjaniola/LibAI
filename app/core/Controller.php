<?php
class Controller
{
    protected function view($view, $data = [])
    {
        extract($data);
        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/' . $view . '.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    protected function handleUpload($file)
    {
        $allowed = ['image/jpeg', 'image/png', 'image/jpg'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['ok' => false, 'error' => 'Upload error.'];
        }

        if ($file['size'] > 2 * 1024 * 1024) {
            return ['ok' => false, 'error' => 'File too large (max 2MB).'];
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $allowed)) {
            return ['ok' => false, 'error' => 'Invalid file type.'];
        }

        $ext = $mime === 'image/png' ? 'png' : 'jpg';
        $name = uniqid('prof_') . '.' . $ext;
        $destDir = __DIR__ . '/../../uploads/profiles';
        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        $dest = $destDir . '/' . $name;
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            return ['ok' => false, 'error' => 'Failed to move uploaded file.'];
        }

        return ['ok' => true, 'path' => 'uploads/profiles/' . $name];
    }
}
