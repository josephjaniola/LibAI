<div class="page-tool-header d-flex justify-content-between align-items-center mb-3">
  <h3>Librarians</h3>
  <a href="?url=admin/addLibrarian" class="btn btn-primary">Add Librarian</a>
</div>
<?php if (!empty($_SESSION['flash'])): ?>
  <div class="alert alert-success"><?php echo e($_SESSION['flash']); unset($_SESSION['flash']); ?></div>
<?php endif; ?>
<div class="admin-list-table table-responsive"><table class="table table-striped mb-0">
  <thead>
    <tr><th>ID</th><th>Name</th><th>Email</th><th>Mobile</th><th>Actions</th></tr>
  </thead>
  <tbody>
    <?php foreach ($librarians as $l): ?>
      <tr>
        <td><?php echo e($l['librarian_id']); ?></td>
        <td><?php echo e($l['firstname'] . ' ' . ($l['lastname'] ?? '')); ?></td>
        <td><?php echo e($l['email']); ?></td>
        <td><?php echo e($l['mobile']); ?></td>
        <td>
          <a class="btn btn-sm btn-secondary" href="?url=admin/editLibrarian/<?php echo $l['id']; ?>">Edit</a>
          <a class="btn btn-sm btn-danger" href="?url=admin/deleteLibrarian/<?php echo $l['id']; ?>" onclick="return confirm('Delete librarian?');">Delete</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>
