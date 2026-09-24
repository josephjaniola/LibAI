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
      <td><?php echo e($b['status']); ?></td>
      <td>
        <?php if (!in_array($_SESSION['user_role'], ['student','faculty'], true)): ?>
          <a class="btn btn-sm btn-secondary" href="?url=book/edit/<?php echo $b['id']; ?>">Edit</a>
          <a class="btn btn-sm btn-danger" href="?url=book/delete/<?php echo $b['id']; ?>" onclick="return confirm('Delete book?');">Delete</a>
        <?php else: ?>
          <span class="text-muted">Read-only access</span>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
