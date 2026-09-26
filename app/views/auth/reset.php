<?php
$showVerification = !empty($show_verification);
$verificationEmail = $_SESSION['password_reset_email'] ?? ($verification_email ?? '');
$error = $error ?? '';
$success = $success ?? '';
$showResetForm = $showVerification;
?>
<div class="auth-login-shell" style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #f8fbff 0%, #eef4ff 100%); padding: 30px 18px; font-family: Arial, Helvetica, sans-serif;">
  <div style="width: 100%; max-width: 440px; background: rgba(255,255,255,0.96); border: 1px solid rgba(148,163,184,0.2); border-radius: 26px; box-shadow: 0 28px 80px rgba(15, 23, 42, 0.12); padding: 30px 28px 26px;">
    <div style="text-align: center; margin-bottom: 20px;">
      <img src="<?php echo BASE_URL; ?>/logo/logo1.png" alt="LibAI logo" style="width: 72px; height: 72px; object-fit: contain; margin-bottom: 12px;">
      <div style="display: inline-block; padding: 8px 14px; border-radius: 999px; background: #e0ecff; color: #1d4ed8; font-size: 0.76rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; margin-bottom: 12px;">Secure recovery</div>
      <h2 style="margin: 0; font-size: 2rem; font-weight: 800; color: #101828; letter-spacing: -0.03em;">Enter Verification Code</h2>
      <p style="margin: 10px 0 0; color: #475467; font-size: 0.96rem; line-height: 1.5;">We sent a 6-digit verification code to your email.</p>
    </div>

    <?php if (!empty($error)): ?>
      <div style="margin-bottom: 14px; background: #fef2f2; color: #b42318; border: 1px solid #f4c7c7; border-radius: 12px; padding: 10px 12px; font-size: 0.95rem;">
        <?php echo e($error); ?>
      </div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
      <div style="margin-bottom: 14px; background: #ecfdf5; color: #166534; border: 1px solid #b7e4c7; border-radius: 12px; padding: 10px 12px; font-size: 0.95rem;">
        <?php echo e($success); ?>
      </div>
    <?php endif; ?>

    <?php if ($showResetForm): ?>
      <form method="post" action="?url=auth/reset" style="margin: 0 0 18px;">
        <input type="hidden" name="_csrf" value="<?php echo generate_csrf_token(); ?>">
        <input type="hidden" name="verification_email" value="<?php echo e($verificationEmail); ?>">
        <input type="hidden" name="code" id="reset-hidden-code" value="">

        <div style="display: flex; justify-content: center; gap: 10px; margin: 18px 0 18px;">
          <?php for ($i = 0; $i < 6; $i++): ?>
            <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" autocomplete="one-time-code" class="reset-verification-digit" aria-label="Verification digit <?php echo $i + 1; ?>" style="width: 50px; height: 56px; text-align: center; border: 1px solid #d0d5dd; border-radius: 12px; font-size: 1.7rem; font-weight: 700; color: #101828; background: #fff; box-sizing: border-box;" />
          <?php endfor; ?>
        </div>

        <div style="margin-bottom: 16px;">
          <label for="password" style="display: block; margin-bottom: 8px; color: #344054; font-size: 0.9rem; font-weight: 700;">New Password</label>
          <input type="password" name="password" id="password" placeholder="Enter new password" style="width: 100%; padding: 13px 14px; border: 1px solid #d0d5dd; border-radius: 12px; box-sizing: border-box; font-size: 1rem;" required>
        </div>

        <div style="margin-bottom: 18px;">
          <label for="confirm_password" style="display: block; margin-bottom: 8px; color: #344054; font-size: 0.9rem; font-weight: 700;">Confirm Password</label>
          <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm your password" style="width: 100%; padding: 13px 14px; border: 1px solid #d0d5dd; border-radius: 12px; box-sizing: border-box; font-size: 1rem;" required>
        </div>

        <button type="submit" style="width: 100%; background: linear-gradient(135deg, #2563eb, #1d4ed8); border: none; border-radius: 12px; color: #ffffff; font-size: 1rem; font-weight: 800; padding: 14px 16px; cursor: pointer; box-shadow: 0 14px 24px rgba(37, 99, 235, 0.25);">Reset Password</button>
      </form>

      <div style="text-align: center; margin-top: 14px;">
        <form method="post" action="?url=auth/forgot" style="display: inline-block; margin: 0;">
          <input type="hidden" name="_csrf" value="<?php echo generate_csrf_token(); ?>">
          <input type="hidden" name="email" value="<?php echo e($verificationEmail); ?>">
          <button type="submit" style="background: transparent; border: none; color: #1d4ed8; font-weight: 700; cursor: pointer; padding: 0;">Resend Code</button>
        </form>
      </div>
    <?php else: ?>
      <div style="text-align: center; padding: 18px 12px 8px;">
        <a href="?url=auth/login" style="display: inline-block; width: 100%; background: linear-gradient(135deg, #2563eb, #1d4ed8); border-radius: 12px; color: #ffffff; font-size: 1rem; font-weight: 800; padding: 14px 16px; text-decoration: none; box-shadow: 0 14px 24px rgba(37, 99, 235, 0.25);">Go to Login</a>
      </div>
    <?php endif; ?>

    <div style="text-align: center; margin-top: 18px;">
      <a href="?url=auth/login" style="color: #475467; text-decoration: none; font-size: 0.92rem;">Back to login</a>
    </div>
  </div>
</div>

<script>
(function () {
  const digits = Array.from(document.querySelectorAll('.reset-verification-digit'));
  const hiddenCode = document.getElementById('reset-hidden-code');

  function updateHidden() {
    if (!hiddenCode) return;
    hiddenCode.value = digits.map((input) => input.value).join('');
  }

  function focusDigit(index) {
    const target = digits[index];
    if (!target) return;
    target.focus();
    target.select();
  }

  digits.forEach((input, index) => {
    input.addEventListener('input', function (event) {
      const value = event.target.value.replace(/\D/g, '').slice(0, 1);
      event.target.value = value;
      updateHidden();
      if (value && index < digits.length - 1) {
        focusDigit(index + 1);
      }
    });

    input.addEventListener('keydown', function (event) {
      if (event.key === 'Backspace' && !event.target.value && index > 0) {
        event.preventDefault();
        digits[index - 1].value = '';
        focusDigit(index - 1);
        updateHidden();
      }

      if (event.key === 'ArrowLeft' && index > 0) {
        event.preventDefault();
        focusDigit(index - 1);
      }

      if (event.key === 'ArrowRight' && index < digits.length - 1) {
        event.preventDefault();
        focusDigit(index + 1);
      }
    });

    input.addEventListener('paste', function (event) {
      const pasted = (event.clipboardData || window.clipboardData).getData('text');
      const cleaned = pasted.replace(/\D/g, '').slice(0, digits.length);
      if (!cleaned) return;
      event.preventDefault();
      cleaned.split('').forEach((char, i) => {
        if (digits[i]) digits[i].value = char;
      });
      updateHidden();
      focusDigit(Math.min(cleaned.length, digits.length - 1));
    });
  });

  if (digits.length) focusDigit(0);
})();
</script>
