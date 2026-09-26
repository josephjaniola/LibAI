<div class="auth-login-shell" style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #f8fbff 0%, #eef4ff 100%); padding: 30px 18px; font-family: Arial, Helvetica, sans-serif;">
  <div style="width: 100%; max-width: 430px; background: rgba(255,255,255,0.95); border: 1px solid rgba(148,163,184,0.2); border-radius: 26px; box-shadow: 0 24px 70px rgba(15, 23, 42, 0.12); padding: 30px 28px 26px;">
    <div style="text-align: center; margin-bottom: 20px;">
      <img src="<?php echo BASE_URL; ?>/logo/logo1.png" alt="LibAI logo" style="width: 72px; height: 72px; object-fit: contain; margin-bottom: 12px;">
      <div style="display: inline-block; padding: 8px 14px; border-radius: 999px; background: #e0ecff; color: #1d4ed8; font-size: 0.76rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; margin-bottom: 12px;">Secure recovery</div>
      <h2 style="margin: 0; font-size: 2rem; font-weight: 800; color: #101828; letter-spacing: -0.03em;">Forgot Password</h2>
      <p style="margin: 10px 0 0; color: #475467; font-size: 0.96rem; line-height: 1.5;">Enter your Gmail address and we’ll send a 6-digit verification code.</p>
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

    <form method="post" action="?url=auth/forgot" style="margin: 0 0 18px;">
      <input type="hidden" name="_csrf" value="<?php echo generate_csrf_token(); ?>">

      <label for="email" style="display: block; margin-bottom: 8px; color: #344054; font-size: 0.9rem; font-weight: 700;">Gmail address</label>
      <input type="email" name="email" id="email" placeholder="you@gmail.com" style="width: 100%; padding: 13px 14px; border: 1px solid #d0d5dd; border-radius: 12px; box-sizing: border-box; font-size: 1rem; margin-bottom: 16px;" required>

      <button type="submit" style="width: 100%; background: linear-gradient(135deg, #2563eb, #1d4ed8); border: none; border-radius: 12px; color: #ffffff; font-size: 1rem; font-weight: 800; padding: 14px 16px; cursor: pointer; box-shadow: 0 14px 24px rgba(37, 99, 235, 0.25);">Send Code</button>
    </form>

    <div style="text-align: center; margin-top: 14px;">
      <a href="?url=auth/login" style="color: #475467; text-decoration: none; font-size: 0.92rem;">Back to login</a>
    </div>
  </div>
</div>
