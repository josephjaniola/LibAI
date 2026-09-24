<div class="row justify-content-center">
  <div class="col-md-8">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Edit Librarian</h4>
        <?php if (!empty($error)): ?><div class="alert alert-danger"><?php echo e($error); ?></div><?php endif; ?>
        <form method="post" enctype="multipart/form-data" action="?url=admin/editLibrarian/<?php echo $librarian['id']; ?>">
          <input type="hidden" name="_csrf" value="<?php echo generate_csrf_token(); ?>">
          <div class="row">
            <div class="col-md-6 mb-3"><input type="text" name="username" value="<?php echo e($librarian['username']); ?>" class="form-control" placeholder="Username"></div>
            <div class="col-md-6 mb-3"><input type="email" name="email" value="<?php echo e($librarian['email']); ?>" class="form-control" placeholder="Email"></div>
          </div>
          <div class="row">
            <div class="col-md-4 mb-3"><input type="text" name="firstname" value="<?php echo e($librarian['firstname']); ?>" class="form-control" placeholder="First name"></div>
            <div class="col-md-4 mb-3"><input type="text" name="middlename" value="<?php echo e($librarian['middlename']); ?>" class="form-control" placeholder="Middle name"></div>
            <div class="col-md-4 mb-3"><input type="text" name="lastname" value="<?php echo e($librarian['lastname']); ?>" class="form-control" placeholder="Last name"></div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3"><input type="text" name="mobile" value="<?php echo e($librarian['mobile']); ?>" class="form-control" placeholder="Mobile"></div>
            <div class="col-md-6 mb-3"></div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3"><input type="password" name="password" class="form-control" placeholder="New password (leave blank to keep)"></div>
            <div class="col-md-6 mb-3"></div>
          </div>
          <div class="mb-3">
            <label class="form-label">Profile Picture</label>
            <input type="file" name="profile_picture" class="form-control">
          </div>
          <div class="d-grid"><button class="btn btn-success">Save Changes</button></div>
        </form>
      </div>
    </div>
  </div>
</div>
