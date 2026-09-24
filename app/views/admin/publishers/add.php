<div class="card">
  <div class="card-body">
    <h4>Add Publisher</h4>
    <?php if (!empty($error)): ?><div class="alert alert-danger"><?php echo e($error); ?></div><?php endif; ?>
    <form method="post" action="?url=adminpublishers/add" class="admin-validate">
      <input type="hidden" name="_csrf" value="<?php echo generate_csrf_token(); ?>">
      <div class="mb-3"><input type="text" name="name" class="form-control" placeholder="Publisher name" required></div>
      <div class="d-grid"><button type="submit" class="btn btn-success">Create</button></div>
    </form>
  </div>
</div>
