<div class="card">
  <div class="card-body">
    <h4>Add Category</h4>
    <?php if (!empty($error)): ?><div class="alert alert-danger"><?php echo e($error); ?></div><?php endif; ?>
    <form method="post" action="?url=admincategories/add" class="admin-validate">
      <input type="hidden" name="_csrf" value="<?php echo generate_csrf_token(); ?>">
      <div class="mb-3"><input type="text" name="name" class="form-control" placeholder="Category name" required></div>
      <div class="mb-3"><textarea name="description" class="form-control" placeholder="Description"></textarea></div>
      <div class="d-grid"><button type="submit" class="btn btn-success">Create</button></div>
    </form>
  </div>
</div>
