<?php
class Reports extends Controller
{
    private function ensureLibrarianOrAdmin()
    {
        if (empty($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['admin','librarian'])) {
            redirect(BASE_URL . '/?url=auth/login');
        }
    }

    public function index()
    {
        $this->ensureLibrarianOrAdmin();
        $db = Database::getInstance();
        // date-range filter
        $start = isset($_GET['start']) ? trim($_GET['start']) : null;
        $end = isset($_GET['end']) ? trim($_GET['end']) : null;
        $now = new DateTime();
        try {
            $endDt = $end ? new DateTime($end) : $now;
        } catch (Exception $e) { $endDt = $now; }
        try {
            $startDt = $start ? new DateTime($start) : (clone $now)->modify('-11 months');
        } catch (Exception $e) { $startDt = (clone $now)->modify('-11 months'); }
        // normalize to date strings
        $startStr = $startDt->format('Y-m-01');
        $endStr = $endDt->format('Y-m-t');

        // Most borrowed books within range
        $sql = 'SELECT b.title, COUNT(bt.id) AS borrow_count FROM borrow_transactions bt JOIN books b ON bt.book_id=b.id WHERE bt.borrow_date BETWEEN :start AND :end GROUP BY bt.book_id ORDER BY borrow_count DESC LIMIT 10';
        $stmt = $db->prepare($sql);
        $stmt->execute([':start'=>$startStr, ':end'=>$endStr]);
        $most = $stmt->fetchAll();

        // Overdue count (overall)
        $sql2 = 'SELECT COUNT(*) as cnt FROM borrow_transactions WHERE status = "borrowed" AND due_date < NOW()';
        $overdue = $db->query($sql2)->fetch();

        // Monthly borrow trend (between start and end)
        $sql3 = "SELECT DATE_FORMAT(borrow_date, '%Y-%m') as ym, COUNT(*) as cnt FROM borrow_transactions WHERE borrow_date BETWEEN :start AND :end GROUP BY ym ORDER BY ym";
        $stmt = $db->prepare($sql3);
        $stmt->execute([':start'=>$startStr, ':end'=>$endStr]);
        $monthly = $stmt->fetchAll();

        // Inventory by category (not date-limited)
        $sql4 = 'SELECT c.name, COUNT(b.id) as cnt FROM books b LEFT JOIN categories c ON b.category_id = c.id GROUP BY c.id ORDER BY cnt DESC';
        $bycat = $db->query($sql4)->fetchAll();

        $this->view('admin/reports/index', ['most'=>$most, 'overdue'=>$overdue['cnt'] ?? 0, 'monthly'=>$monthly, 'bycat'=>$bycat]);
    }

    public function export()
    {
        $this->ensureLibrarianOrAdmin();
        $format = $_GET['format'] ?? 'csv';
        $type = $_GET['type'] ?? 'most';
        $start = isset($_GET['start']) ? trim($_GET['start']) : null;
        $end = isset($_GET['end']) ? trim($_GET['end']) : null;
        $now = new DateTime();
        try { $endDt = $end ? new DateTime($end) : $now; } catch (Exception $e) { $endDt = $now; }
        try { $startDt = $start ? new DateTime($start) : (clone $now)->modify('-11 months'); } catch (Exception $e) { $startDt = (clone $now)->modify('-11 months'); }
        $startStr = $startDt->format('Y-m-01');
        $endStr = $endDt->format('Y-m-t');

        $db = Database::getInstance();
        if ($format === 'csv') {
            header('Content-Type: text/csv');
            $fname = 'reports_' . $type . '_' . date('Ymd') . '.csv';
            header('Content-Disposition: attachment; filename="' . $fname . '"');
            $out = fopen('php://output', 'w');

            if ($type === 'most' || $type === 'all') {
                $sql = 'SELECT b.title, COUNT(bt.id) AS borrow_count FROM borrow_transactions bt JOIN books b ON bt.book_id=b.id WHERE bt.borrow_date BETWEEN :start AND :end GROUP BY bt.book_id ORDER BY borrow_count DESC';
                $stmt = $db->prepare($sql);
                $stmt->execute([':start'=>$startStr, ':end'=>$endStr]);
                $rows = $stmt->fetchAll();
                fputcsv($out, ['Most Borrowed (range ' . $startStr . ' to ' . $endStr . ')']);
                fputcsv($out, ['Title','Borrow Count']);
                foreach ($rows as $r) fputcsv($out, [$r['title'],$r['borrow_count']]);
                if ($type !== 'all') { fclose($out); exit; }
                fputcsv($out, []);
            }

            if ($type === 'monthly' || $type === 'all') {
                $sqlm = "SELECT DATE_FORMAT(borrow_date, '%Y-%m') as ym, COUNT(*) as cnt FROM borrow_transactions WHERE borrow_date BETWEEN :start AND :end GROUP BY ym ORDER BY ym";
                $stmt = $db->prepare($sqlm);
                $stmt->execute([':start'=>$startStr, ':end'=>$endStr]);
                $rows = $stmt->fetchAll();
                fputcsv($out, ['Monthly Borrows (range ' . $startStr . ' to ' . $endStr . ')']);
                fputcsv($out, ['Year-Month','Count']);
                foreach ($rows as $r) fputcsv($out, [$r['ym'],$r['cnt']]);
                if ($type !== 'all') { fclose($out); exit; }
                fputcsv($out, []);
            }

            if ($type === 'bycat' || $type === 'all') {
                $sqlc = 'SELECT c.name, COUNT(b.id) as cnt FROM books b LEFT JOIN categories c ON b.category_id = c.id GROUP BY c.id ORDER BY cnt DESC';
                $stmt = $db->query($sqlc);
                $rows = $stmt->fetchAll();
                fputcsv($out, ['Inventory by Category']);
                fputcsv($out, ['Category','Count']);
                foreach ($rows as $r) fputcsv($out, [$r['name'],$r['cnt']]);
                fclose($out); exit;
            }
            fclose($out);
            exit;
        }
        // future: handle xlsx/pdf
        redirect(BASE_URL . '/?url=reports/index');
    }
}
