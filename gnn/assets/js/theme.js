/**
 * GNN theme runtime: dark/light toggle + mobile navigation.
 * (The initial theme mode is applied by an inline script in <head> to avoid FOUC.)
 */
(function () {
	'use strict';

	/* Dark / light toggle — persists in localStorage, mirrors the mockup. */
	document.querySelectorAll('.theme-toggle').forEach(function (button) {
		button.addEventListener('click', function () {
			var root = document.documentElement;
			var next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
			root.setAttribute('data-theme', next);
			/* Persist only when the panel's "remember" switch is on. */
			if (window.gnnThemeRemember !== false) {
				try { localStorage.setItem('gnn-theme', next); } catch (e) {}
			}
		});
	});

	/* Mobile drawer — hamburger toggles .toggled on .main-navigation (breakpoint 900px). */
	var toggle = document.querySelector('.menu-toggle');
	var mainNav = document.querySelector('.main-navigation');
	var mobileNav = document.getElementById('mobile-navigation');

	if (toggle && mobileNav) {
		toggle.addEventListener('click', function () {
			var open = toggle.getAttribute('aria-expanded') === 'true';
			toggle.setAttribute('aria-expanded', open ? 'false' : 'true');
			mobileNav.hidden = open;
			if (mainNav) { mainNav.classList.toggle('toggled', !open); }
			document.body.classList.toggle('mobile-nav-open', !open);
		});

		/* Close the drawer when resizing up past the breakpoint. */
		window.addEventListener('resize', function () {
			if (window.innerWidth >= 900 && !mobileNav.hidden) {
				toggle.setAttribute('aria-expanded', 'false');
				mobileNav.hidden = true;
				if (mainNav) { mainNav.classList.remove('toggled'); }
				document.body.classList.remove('mobile-nav-open');
			}
		});
	}

	/* Keyboard support for dropdowns: focus keeps the sub-menu open (CSS
	   :focus-within); aria-expanded mirrors that state for screen readers;
	   Escape closes by moving focus back to the parent link. */
	document.querySelectorAll('.main-navigation .menu-item-has-children').forEach(function (item) {
		var link = item.querySelector(':scope > a');
		if (!link) { return; }
		link.setAttribute('aria-haspopup', 'true');
		link.setAttribute('aria-expanded', 'false');

		item.addEventListener('focusin', function () { link.setAttribute('aria-expanded', 'true'); });
		item.addEventListener('focusout', function () {
			/* Delay: focusout fires before the next element receives focus. */
			setTimeout(function () {
				if (!item.contains(document.activeElement)) {
					link.setAttribute('aria-expanded', 'false');
				}
			}, 0);
		});
		item.addEventListener('keydown', function (event) {
			if (event.key === 'Escape') {
				/* Close the sub-menu but KEEP focus on its parent link — a
				   blur() here would drop the user out of the navigation
				   entirely. .is-closed suppresses the CSS :focus-within
				   rule that would otherwise re-open it; it clears as soon
				   as focus leaves the item. */
				link.setAttribute('aria-expanded', 'false');
				item.classList.add('is-closed');
				link.focus();
			}
		});
		item.addEventListener('focusout', function () {
			setTimeout(function () {
				if (!item.contains(document.activeElement)) {
					item.classList.remove('is-closed');
				}
			}, 0);
		});
		item.addEventListener('mouseleave', function () { item.classList.remove('is-closed'); });
	});
})();
