/* Account for real wrapping, font loading, top bars and custom header regions. */
(function () {
	'use strict';
	var header = document.querySelector('.site-header') || document.querySelector('.gnn-layout-region--header');
	var topbar = document.querySelector('.gnn-topbar');
	var admin = document.getElementById('wpadminbar');
	function measure() {
		var height = 0;
		[header, topbar, admin].forEach(function (element) { if (element) height += element.getBoundingClientRect().height; });
		document.documentElement.style.setProperty('--gnn-header-occupied', Math.ceil(height) + 'px');
	}
	if (window.ResizeObserver) {
		var observer = new ResizeObserver(measure);
		[header, topbar, admin].forEach(function (element) { if (element) observer.observe(element); });
	}
	window.addEventListener('resize', measure, {passive: true});
	measure();
}());
