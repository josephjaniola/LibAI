<div class="page-tool-header d-flex justify-content-between align-items-center mb-3">
  <h3>Categories</h3>
  <a href="?url=admincategories/add" class="btn btn-primary">Add Category</a>
</div>
<?php if (!empty($_SESSION['flash'])): ?><div class="alert alert-success"><?php echo e($_SESSION['flash']); unset($_SESSION['flash']); ?></div><?php endif; ?>
<div class="admin-list-table table-responsive"><table class="table table-striped mb-0">
  <thead><tr><th>Name</th><th>Description</th><th style="width:150px">Actions</th></tr></thead>
  <tbody>
  <?php foreach ($categories as $c): ?>
    <tr>
      <td><?php echo e($c['name']); ?></td>
      <td><?php echo e($c['description']); ?></td>
      <td>
        <a href="?url=admincategories/edit&id=<?php echo $c['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
        <form method="post" action="?url=admincategories/delete" style="display:inline-block" onsubmit="return confirm('Delete this category?');">
          <input type="hidden" name="_csrf" value="<?php echo generate_csrf_token(); ?>">
          <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
          <button class="btn btn-sm btn-outline-danger">Delete</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
</div>
