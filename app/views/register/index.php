<div class="profile-edit-shell register-shell register-choice-shell">
  <div class="card profile-edit-card">
    <div class="card-body">
      <div class="register-heading">
        <img class="register-brand-mark" src="<?php echo BASE_URL; ?>/logo/logo1.png" alt="LibAI logo">
        <div>
          <p class="profile-edit-eyebrow">LibAI membership</p>
          <h1>Create your account</h1>
          <p>Choose the account type that matches your library community.</p>
        </div>
      </div>
      <?php if (!empty($showGoogleNotice)): ?>
        <div id="googleSignupNotice" class="register-google-notice" role="status" aria-live="polite">
          <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
          <div>
            <strong>Complete your details</strong>
            <p>Your Google name and email are prefilled. Choose Student or Faculty to continue to the form.</p>
          </div>
        </div>
        <script>
          window.setTimeout(function () {
            var notice = document.getElementById('googleSignupNotice');
            if (!notice) return;
            notice.classList.add('is-dismissing');
            window.setTimeout(function () { notice.remove(); }, 300);
          }, 3000);
        </script>
      <?php endif; ?>
      <div class="profile-form-section register-choice-section">
        <div class="profile-form-section-heading">
          <h2>Choose your account</h2>
          <span>Select the option that fits you</span>
        </div>
        <div class="register-role-list">
          <a href="?url=register/student<?php echo !empty($googleSignup) ? '&amp;google_signup=1' : ''; ?>" class="register-role-option">
            <span class="register-role-icon"><i class="fa-solid fa-user-graduate" aria-hidden="true"></i></span>
            <span class="register-role-copy"><strong>Register as Student</strong></span>
            <i class="fa-solid fa-chevron-right register-role-arrow" aria-hidden="true"></i>
          </a>
          <a href="?url=register/faculty<?php echo !empty($googleSignup) ? '&amp;google_signup=1' : ''; ?>" class="register-role-option">
            <span class="register-role-icon register-role-icon-faculty"><i class="fa-solid fa-user-tie" aria-hidden="true"></i></span>
            <span class="register-role-copy"><strong>Register as Faculty</strong></span>
            <i class="fa-solid fa-chevron-right register-role-arrow" aria-hidden="true"></i>
          </a>
        </div>
      </div>

      <div class="profile-edit-actions register-index-actions">
        <a href="?url=auth/login" class="btn btn-light">Already have an account? Login</a>
        <a href="<?php echo e(BASE_URL); ?>" class="btn btn-outline-secondary">
          <i class="fa-solid fa-house me-2" aria-hidden="true"></i>Go Home
        </a>
      </div>
    </div>
  </div>
</div>
