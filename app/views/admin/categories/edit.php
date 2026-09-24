<div class="card">
  <div class="card-body">
    <h4>Edit Category</h4>
    <?php if (!empty($error)): ?><div class="alert alert-danger"><?php echo e($error); ?></div><?php endif; ?>
    <form method="post" action="?url=admincategories/edit&id=<?php echo $category['id']; ?>" class="admin-validate">
      <input type="hidden" name="_csrf" value="<?php echo generate_csrf_token(); ?>">
      <div class="mb-3"><input type="text" name="name" class="form-control" value="<?php echo e($category['name']); ?>" required></div>
      <div class="mb-3"><textarea name="description" class="form-control"><?php echo e($category['description']); ?></textarea></div>
      <div class="d-grid"><button type="submit" class="btn btn-success">Save</button></div>
    </form>
  </div>
</div>
