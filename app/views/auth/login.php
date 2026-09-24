<?php
$error = $error ?? '';
$success = $success ?? '';
$otpIdentifier = $_SESSION['otp_identifier'] ?? '';
$flashError = $_SESSION['flash_error'] ?? '';
$flashSuccess = $_SESSION['flash'] ?? '';
unset($_SESSION['flash_error'], $_SESSION['flash']);
?>
<div class="auth-login-shell" style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #f5f7fb; font-family: Arial, Helvetica, sans-serif;">
  <div style="width: 100%; max-width: 430px; background: #ffffff; border: 1px solid #e5e7eb; border-radius: 20px; box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08); padding: 30px 28px 26px;">
    <div style="text-align: center; margin-bottom: 22px;">
      <div style="display: inline-block; padding: 8px 14px; border-radius: 999px; background: #e0ecff; color: #1d4ed8; font-size: 0.78rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; margin-bottom: 12px;">Welcome back!</div>
      <h2 style="margin: 0; font-size: 2rem; font-weight: 800; color: #101828; letter-spacing: -0.03em;">Let’s make your library day brighter.</h2>
      <p style="margin: 10px 0 0; color: #475467; font-size: 0.96rem; line-height: 1.5;">Sign in and discover your next great read with ease.</p>
    </div>

    <?php if (!empty($error) || !empty($flashError)): ?>
      <div style="margin-bottom: 14px; background: #fef2f2; color: #b42318; border: 1px solid #f4c7c7; border-radius: 10px; padding: 10px 12px; font-size: 0.95rem;">
        <?php echo e($error ?: $flashError); ?>
      </div>
    <?php endif; ?>
    <?php if (!empty($success) || !empty($flashSuccess)): ?>
      <div style="margin-bottom: 14px; background: #ecfdf5; color: #166534; border: 1px solid #b7e4c7; border-radius: 10px; padding: 10px 12px; font-size: 0.95rem;">
        <?php echo e($success ?: $flashSuccess); ?>
      </div>
    <?php endif; ?>

    <form method="post" action="?url=auth/login" style="margin: 0 0 18px;">
      <input type="hidden" name="_csrf" value="<?php echo generate_csrf_token(); ?>">

      <label for="identifier" style="display: block; margin-bottom: 8px; color: #344054; font-size: 0.9rem; font-weight: 600;">Email or Student ID</label>
      <input type="text" name="identifier" id="identifier" placeholder="Enter email or ID" style="width: 100%; padding: 12px 14px; border: 1px solid #d0d5dd; border-radius: 10px; margin-bottom: 14px; box-sizing: border-box; font-size: 1rem;" required>

      <label for="password" style="display: block; margin-bottom: 8px; color: #344054; font-size: 0.9rem; font-weight: 600;">Password</label>
      <input type="password" name="password" id="password" placeholder="Enter password" style="width: 100%; padding: 12px 14px; border: 1px solid #d0d5dd; border-radius: 10px; margin-bottom: 16px; box-sizing: border-box; font-size: 1rem;" required>

      <button type="submit" style="width: 100%; background: linear-gradient(135deg, #2563eb, #1d4ed8); border: none; border-radius: 10px; color: #ffffff; font-size: 1rem; font-weight: 800; padding: 12px 16px; cursor: pointer; box-shadow: 0 10px 20px rgba(37, 99, 235, 0.25);">Login to Explore</button>
    </form>

    <div style="display: flex; align-items: center; margin: 12px 0 18px; color: #667085; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.08em;">
      <div style="flex: 1; border-bottom: 1px solid #e4e7ec;"></div>
      <span style="padding: 0 12px;">Or</span>
      <div style="flex: 1; border-bottom: 1px solid #e4e7ec;"></div>
    </div>

    <a href="?url=auth/google" style="display: flex; align-items: center; justify-content: center; width: 100%; background: #ffffff; border: 1px solid #d0d5dd; border-radius: 10px; color: #111827; font-size: 1rem; font-weight: 700; text-decoration: none; padding: 12px 14px; margin-bottom: 18px; gap: 10px; box-shadow: 0 6px 16px rgba(15, 23, 42, 0.04);">
      <span style="width: 22px; height: 22px; border-radius: 50%; background: #4285F4; color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 800;">G</span>
      <span>Continue with Google and start smiling</span>
    </a>

  </div>
</div>
