<?php
$error = $error ?? '';
$success = $success ?? '';
$otpIdentifier = $_SESSION['otp_identifier'] ?? '';
$flashError = $_SESSION['flash_error'] ?? '';
$flashSuccess = $_SESSION['flash'] ?? '';
unset($_SESSION['flash_error'], $_SESSION['flash']);
?>
<div class="auth-login-shell" style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #ffffff; font-family: Arial, Helvetica, sans-serif; padding: 30px 18px;">
  <div class="auth-login-card" style="width: 100%; max-width: 980px; background: rgba(255,255,255,0.9); border: 1px solid rgba(148,163,184,0.2); border-radius: 32px; box-shadow: 0 28px 80px rgba(15, 23, 42, 0.14); overflow: hidden; display: flex; backdrop-filter: blur(10px);">
    <div class="auth-login-form-panel" style="flex: 1 1 52%; padding: 30px 28px 26px; background: #ffffff;">
      <?php if (!empty($error) || !empty($flashError)): ?>
        <div style="margin-bottom: 14px; background: #fef2f2; color: #b42318; border: 1px solid #f4c7c7; border-radius: 12px; padding: 10px 12px; font-size: 0.95rem; box-shadow: 0 8px 20px rgba(180,35,24,0.06);">
          <?php echo e($error ?: $flashError); ?>
        </div>
      <?php endif; ?>
      <?php if (!empty($success) || !empty($flashSuccess)): ?>
        <div style="margin-bottom: 14px; background: #ecfdf5; color: #166534; border: 1px solid #b7e4c7; border-radius: 12px; padding: 10px 12px; font-size: 0.95rem; box-shadow: 0 8px 20px rgba(22,101,52,0.06);">
          <?php echo e($success ?: $flashSuccess); ?>
        </div>
      <?php endif; ?>

      <form id="loginForm" method="post" action="?url=auth/login" data-gmail-check-url="<?php echo e(rtrim(BASE_URL, '/') . '/?url=auth/checkGmail'); ?>" style="margin: 0 0 18px;">
        <input type="hidden" name="_csrf" value="<?php echo generate_csrf_token(); ?>">

        <div style="text-align: center; margin-bottom: 18px;">
          <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 16px;">
            <img src="<?php echo BASE_URL; ?>/logo/logo1.png" alt="LibAI logo" style="width: 74px; height: 74px; object-fit: contain; filter: drop-shadow(0 8px 18px rgba(15, 23, 42, 0.12));">
          </div>
        </div>

        <div id="emailStep" style="margin-bottom: 16px;">
          <label for="identifier" style="display: block; margin-bottom: 8px; color: #344054; font-size: 0.9rem; font-weight: 700;">Email</label>
          <input type="text" name="identifier" id="identifier" value="<?php echo e($identifier ?? ''); ?>" placeholder="Enter your email" autocomplete="username" style="width: 100%; padding: 13px 14px; border: 1px solid #d0d5dd; border-radius: 12px; margin: 0; box-sizing: border-box; font-size: 1rem; background: #ffffff; transition: all 0.2s ease;" required>
          <div id="identifierStatus" role="status" aria-live="polite" style="display: none; margin-top: 8px; font-size: 0.86rem;"></div>
        </div>

        <div id="passwordStep" style="display: none; margin-bottom: 14px;">
          <label for="password" style="display: block; margin-bottom: 8px; color: #344054; font-size: 0.9rem; font-weight: 700;">Password</label>
          <div class="auth-password-wrap" style="position: relative;">
            <input type="password" name="password" id="password" placeholder="Enter password" style="width: 100%; padding: 13px 48px 13px 14px; border: 1px solid #d0d5dd; border-radius: 12px; box-sizing: border-box; font-size: 1rem; background: #fff; transition: all 0.2s ease;" aria-hidden="true" required>
            <button type="button" id="togglePassword" class="auth-password-toggle" aria-label="Show password" title="Show password" style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); width: 34px; height: 34px; border: 1px solid rgba(148,163,184,0.35); background: rgba(148,163,184,0.08); display: flex; align-items: center; justify-content: center; cursor: pointer; border-radius: 10px; padding: 0; box-shadow: inset 0 1px 0 rgba(255,255,255,0.35);">
              <img src="<?php echo BASE_URL; ?>/password%20icon/view.png" alt="Show password" style="width: 18px; height: 18px; display: block;">
            </button>
          </div>
        </div>

        <div id="emailActions" style="margin-bottom: 16px;">
          <button type="button" id="nextToPasswordBtn" style="width: 100%; background: linear-gradient(135deg, #2563eb, #1d4ed8); border: none; border-radius: 12px; color: #ffffff; font-size: 1rem; font-weight: 800; padding: 14px 16px; cursor: pointer; box-shadow: 0 14px 24px rgba(37, 99, 235, 0.25); transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;">
            <span style="display: inline-block; transition: transform 0.2s ease;">Next</span>
          </button>
          <a href="<?php echo e(rtrim(BASE_URL, '/') . '/?url=register'); ?>" style="display: block; margin-top: 14px; color: #2563eb; font-size: 0.9rem; font-weight: 700; text-align: center; text-decoration: none;">Don't have an account? Create account</a>
        </div>

        <div id="passwordActions" style="display: none;">
          <div class="auth-forgot-row" style="display: flex; justify-content: flex-end; margin: 0 0 18px;">
            <a href="?url=auth/forgot" style="color: #2563eb; font-size: 0.82rem; font-weight: 700; text-decoration: none;">Forgot your password?</a>
          </div>

          <button id="loginSubmitBtn" type="submit" style="width: 100%; background: linear-gradient(135deg, #2563eb, #1d4ed8); border: none; border-radius: 12px; color: #ffffff; font-size: 1rem; font-weight: 800; padding: 14px 16px; cursor: pointer; box-shadow: 0 14px 24px rgba(37, 99, 235, 0.25); transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;">
            <span style="display: inline-block; transition: transform 0.2s ease;">Login to Explore</span>
          </button>
        </div>
      </form>

      <div style="display: flex; align-items: center; margin: 12px 0 18px; color: #667085; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.08em;">
        <div style="flex: 1; border-bottom: 1px solid #e4e7ec;"></div>
        <span style="padding: 0 12px;">Or</span>
        <div style="flex: 1; border-bottom: 1px solid #e4e7ec;"></div>
      </div>

      <a href="?url=auth/google" style="display: flex; align-items: center; justify-content: center; width: 100%; background: #ffffff; border: 1px solid #d0d5dd; border-radius: 12px; color: #111827; font-size: 1rem; font-weight: 700; text-decoration: none; padding: 12px 14px; gap: 10px; box-shadow: 0 8px 18px rgba(15, 23, 42, 0.04); transition: box-shadow 0.2s ease, transform 0.2s ease, border-color 0.2s ease;">
        <span style="width: 22px; height: 22px; border-radius: 50%; background: #4285F4; color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 800;">G</span>
        <span>Continue with Google</span>
      </a>
      <a href="<?php echo e(BASE_URL); ?>" style="display: flex; align-items: center; justify-content: center; width: 100%; margin-top: 12px; background: #ffffff; border: 1px solid #d0d5dd; border-radius: 12px; color: #111827; font-size: 1rem; font-weight: 700; text-decoration: none; padding: 12px 14px; gap: 10px; box-shadow: 0 8px 18px rgba(15, 23, 42, 0.04); transition: box-shadow 0.2s ease, transform 0.2s ease, border-color 0.2s ease;">
        <i class="fa-solid fa-house" aria-hidden="true"></i>
        <span>Go Home</span>
      </a>
    </div>

    <div class="auth-login-welcome" style="order: -1; flex: 1 1 48%; background: #ffffff; color: #344054; border-right: 1px solid #e4e7ec; display: flex; align-items: center; justify-content: center; padding: 36px 28px; position: relative;">
      <div style="position: relative; max-width: 360px; text-align: left;">
        <div style="display: inline-block; padding: 8px 14px; border-radius: 999px; background: #f8fafc; color: #2563eb; border: 1px solid #e4e7ec; font-size: 0.76rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; margin-bottom: 18px;">Welcome back!</div>
        <h2 style="margin: 0 0 14px; font-size: clamp(2rem, 2.3vw, 2.7rem); line-height: 1.1; font-weight: 800; letter-spacing: -0.04em; color: #101828;">Let’s make your library day brighter.</h2>
        <p style="margin: 0; color: #667085; font-size: 1rem; line-height: 1.7;">Sign in and discover your next great read with ease.</p>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const emailStep = document.getElementById('emailStep');
    const passwordStep = document.getElementById('passwordStep');
    const emailActions = document.getElementById('emailActions');
    const passwordActions = document.getElementById('passwordActions');
    const nextBtn = document.getElementById('nextToPasswordBtn');
    const loginSubmitBtn = document.getElementById('loginSubmitBtn');
    const loginForm = document.getElementById('loginForm');
    const identifier = document.getElementById('identifier');
    const identifierStatus = document.getElementById('identifierStatus');
    const password = document.getElementById('password');
    const togglePassword = document.getElementById('togglePassword');

    if (!emailStep || !passwordStep || !emailActions || !passwordActions || !nextBtn || !identifier || !password || !togglePassword || !loginForm) {
      return;
    }

    identifier.required = true;
    password.required = false;
    password.setAttribute('aria-hidden', 'true');

    identifier.addEventListener('input', function () {
      identifier.setCustomValidity('');
      if (identifierStatus) {
        identifierStatus.textContent = '';
        identifierStatus.style.display = 'none';
      }
    });
    password.addEventListener('input', function () {
      password.setCustomValidity('');
    });

    nextBtn.addEventListener('click', async function () {
      if (!identifier.value.trim()) {
        identifier.setCustomValidity('Please enter your @gmail.com address.');
        identifier.reportValidity();
        identifier.focus();
        return;
      }
      identifier.setCustomValidity('');
      const identifierValue = identifier.value.trim().toLowerCase();
      const isAdminOrLibrarianUsername = identifierValue === 'admin' || identifierValue === 'librarian';
      const isGmailAddress = /^[^@\s]+@gmail\.com$/.test(identifierValue);
      if (!isAdminOrLibrarianUsername && !isGmailAddress) {
        identifier.setCustomValidity('Please enter your @gmail.com address.');
        identifier.reportValidity();
        identifier.focus();
        return;
      }

      if (isGmailAddress) {
        nextBtn.disabled = true;
        if (identifierStatus) {
          identifierStatus.style.color = '#667085';
          identifierStatus.textContent = 'Checking this Gmail address...';
          identifierStatus.style.display = 'block';
        }

        try {
          const checkResponse = await fetch(loginForm.dataset.gmailCheckUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' },
            body: new URLSearchParams({
              _csrf: loginForm.querySelector('[name="_csrf"]').value,
              identifier: identifier.value.trim()
            })
          });
          const checkResult = await checkResponse.json();
          if (!checkResponse.ok) {
            throw new Error(checkResult.error || 'Unable to check this Gmail address.');
          }
          if (!checkResult.registered) {
            if (identifierStatus) {
              identifierStatus.style.color = '#b42318';
              identifierStatus.textContent = 'No user available with this Gmail address. Please create an account first.';
            }
            return;
          }
          if (identifierStatus) {
            identifierStatus.textContent = '';
            identifierStatus.style.display = 'none';
          }
        } catch (error) {
          if (identifierStatus) {
            identifierStatus.style.color = '#b42318';
            identifierStatus.textContent = error.message || 'Unable to check this Gmail address. Please try again.';
          }
          return;
        } finally {
          nextBtn.disabled = false;
        }
      }

      identifier.required = false;
      password.required = true;
      password.setAttribute('aria-hidden', 'false');
      emailStep.style.display = 'none';
      emailActions.style.display = 'none';
      passwordStep.style.display = 'block';
      passwordActions.style.display = 'block';
      password.focus();
    });

    if (loginSubmitBtn) {
      loginSubmitBtn.addEventListener('click', function (event) {
        if (!password.value.trim()) {
          event.preventDefault();
          password.setCustomValidity('Please fill in your password to log in.');
          password.reportValidity();
          password.focus();
          return;
        }

        loginForm.submit();
      });
    }

    const loginButtons = document.querySelectorAll('#nextToPasswordBtn, #loginSubmitBtn');
    loginButtons.forEach(function (button) {
      button.addEventListener('mouseenter', function () {
        button.style.transform = 'translateY(-1px)';
        button.style.filter = 'brightness(1.03)';
        button.style.boxShadow = '0 18px 28px rgba(37, 99, 235, 0.32)';
      });

      button.addEventListener('mouseleave', function () {
        button.style.transform = 'translateY(0)';
        button.style.filter = 'none';
        button.style.boxShadow = '0 14px 24px rgba(37, 99, 235, 0.25)';
      });
    });

    const googleButton = document.querySelector('a[href="?url=auth/google"]');
    if (googleButton) {
      googleButton.addEventListener('mouseenter', function () {
        googleButton.style.transform = 'translateY(-1px)';
        googleButton.style.boxShadow = '0 12px 22px rgba(15, 23, 42, 0.08)';
        googleButton.style.borderColor = '#c7d2fe';
      });

      googleButton.addEventListener('mouseleave', function () {
        googleButton.style.transform = 'translateY(0)';
        googleButton.style.boxShadow = '0 8px 18px rgba(15, 23, 42, 0.04)';
        googleButton.style.borderColor = '#d0d5dd';
      });
    }
  });
</script>
