/* Measure the shell once for hero, navigation, admin toolbar and anchor offsets. */
(function () {
	'use strict';

	var root = document.documentElement;
	var shell = document.querySelector('[data-gnn-header-shell]');
	var topbar = shell && Array.from(shell.children).find(function (child) {
		return child.classList.contains('gnn-topbar');
	});
	var admin = document.getElementById('wpadminbar');
	var controls = Array.from(document.querySelectorAll('[data-gnn-header]'));
	var queued = false;

	function measure() {
		queued = false;
		var adminBox = admin && admin.getBoundingClientRect();
		root.style.setProperty('--gnn-admin-h', (adminBox ? Math.ceil(adminBox.height) : 0) + 'px');
		root.style.setProperty('--gnn-admin-inset', (adminBox ? Math.max(0, Math.ceil(adminBox.bottom)) : 0) + 'px');
		var topbarHeight = topbar ? Math.ceil(topbar.getBoundingClientRect().height) : 0;
		var shellHeight = shell ? Math.ceil(shell.getBoundingClientRect().height) : 0;
		root.style.setProperty('--gnn-topbar-h', topbarHeight + 'px');
		root.style.setProperty('--gnn-header-h', Math.max(0, shellHeight - topbarHeight) + 'px');

		controls.forEach(function (control) {
			var bottom = Math.max(0, Math.ceil(control.getBoundingClientRect().bottom));
			control.style.setProperty('--gnn-header-visible-bottom', bottom + 'px');
		});
	}

	function requestMeasure() {
		if (!queued) {
			queued = true;
			window.requestAnimationFrame(measure);
		}
	}

	if (window.ResizeObserver) {
		var observer = new ResizeObserver(requestMeasure);
		[shell, topbar, admin].concat(controls).forEach(function (element) {
			if (element) { observer.observe(element); }
		});
	}
	if (shell) { shell.addEventListener('transitionend', requestMeasure); }
	document.addEventListener('gnn:header-layout', requestMeasure);
	window.addEventListener('resize', requestMeasure, {passive: true});
	window.addEventListener('scroll', requestMeasure, {passive: true});
	measure();
}());
