<div class="row justify-content-center">
  <div class="col-md-8">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Add Librarian</h4>
        <?php if (!empty($error)): ?><div class="alert alert-danger"><?php echo e($error); ?></div><?php endif; ?>
        <form method="post" enctype="multipart/form-data" action="?url=admin/addLibrarian">
          <input type="hidden" name="_csrf" value="<?php echo generate_csrf_token(); ?>">
          <div class="row">
            <div class="col-md-6 mb-3"><input type="text" name="librarian_id" class="form-control" placeholder="Librarian ID" required></div>
            <div class="col-md-6 mb-3"><input type="text" name="username" class="form-control" placeholder="Username"></div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3"><input type="email" name="email" class="form-control" placeholder="Email" required></div>
            <div class="col-md-6 mb-3"><input type="text" name="mobile" class="form-control" placeholder="Mobile"></div>
          </div>
          <div class="row">
            <div class="col-md-4 mb-3"><input type="text" name="firstname" class="form-control" placeholder="First name" required></div>
            <div class="col-md-4 mb-3"><input type="text" name="middlename" class="form-control" placeholder="Middle name"></div>
            <div class="col-md-4 mb-3"><input type="text" name="lastname" class="form-control" placeholder="Last name" required></div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3"><input type="password" name="password" class="form-control" placeholder="Password" required></div>
            <div class="col-md-6 mb-3"><input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required></div>
          </div>
          <div class="mb-3">
            <label class="form-label">Profile Picture</label>
            <input type="file" name="profile_picture" class="form-control">
          </div>
          <div class="d-grid"><button class="btn btn-success">Create Librarian</button></div>
        </form>
      </div>
    </div>
  </div>
</div>
