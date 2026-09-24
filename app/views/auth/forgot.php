<div class="auth-login-shell">
  <div class="row justify-content-center">
    <div class="col-xl-6">
      <div class="login-panel">
        <div class="login-panel-body">
          <div class="login-header">
            <h2>Forgot Password</h2>
            <p class="text-muted">Enter your username, email, or ID and we’ll send a reset link.</p>
          </div>

          <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo e($error); ?></div>
          <?php endif; ?>
          <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo e($success); ?></div>
          <?php endif; ?>

          <form method="post" action="?url=auth/forgot" class="auth-login-form">
            <input type="hidden" name="_csrf" value="<?php echo generate_csrf_token(); ?>">
            <div class="form-group mb-3">
              <label for="identifier" class="form-label">Username / Email / ID</label>
              <input type="text" name="identifier" id="identifier" class="form-control" required>
            </div>
            <div class="d-grid">
              <button type="submit" class="btn btn-primary">Send Reset Link</button>
            </div>
          </form>

          <div class="login-footer text-center mt-4">
            <a href="?url=auth/login" class="btn btn-outline-secondary btn-sm">Back to Login</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
