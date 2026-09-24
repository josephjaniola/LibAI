<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Reservations</h3>
  <a href="?url=reservation/createForm" class="btn btn-primary">New Reservation</a>
</div>
<?php if (!empty($_SESSION['flash'])): ?><div class="alert alert-success"><?php echo e($_SESSION['flash']); unset($_SESSION['flash']); ?></div><?php endif; ?>
<?php if (!empty($_SESSION['flash_error'])): ?><div class="alert alert-danger"><?php echo e($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></div><?php endif; ?>
<div class="table-responsive-sm">
<table class="table table-striped">
  <thead><tr><th>Book</th><th>Borrower</th><th>Type</th><th>Status</th><th>Reserved At</th><th>Expires</th><th>Actions</th></tr></thead>
  <tbody>
    <?php foreach ($reservations as $r): ?>
    <tr>
      <td><?php echo e($r['title']); ?></td>
      <td><?php echo e($r['borrower_type']); ?> #<?php echo e($r['borrower_ref_id']); ?></td>
      <td><?php echo e(ucfirst($r['borrower_type'])); ?></td>
      <td><?php echo e(ucfirst($r['status'])); ?></td>
      <td><?php echo e($r['reserved_at']); ?></td>
      <td><?php echo e($r['expires_at']); ?></td>
      <td>
        <?php if (in_array($r['status'], ['pending','approved'])): ?>
          <a href="?url=reservation/ready/<?php echo $r['id']; ?>" class="btn btn-sm btn-success me-1">Mark Ready</a>
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
