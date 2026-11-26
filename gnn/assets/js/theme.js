/**
 * GNN theme runtime: dark/light toggle.
 * (The initial theme mode is applied by an inline script in <head> to avoid FOUC.)
 */
(function () {
	'use strict';

	/* Dark / light toggle — persists in localStorage, mirrors the mockup.
	   aria-pressed="true" means dark mode is on (header and dock buttons). */
	var themeToggles = document.querySelectorAll('.theme-toggle');
	function syncThemeToggles() {
		var dark = document.documentElement.getAttribute('data-theme') !== 'light';
		themeToggles.forEach(function (toggleButton) {
			toggleButton.setAttribute('aria-pressed', dark ? 'true' : 'false');
		});
	}
	syncThemeToggles();
	themeToggles.forEach(function (button) {
		button.addEventListener('click', function () {
			var root = document.documentElement;
			var next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
			root.setAttribute('data-theme', next);
			syncThemeToggles();
			/* Persist only when the panel's "remember" switch is on. */
			if (window.gnnThemeRemember !== false) {
				try { localStorage.setItem('gnn-theme', next); } catch (e) {}
			}
		});
	});
})();
