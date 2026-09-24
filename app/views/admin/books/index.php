<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Books</h3>
  <?php if (!in_array($_SESSION['user_role'], ['student','faculty'], true)): ?>
    <a href="?url=book/add" class="btn btn-primary">Add Book</a>
  <?php endif; ?>
</div>
<?php if (!empty($_SESSION['flash'])): ?>
  <div class="alert alert-success"><?php echo e($_SESSION['flash']); unset($_SESSION['flash']); ?></div>
<?php endif; ?>
<table class="table table-sm table-hover">
  <thead>
    <tr><th>RFID</th><th>Cover</th><th>Title</th><th>Author(s)</th><th>Category</th><th>Status</th><th>Actions</th></tr>
  </thead>
  <tbody>
  <?php foreach ($books as $b): ?>
    <tr>
      <td><?php echo e($b['rfid_uid']); ?></td>
      <td><?php if ($b['cover_image']): ?><img src="<?php echo BASE_URL . '/' . e($b['cover_image']); ?>" style="height:60px;"><?php endif; ?></td>
      <td><?php echo e($b['title']); ?></td>
      <td><!-- authors placeholder --></td>
      <td><?php echo e($b['category_name']); ?></td>
      <td>
        <?php $userReservation = $userReservations[(int) $b['id']] ?? null; ?>
        <?php if ($userReservation && $userReservation['status'] === 'ready'): ?>
          <span class="badge bg-success">Ready to Pick Up</span>
        <?php else: ?>
          <?php if ($b['status'] === 'reserved'): ?>
            <span class="badge bg-warning text-dark">Not Available - Reserved</span>
          <?php elseif ($b['status'] === 'ready'): ?>
            <span class="badge bg-secondary">Not Available - Ready for Pickup</span>
          <?php elseif ($b['status'] === 'borrowed'): ?>
            <span class="badge bg-secondary">Not Available - Borrowed</span>
          <?php else: ?>
            <?php echo e($b['status']); ?>
          <?php endif; ?>
        <?php endif; ?>
      </td>
      <td>
        <?php if (!in_array($_SESSION['user_role'], ['student','faculty'], true)): ?>
          <a class="btn btn-sm btn-secondary" href="?url=book/edit/<?php echo $b['id']; ?>">Edit</a>
          <a class="btn btn-sm btn-danger" href="?url=book/delete/<?php echo $b['id']; ?>" onclick="return confirm('Delete book?');">Delete</a>
        <?php else: ?>
          <?php if ($userReservation && $userReservation['status'] === 'ready'): ?>
            <span class="badge bg-success me-1">Ready to Pick Up</span>
            <a class="btn btn-sm btn-outline-danger" href="?url=reservation/cancel/<?php echo (int) $userReservation['id']; ?>" onclick="return confirm('Cancel this reservation?');">Cancel Reservation</a>
          <?php elseif ($b['status'] === 'available' && !$userReservation): ?>
            <a class="btn btn-sm btn-primary" href="?url=book/reserve/<?php echo $b['id']; ?>" onclick="return confirm('Reserve this book?');">Reserve</a>
          <?php elseif ($userReservation): ?>
            <a class="btn btn-sm btn-outline-danger" href="?url=reservation/cancel/<?php echo (int) $userReservation['id']; ?>" onclick="return confirm('Cancel this reservation?');">Cancel Reservation</a>
          <?php else: ?>
            <span class="text-muted"><?php echo e(ucfirst($b['status'])); ?></span>
          <?php endif; ?>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
