<div class="reservations-page page-tool-header d-flex justify-content-between align-items-center mb-3">
  <h3>Reservations</h3>
  <a href="?url=reservation/createForm" class="btn btn-primary">New Reservation</a>
</div>
<?php if (!empty($_SESSION['flash'])): ?><div class="alert alert-success"><?php echo e($_SESSION['flash']); unset($_SESSION['flash']); ?></div><?php endif; ?>
<?php if (!empty($_SESSION['flash_error'])): ?><div class="alert alert-danger"><?php echo e($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></div><?php endif; ?>
<div class="reservations-table table-responsive-sm">
<table class="table table-striped">
  <thead><tr><th>Book</th><th>Borrower</th><th>Type</th><th>Status</th><th>Reserved At</th><th>Expires</th><th>Actions</th></tr></thead>
  <tbody>
    <?php foreach ($reservations as $r): ?>
    <tr>
      <td><?php echo e($r['title']); ?></td>
      <td><?php echo e($r['borrower_type']); ?> #<?php echo e($r['borrower_ref_id']); ?></td>
      <td><?php echo e(ucfirst($r['borrower_type'])); ?></td>
      <td>
        <?php if ($r['status'] === 'ready'): ?>
          <span class="badge bg-success">Ready to Pick Up</span>
        <?php else: ?>
          <?php echo e(ucfirst($r['status'])); ?>
        <?php endif; ?>
      </td>
      <td><?php echo e($r['reserved_at']); ?></td>
      <td><?php echo e($r['expires_at']); ?></td>
      <td>
        <?php if ($r['status'] === 'pending'): ?>
          <a href="?url=reservation/approve/<?php echo (int) $r['id']; ?>" class="btn btn-sm btn-primary me-1" onclick="return confirm('Accept this reservation?');">Accept Reservation</a>
        <?php elseif ($r['status'] === 'approved'): ?>
          <a href="?url=reservation/ready/<?php echo (int) $r['id']; ?>" class="btn btn-sm btn-success me-1" onclick="return confirm('Mark this reservation as ready for pickup?');">Ready to Pick Up</a>
        <?php endif; ?>
        <?php if ($r['status'] !== 'cancelled'): ?>
          <a href="?url=reservation/cancel/<?php echo $r['id']; ?>" class="btn btn-sm btn-danger">Cancel</a>
        <?php endif; ?>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>
