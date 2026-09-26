<div class="profile-edit-shell register-shell">
  <div class="card profile-edit-card">
      <div class="card-body">
        <div class="register-heading">
          <img class="register-brand-mark" src="<?php echo BASE_URL; ?>/logo/logo1.png" alt="LibAI logo">
          <div>
            <p class="profile-edit-eyebrow">Faculty membership</p>
            <h1>Faculty Registration</h1>
            <p>Set up your faculty account to use the LibAI library.</p>
          </div>
        </div>
        <?php if (!empty($error)): ?>
          <div class="alert alert-danger"><?php echo e($error); ?></div>
        <?php endif; ?>
        <form class="register-form" method="post" enctype="multipart/form-data" action="?url=register/faculty">
          <input type="hidden" name="_csrf" value="<?php echo generate_csrf_token(); ?>">
          <div class="profile-form-section">
            <div class="profile-form-section-heading">
              <h2>Faculty details</h2>
              <span>Academic and contact information</span>
            </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Faculty ID</label>
              <input type="text" name="faculty_id" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" value="<?php echo e($email ?? ($_SESSION['google_signup_email'] ?? '')); ?>">
            </div>
          </div>
          <div class="row">
            <div class="col-md-4 mb-3">
              <label class="form-label" for="firstname">First name</label>
              <input type="text" id="firstname" name="firstname" autocomplete="given-name" class="form-control" value="<?php echo e($firstname ?? ($_SESSION['google_signup_name'] ? explode(' ', trim($_SESSION['google_signup_name']))[0] : '')); ?>" required>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label" for="middlename">Middle name</label>
              <input type="text" id="middlename" name="middlename" autocomplete="additional-name" class="form-control">
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label" for="lastname">Last name</label>
              <input type="text" id="lastname" name="lastname" autocomplete="family-name" class="form-control" value="<?php echo e($lastname ?? ($_SESSION['google_signup_name'] ? (isset(explode(' ', trim($_SESSION['google_signup_name']))[1]) ? explode(' ', trim($_SESSION['google_signup_name']))[1] : '') : '')); ?>" required>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label" for="department">Department</label>
              <div class="select-with-arrow">
                <select name="department" id="department" class="form-select" required>
                  <option value="">Select Department</option>
                  <?php foreach (getCourseOptions() as $value => $label): ?>
                    <option value="<?php echo e($value); ?>"<?php echo (isset($_POST['department']) && $_POST['department'] === $value) ? ' selected' : ''; ?>><?php echo e($label); ?></option>
                  <?php endforeach; ?>
                </select>
                <i class="fa-solid fa-chevron-down select-with-arrow-icon" aria-hidden="true"></i>
              </div>
            </div>
            <div class="col-md-6 mb-3"><input type="text" name="position" placeholder="Position" class="form-control"></div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label" for="mobile">Philippines mobile number</label>
              <input type="tel" id="mobile" name="mobile" autocomplete="tel" inputmode="tel" pattern="(?:09[0-9]{9}|\+639[0-9]{9})" maxlength="13" placeholder="09XXXXXXXXX or +639XXXXXXXXX" title="Enter a Philippine mobile number starting with 09 or +639" value="<?php echo e($_POST['mobile'] ?? ''); ?>" class="form-control">
            </div>
          </div>
          </div>
          <div class="profile-form-section">
            <div class="profile-form-section-heading">
              <h2>Security</h2>
              <span>Create a password for your account</span>
            </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label" for="faculty_password">Password</label>
              <input type="password" id="faculty_password" name="password" autocomplete="new-password" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label" for="confirm_password">Confirm password</label>
              <input type="password" id="confirm_password" name="confirm_password" autocomplete="new-password" class="form-control" required>
            </div>
          </div>
          </div>
          <div class="profile-edit-actions register-form-actions">
            <a href="?url=register<?php echo !empty($_SESSION['google_signup_active']) ? '&amp;google_signup=1' : ''; ?>" class="btn btn-outline-secondary">
              <i class="fa-solid fa-arrow-left me-1" aria-hidden="true"></i>Back to choose account type
            </a>
            <button class="btn btn-primary">Create account</button>
          </div>
        </form>
      </div>
  </div>
</div>
