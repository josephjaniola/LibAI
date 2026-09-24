<div class="row justify-content-center">
  <div class="col-md-8">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Student Registration</h4>
        <?php if (!empty($error)): ?>
          <div class="alert alert-danger"><?php echo e($error); ?></div>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data" action="?url=register/student">
          <input type="hidden" name="_csrf" value="<?php echo generate_csrf_token(); ?>">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Student ID</label>
              <input type="text" name="student_id" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" value="<?php echo e($email ?? ($_SESSION['google_signup_email'] ?? '')); ?>">
            </div>
          </div>
          <div class="row">
            <div class="col-md-4 mb-3"><input type="text" name="firstname" placeholder="First name" class="form-control" value="<?php echo e($firstname ?? ($_SESSION['google_signup_name'] ? explode(' ', trim($_SESSION['google_signup_name']))[0] : '')); ?>" required></div>
            <div class="col-md-4 mb-3"><input type="text" name="middlename" placeholder="Middle name" class="form-control"></div>
            <div class="col-md-4 mb-3"><input type="text" name="lastname" placeholder="Last name" class="form-control" value="<?php echo e($lastname ?? ($_SESSION['google_signup_name'] ? (isset(explode(' ', trim($_SESSION['google_signup_name']))[1]) ? explode(' ', trim($_SESSION['google_signup_name']))[1] : '') : '')); ?>" required></div>
          </div>
          <div class="row">
            <div class="col-md-4 mb-3">
              <label class="form-label">Course</label>
              <select name="course" class="form-select" required>
                <option value="">Select Course</option>
                <?php foreach (getCourseOptions() as $value => $label): ?>
                  <option value="<?php echo e($value); ?>"<?php echo (isset($_POST['course']) && $_POST['course'] === $value) ? ' selected' : ''; ?>><?php echo e($label); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4 mb-3"><input type="text" name="year_level" placeholder="Year Level" class="form-control"></div>
            <div class="col-md-4 mb-3"><input type="text" name="mobile" placeholder="Mobile" class="form-control"></div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3"><input type="password" name="password" placeholder="Password" class="form-control" required></div>
            <div class="col-md-6 mb-3"><input type="password" name="confirm_password" placeholder="Confirm Password" class="form-control" required></div>
          </div>
          <div class="mb-3">
            <label class="form-label">Profile Picture</label>
            <input type="file" name="profile_picture" class="form-control">
          </div>
          <div class="d-grid"><button class="btn btn-success">Register</button></div>
        </form>
      </div>
    </div>
  </div>
</div>
