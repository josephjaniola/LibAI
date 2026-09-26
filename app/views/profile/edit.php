<?php $profileRole = ucfirst((string) ($role ?? 'user')); ?>
<div class="profile-edit-shell">
  <div class="profile-edit-hero">
    <div>
      <p class="profile-edit-eyebrow">Account settings</p>
      <h1>Edit your profile</h1>
      <p>Update your personal information and keep your LibAI account current.</p>
    </div>
  </div>

  <div class="card profile-edit-card">
      <div class="card-body">
        <?php if (!empty($error)): ?>
          <div class="alert alert-danger"><?php echo e($error); ?></div>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data" action="?url=profile/edit">
          <input type="hidden" name="_csrf" value="<?php echo generate_csrf_token(); ?>">

          <div class="profile-form-section">
            <div class="profile-form-section-heading">
              <h2>Academic details</h2>
              <span><?php echo e($profileRole); ?></span>
            </div>
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
                <div class="select-with-arrow">
                  <select name="year_level" class="form-select">
                    <option value="">Select year</option>
                    <option value="1"<?php echo (($user['year_level'] ?? '') === '1') ? ' selected' : ''; ?>>1st Year</option>
                    <option value="2"<?php echo (($user['year_level'] ?? '') === '2') ? ' selected' : ''; ?>>2nd Year</option>
                    <option value="3"<?php echo (($user['year_level'] ?? '') === '3') ? ' selected' : ''; ?>>3rd Year</option>
                    <option value="4"<?php echo (($user['year_level'] ?? '') === '4') ? ' selected' : ''; ?>>4th Year</option>
                  </select>
                  <i class="fa-solid fa-chevron-down select-with-arrow-icon" aria-hidden="true"></i>
                </div>
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
          </div>

          <div class="profile-form-section">
            <div class="profile-form-section-heading">
              <h2>Personal details</h2>
              <span>Visible on your library account</span>
            </div>
          <div class="row">
            <div class="col-md-4 mb-3"><label class="form-label">First name</label><input type="text" name="firstname" value="<?php echo e($user['firstname'] ?? ''); ?>" class="form-control"></div>
            <div class="col-md-4 mb-3"><label class="form-label">Middle name</label><input type="text" name="middlename" value="<?php echo e($user['middlename'] ?? ''); ?>" class="form-control"></div>
            <div class="col-md-4 mb-3"><label class="form-label">Last name</label><input type="text" name="lastname" value="<?php echo e($user['lastname'] ?? ''); ?>" class="form-control"></div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3"><label class="form-label">Email address</label><input type="email" name="email" value="<?php echo e($user['email'] ?? ''); ?>" class="form-control"></div>
            <div class="col-md-6 mb-3"><label class="form-label">Mobile number</label><input type="text" name="mobile" value="<?php echo e($user['mobile'] ?? ''); ?>" class="form-control"></div>
          </div>
          </div>

          <div class="profile-form-section">
            <div class="profile-form-section-heading">
              <h2>Security</h2>
              <span>Leave blank to keep your password</span>
            </div>
          <div class="row">
            <div class="col-md-6 mb-3"><label class="form-label">Current password</label><input type="password" name="current_password" class="form-control" placeholder="Required for password changes"></div>
            <div class="col-md-6 mb-3"><label class="form-label">New password</label><input type="password" name="password" class="form-control" placeholder="Leave blank to keep current password"></div>
          </div>
          </div>

          <div class="profile-edit-actions">
            <a href="?url=profile/view" class="btn btn-light">Cancel</a>
            <button class="btn btn-primary">Save Changes</button>
          </div>
        </form>
      </div>
  </div>
</div>
