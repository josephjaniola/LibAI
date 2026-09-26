<div class="page-tool-header d-flex justify-content-between align-items-center mb-3">
  <h3>Activity Logs</h3>
  <small class="text-muted">Showing latest 200 entries</small>
</div>
<div class="admin-list-table table-responsive"><table class="table table-sm table-striped mb-0">
  <thead><tr><th>When</th><th>User</th><th>Action</th><th>Detail</th><th>IP</th></tr></thead>
  <tbody>
  <?php foreach ($logs as $l): ?>
    <tr>
      <td><?php echo e($l['created_at']); ?></td>
      <td><?php echo e($l['user_type']); ?>#<?php echo e($l['user_ref_id']); ?></td>
      <td><?php echo e($l['action']); ?></td>
      <td style="max-width:400px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?php echo e($l['detail']); ?></td>
      <td><?php echo e($l['ip_address']); ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
</div>
