<div class="row justify-content-center">
  <div class="col-md-8">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Edit Profile</h4>
        <?php if (!empty($error)): ?>
          <div class="alert alert-danger"><?php echo e($error); ?></div>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data" action="?url=profile/edit">
          <input type="hidden" name="_csrf" value="<?php echo generate_csrf_token(); ?>">

          <?php if (($role ?? '') === 'student'): ?>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Student ID</label>
                <input type="text" name="student_id" value="<?php echo e($user['student_id'] ?? ''); ?>" class="form-control">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Course</label>
                <select name="course" class="form-select">
                  <option value="">Select Course</option>
                  <?php foreach (getCourseOptions() as $value => $label): ?>
                    <option value="<?php echo e($value); ?>" <?php echo (($user['course'] ?? '') === $value) ? 'selected' : ''; ?>><?php echo e($label); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Year Level</label>
                <input type="text" name="year_level" value="<?php echo e($user['year_level'] ?? ''); ?>" class="form-control">
              </div>
            </div>
          <?php elseif (($role ?? '') === 'faculty'): ?>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Faculty ID</label>
                <input type="text" name="faculty_id" value="<?php echo e($user['faculty_id'] ?? ''); ?>" class="form-control">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Department</label>
                <select name="department" class="form-select">
                  <option value="">Select Department</option>
                  <?php foreach (getCourseOptions() as $value => $label): ?>
                    <option value="<?php echo e($value); ?>" <?php echo (($user['department'] ?? '') === $value) ? 'selected' : ''; ?>><?php echo e($label); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Position</label>
                <input type="text" name="position" value="<?php echo e($user['position'] ?? ''); ?>" class="form-control">
              </div>
            </div>
          <?php endif; ?>

          <div class="row">
            <div class="col-md-4 mb-3"><input type="text" name="firstname" value="<?php echo e($user['firstname'] ?? ''); ?>" class="form-control" placeholder="First name"></div>
            <div class="col-md-4 mb-3"><input type="text" name="middlename" value="<?php echo e($user['middlename'] ?? ''); ?>" class="form-control" placeholder="Middle name"></div>
            <div class="col-md-4 mb-3"><input type="text" name="lastname" value="<?php echo e($user['lastname'] ?? ''); ?>" class="form-control" placeholder="Last name"></div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3"><input type="email" name="email" value="<?php echo e($user['email'] ?? ''); ?>" class="form-control" placeholder="Email"></div>
            <div class="col-md-6 mb-3"><input type="text" name="mobile" value="<?php echo e($user['mobile'] ?? ''); ?>" class="form-control" placeholder="Mobile"></div>
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
