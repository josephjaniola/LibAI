<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Publishers</h3>
  <a href="?url=adminpublishers/add" class="btn btn-primary">Add Publisher</a>
</div>
<?php if (!empty($_SESSION['flash'])): ?><div class="alert alert-success"><?php echo e($_SESSION['flash']); unset($_SESSION['flash']); ?></div><?php endif; ?>
<table class="table table-striped">
  <thead><tr><th>Name</th><th style="width:150px">Actions</th></tr></thead>
  <tbody>
  <?php foreach ($publishers as $p): ?>
    <tr>
      <td><?php echo e($p['name']); ?></td>
      <td>
        <a href="?url=adminpublishers/edit&id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
        <form method="post" action="?url=adminpublishers/delete" style="display:inline-block" onsubmit="return confirm('Delete this publisher?');">
          <input type="hidden" name="_csrf" value="<?php echo generate_csrf_token(); ?>">
          <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
          <button class="btn btn-sm btn-outline-danger">Delete</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
