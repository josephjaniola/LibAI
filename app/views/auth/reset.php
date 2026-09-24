<div class="auth-login-shell">
  <div class="row justify-content-center">
    <div class="col-xl-6">
      <div class="login-panel">
        <div class="login-panel-body">
          <div class="login-header">
            <h2>Reset Password</h2>
            <p class="text-muted">Enter your new password to finish resetting your account.</p>
          </div>

          <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo e($error); ?></div>
          <?php endif; ?>
          <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo e($success); ?></div>
          <?php endif; ?>

          <?php if (empty($success)): ?>
            <form method="post" action="?url=auth/reset/<?php echo e($token); ?>" class="auth-login-form">
              <input type="hidden" name="_csrf" value="<?php echo generate_csrf_token(); ?>">
              <div class="form-group mb-3">
                <label for="password" class="form-label">New Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
              </div>
              <div class="form-group mb-4">
                <label for="confirm_password" class="form-label">Confirm New Password</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
              </div>
              <div class="d-grid">
                <button type="submit" class="btn btn-primary">Reset Password</button>
              </div>
            </form>
          <?php endif; ?>

          <div class="login-footer text-center mt-4">
            <a href="?url=auth/login" class="btn btn-outline-secondary btn-sm">Back to Login</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
