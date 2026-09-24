// Main JS for LibAI
console.log('LibAI scaffold loaded');

// Collapse Bootstrap navbar on mobile after clicking a nav link
document.addEventListener('DOMContentLoaded', function(){
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
