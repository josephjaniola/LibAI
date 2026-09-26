<div class="inventory-page page-tool-header d-flex justify-content-between align-items-center mb-3">
  <h3>Inventory</h3>
  <div>
    <a class="btn btn-secondary" href="?url=inventory/exportCsv">Export CSV</a>
  </div>
</div>
<div class="inventory-table table-responsive">
<table class="table table-sm mb-0">
  <thead><tr><th>RFID</th><th>Title</th><th>Accession</th><th>Call</th><th>Category</th><th>Status</th></tr></thead>
  <tbody>
    <?php foreach ($books as $b): ?>
      <tr>
        <td><?php echo e($b['rfid_uid']); ?></td>
        <td><?php echo e($b['title']); ?></td>
        <td><?php echo e($b['accession_number']); ?></td>
        <td><?php echo e($b['call_number']); ?></td>
        <td><?php echo e($b['category_name']); ?></td>
        <td><?php echo e($b['status']); ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>
