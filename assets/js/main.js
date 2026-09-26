// Main JS for LibAI
console.log('LibAI scaffold loaded');

// Collapse Bootstrap navbar on mobile after clicking a nav link
document.addEventListener('DOMContentLoaded', function(){
	var passwordInput = document.getElementById('password');
	var passwordToggle = document.getElementById('togglePassword');
	if (passwordInput && passwordToggle && passwordToggle.getAttribute('data-libai-custom-toggle') !== '1') {
		passwordToggle.addEventListener('click', function(){
			var isHidden = passwordInput.type === 'password';
			passwordInput.type = isHidden ? 'text' : 'password';
			passwordToggle.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
			passwordToggle.setAttribute('title', isHidden ? 'Hide password' : 'Show password');
			passwordToggle.innerHTML = '<img src="/LIBAI/password%20icon/' + (isHidden ? 'hide.png' : 'view.png') + '" alt="' + (isHidden ? 'Hide password' : 'Show password') + '" style="width: 20px; height: 20px; display: block;">';
		});
	}

	document.querySelectorAll('input[type="password"]').forEach(function(input){
		if (input === passwordInput || input.closest('.password-field-wrap')) {
			return;
		}

		var wrapper = document.createElement('div');
		wrapper.className = 'password-field-wrap';
		input.parentNode.insertBefore(wrapper, input);
		wrapper.appendChild(input);

		var toggleButton = document.createElement('button');
		toggleButton.type = 'button';
		toggleButton.className = 'password-field-toggle';
		toggleButton.setAttribute('aria-label', 'Show password');
		toggleButton.setAttribute('title', 'Show password');
		toggleButton.innerHTML = '<img src="/LIBAI/password%20icon/view.png" alt="Show password" style="width: 18px; height: 18px; display: block;">';
		wrapper.appendChild(toggleButton);

		toggleButton.addEventListener('click', function(){
			var isHidden = input.type === 'password';
			input.type = isHidden ? 'text' : 'password';
			toggleButton.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
			toggleButton.setAttribute('title', isHidden ? 'Hide password' : 'Show password');
			toggleButton.innerHTML = '<img src="/LIBAI/password%20icon/' + (isHidden ? 'hide.png' : 'view.png') + '" alt="' + (isHidden ? 'Hide password' : 'Show password') + '" style="width: 18px; height: 18px; display: block;">';
		});
	});

	var resetCodeInput = document.getElementById('code');
	var resetPasswordFields = document.getElementById('resetPasswordFields');
	var resetPasswordInput = document.getElementById('password');
	if (resetCodeInput && resetPasswordFields && resetPasswordInput) {
		resetCodeInput.addEventListener('input', function(){
			resetCodeInput.value = resetCodeInput.value.replace(/\D/g, '').slice(0, 6);
			if (resetCodeInput.value.length === 6) {
				resetPasswordFields.hidden = false;
				resetPasswordInput.focus();
				resetPasswordFields.scrollIntoView({ behavior: 'smooth', block: 'center' });
			}
		});
	}

	var nav = document.querySelector('.navbar-collapse');
	if (nav) {
		nav.querySelectorAll('a.nav-link').forEach(function(link){
			link.addEventListener('click', function(){
				var toggler = document.querySelector('.navbar-toggler');
				if (toggler && window.getComputedStyle(toggler).display !== 'none') {
					var bs = bootstrap.Collapse.getInstance(nav);
					if (bs) bs.hide();
				}
			});
		});
	}

	var sidebar = document.querySelector('.app-sidebar');
	var toggle = document.querySelector('.sidebar-toggle');
	var overlay = document.querySelector('.sidebar-overlay');

	function closeSidebar(){
		if (sidebar) {
			sidebar.classList.remove('open');
		}
		if (overlay) {
			overlay.classList.remove('show');
		}
		document.body.classList.remove('menu-open');
	}

	if (toggle && sidebar) {
		toggle.addEventListener('click', function(){
			sidebar.classList.toggle('open');
			if (overlay) {
				overlay.classList.toggle('show');
			}
			document.body.classList.toggle('menu-open', sidebar.classList.contains('open'));
		});
	}

	if (overlay) {
		overlay.addEventListener('click', closeSidebar);
	}

	if (sidebar) {
		sidebar.querySelectorAll('.sidebar-link').forEach(function(link){
			link.addEventListener('click', closeSidebar);
		});
	}

	window.addEventListener('resize', function(){
		if (window.innerWidth > 1100) {
			closeSidebar();
		}
	});
});
