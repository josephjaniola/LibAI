<?php
class Inventory extends Controller
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
        $books = (new Book_model())->getAll();
        $this->view('admin/inventory/index', ['books'=>$books]);
    }

    public function exportCsv()
    {
        $this->ensureLibrarianOrAdmin();
        $books = (new Book_model())->getAll();
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="inventory.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['RFID UID','Accession','Call Number','ISBN','Title','Author','Publisher','Category','Shelf','Status','Date Received','Year Published','Remarks']);
        foreach ($books as $b) {
            fputcsv($out, [
                $b['rfid_uid'],$b['accession_number'],$b['call_number'],$b['isbn'],$b['title'],$b['publisher_name'] ?? '',$b['publisher_name'] ?? '',$b['category_name'] ?? '',$b['shelf_location'],$b['status'],$b['date_received'],$b['year_published'],$b['remarks']
            ]);
        }
        fclose($out);
    }

    public function exportExcel()
    {
        $this->ensureLibrarianOrAdmin();
        // Use PhpSpreadsheet if available, otherwise fallback to CSV
        if (class_exists('PhpOffice\\PhpSpreadsheet\\Spreadsheet')) {
            $books = (new Book_model())->getAll();
            $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $headers = ['RFID UID','Accession','Call Number','ISBN','Title','Author','Publisher','Category','Shelf','Status','Date Received','Year Published','Remarks'];
            $sheet->fromArray($headers, NULL, 'A1');
            $row = 2;
            foreach ($books as $b) {
                $sheet->fromArray([
                    $b['rfid_uid'],$b['accession_number'],$b['call_number'],$b['isbn'],$b['title'],'', $b['publisher_name'] ?? '',$b['category_name'] ?? '',$b['shelf_location'],$b['status'],$b['date_received'],$b['year_published'],$b['remarks']
                ], NULL, 'A' . $row);
                $row++;
            }
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="inventory.xlsx"');
            $writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        } else {
            // fallback to CSV
            $this->exportCsv();
        }
    }

    public function exportPdf()
    {
        $this->ensureLibrarianOrAdmin();
        if (!class_exists('Dompdf\\Dompdf')) {
            // Dompdf not installed; fall back to CSV
            $this->exportCsv();
            return;
        }
        $books = (new Book_model())->getAll();
        $html = '<h2>Inventory</h2><table border="1" cellpadding="4" cellspacing="0"><tr><th>RFID</th><th>Title</th><th>Accession</th><th>Call</th><th>Category</th><th>Status</th></tr>';
        foreach ($books as $b) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($b['rfid_uid']) . '</td>';
            $html .= '<td>' . htmlspecialchars($b['title']) . '</td>';
            $html .= '<td>' . htmlspecialchars($b['accession_number']) . '</td>';
            $html .= '<td>' . htmlspecialchars($b['call_number']) . '</td>';
            $html .= '<td>' . htmlspecialchars($b['category_name']) . '</td>';
            $html .= '<td>' . htmlspecialchars($b['status']) . '</td>';
            $html .= '</tr>';
        }
        $html .= '</table>';
        $dompdf = new Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('inventory.pdf');
    }
}
