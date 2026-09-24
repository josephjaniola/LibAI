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
      <h2 style="margin: 0; font-size: 2rem; font-weight: 700; color: #101828; letter-spacing: -0.03em;">LibAI Login</h2>
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

    <a href="?url=auth/google" style="display: flex; align-items: center; justify-content: center; width: 100%; background: #ffffff; border: 1px solid #d0d5dd; border-radius: 10px; color: #111827; font-size: 1rem; font-weight: 600; text-decoration: none; padding: 12px 14px; margin-bottom: 18px; gap: 10px;">
      <span style="width: 22px; height: 22px; border-radius: 50%; background: #4285F4; color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700;">G</span>
      <span>Continue with Google</span>
    </a>

    <div style="display: flex; align-items: center; margin: 12px 0 18px; color: #667085; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.08em;">
      <div style="flex: 1; border-bottom: 1px solid #e4e7ec;"></div>
      <span style="padding: 0 12px;">Or</span>
      <div style="flex: 1; border-bottom: 1px solid #e4e7ec;"></div>
    </div>

    <form method="post" action="?url=auth/sendOtp" style="margin: 0;">
      <input type="hidden" name="_csrf" value="<?php echo generate_csrf_token(); ?>">
      <label for="phone" style="display: block; margin-bottom: 8px; color: #344054; font-size: 0.9rem; font-weight: 600;">Phone Number</label>
      <div style="display: flex; align-items: center; border: 1px solid #d0d5dd; border-radius: 10px; overflow: hidden; background: #ffffff; margin-bottom: 14px;">
        <span style="padding: 12px 14px; background: #f9fafb; border-right: 1px solid #e5e7eb; color: #475467; font-weight: 600;">+63</span>
        <input type="tel" name="phone" id="phone" value="<?php echo e($otpIdentifier); ?>" inputmode="numeric" pattern="[0-9]{10}" placeholder="9XXXXXXXXX" style="flex: 1; border: none; padding: 12px 14px; font-size: 1rem; outline: none; box-sizing: border-box;" required>
      </div>
      <button type="submit" style="width: 100%; background: #1d4ed8; border: none; border-radius: 10px; color: #ffffff; font-size: 1rem; font-weight: 700; padding: 12px 16px; cursor: pointer; margin-bottom: 8px;">Continue with Phone</button>
    </form>

    <?php if (!empty($success) || !empty($otpIdentifier)): ?>
      <form method="post" action="?url=auth/verifyOtp" style="margin-top: 22px; border-top: 1px solid #eef2f6; padding-top: 18px;">
        <input type="hidden" name="_csrf" value="<?php echo generate_csrf_token(); ?>">
        <input type="hidden" name="phone" value="<?php echo e($otpIdentifier); ?>">
        <p style="margin: 0 0 12px; font-size: 1.05rem; font-weight: 700; color: #101828; text-align: center;">Verify Your Number</p>
        <label for="otp-code" style="display: block; margin-bottom: 8px; color: #344054; font-size: 0.9rem; font-weight: 600;">Enter 6-digit code</label>
        <div style="display: flex; justify-content: center; gap: 8px; margin-bottom: 14px;">
          <input type="text" name="otp" id="otp-code" inputmode="numeric" maxlength="6" autocomplete="one-time-code" style="width: 100%; padding: 12px 14px; border: 1px solid #d0d5dd; border-radius: 10px; font-size: 1.1rem; text-align: center; letter-spacing: 0.35rem; box-sizing: border-box;" placeholder="______" required>
        </div>
        <button type="submit" style="width: 100%; background: #1d4ed8; border: none; border-radius: 10px; color: #ffffff; font-size: 1rem; font-weight: 700; padding: 12px 16px; cursor: pointer;">Verify</button>
        <p style="margin: 12px 0 0; text-align: center; color: #667085; font-size: 0.88rem;">Resend code in 30 seconds</p>
      </form>
    <?php endif; ?>
  </div>
</div>
