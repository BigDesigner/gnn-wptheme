/* Header disclosure controls, responsive fit, search and scroll behavior. */
(function () {
	'use strict';

	var activePanel = null;
	var activeSearch = null;
	var instances = [];
	var shell = document.querySelector('[data-gnn-header-shell]');
	var root = document.documentElement;

	function emitLayout() {
		document.dispatchEvent(new Event('gnn:header-layout'));
	}

	function directChild(container, className) {
		return Array.from(container.children).find(function (child) { return child.classList.contains(className); });
	}

	function getControls(container) {
		return Array.from(container.querySelectorAll(
			'a[href], button:not([disabled]), input:not([type="hidden"]):not([disabled]), select:not([disabled]), [tabindex="0"]'
		)).filter(function (element) {
			return element.getClientRects().length > 0;
		});
	}

	function interactionState() {
		if (shell) {
			var engaged = Boolean(activePanel || activeSearch || shell.querySelector('[data-gnn-header] .is-open'));
			shell.classList.toggle('is-interacting', engaged);
			shell.classList.toggle('is-panel-open', Boolean(activePanel && shell.contains(activePanel.root)));
		}
	}

	function closePanel(restoreFocus) {
		if (!activePanel) { return; }
		var instance = activePanel;
		activePanel = null;
		instance.toggle.setAttribute('aria-expanded', 'false');
		instance.panel.hidden = true;
		instance.backdrop.hidden = true;
		instance.closeBranches(instance.panel);
		document.body.classList.remove('mobile-nav-open');
		root.classList.remove('mobile-nav-open');
		interactionState();
		if (restoreFocus) {
			var target = instance.toggle.getClientRects().length ? instance.toggle : getControls(instance.root)[0];
			if (target) { target.focus({preventScroll: true}); }
		}
	}

	function openPanel(instance) {
		if (activePanel === instance) {
			closePanel(true);
			return;
		}
		closePanel(false);
		instances.forEach(function (item) { item.closeBranches(item.root); });
		activePanel = instance;
		instance.toggle.setAttribute('aria-expanded', 'true');
		instance.panel.hidden = false;
		instance.backdrop.hidden = false;
		document.body.classList.add('mobile-nav-open');
		root.classList.add('mobile-nav-open');
		interactionState();
		emitLayout();
		(getControls(instance.panel)[0] || instance.panel).focus({preventScroll: true});
	}

	function positionDropdown(button) {
		var item = button.parentElement;
		var panel = document.getElementById(button.getAttribute('aria-controls'));
		if (!panel || panel.classList.contains('mega-menu') || item.closest('.mobile-navigation')) { return; }
		var limit = root.clientWidth - 8;
		function overflow(rect) {
			return Math.max(0, 8 - rect.left) + Math.max(0, rect.right - limit);
		}
		panel.style.removeProperty('--gnn-dropdown-shift');
		item.classList.remove('gnn-flip');
		var bounds = panel.getBoundingClientRect();
		if (!overflow(bounds)) { return; }
		item.classList.add('gnn-flip');
		var flipped = panel.getBoundingClientRect();
		if (overflow(flipped) >= overflow(bounds)) {
			item.classList.remove('gnn-flip');
		} else {
			bounds = flipped;
		}
		var adjustment = Math.max(0, 8 - bounds.left) - Math.max(0, bounds.right - limit);
		var rtl = getComputedStyle(panel).direction === 'rtl';
		panel.style.setProperty('--gnn-dropdown-shift', (rtl ? -adjustment : adjustment) + 'px');
	}

	function setBranch(button, open) {
		button.setAttribute('aria-expanded', String(open));
		button.parentElement.classList.toggle('is-open', open);
		if (open) { positionDropdown(button); }
	}

	function menuLists(container) {
		var menus = [];
		container.querySelectorAll('.main-navigation > ul.menu').forEach(function (list) {
			var more = directChild(list, 'gnn-menu-more');
			if (!more) { return; }
			menus.push({
				list: list,
				items: Array.from(list.children).filter(function (item) { return item !== more; }),
				more: more,
				overflow: more.querySelector('.sub-menu')
			});
		});
		return menus;
	}

	function restoreItems(menu) {
		menu.items.forEach(function (item) {
			if (item.parentElement === menu.overflow) {
				menu.list.insertBefore(item, menu.more);
			}
		});
		menu.more.hidden = true;
	}

	function fitMenu(menu, mode) {
		restoreItems(menu);
		var gap = parseFloat(getComputedStyle(menu.list).columnGap) || 0;
		var widths = menu.items.map(function (item) { return item.getBoundingClientRect().width; });
		var available = menu.list.clientWidth;
		var width = widths.reduce(function (sum, item) { return sum + item; }, 0) + gap * Math.max(0, widths.length - 1);
		if (width <= available + 1) { return true; }
		if (mode === 'collapse') { return false; }

		menu.more.hidden = false;
		var moreWidth = menu.more.getBoundingClientRect().width;
		if (moreWidth > available + 1) { return false; }
		for (var i = menu.items.length - 1; i >= 0 && width + gap + moreWidth > available + 1; i--) {
			width -= widths[i] + gap;
			menu.overflow.insertBefore(menu.items[i], menu.overflow.firstChild);
		}
		return true;
	}

	function initHeader(container) {
		var toggle = container.querySelector('.menu-toggle');
		var panel = document.getElementById(toggle.getAttribute('aria-controls'));
		var backdrop = container.querySelector('.gnn-header-backdrop');
		var mobileMax = (Number(container.dataset.breakpoint) - 0.02).toFixed(2);
		var breakpoint = window.matchMedia('(max-width: ' + mobileMax + 'px)');
		var tabletQuery = window.matchMedia('(min-width: 601px)');
		var lists = menuLists(container);
		var row = container.querySelector('.gnn-header-row');
		var brand = row && row.querySelector('.site-branding');
		var actions = container.querySelector('.gnn-header-row .header-actions');
		var right = container.querySelector('.gnn-header-right-group');
		var instance = {root: container, toggle: toggle, panel: panel, backdrop: backdrop, closeBranches: closeBranches};
		var fitQueued = false;
		var previousWidth = -1;
		var layoutChanged = true;
		var hovered = new WeakSet();
		var timers = new WeakMap();

		function closeBranches(scope) {
			scope.querySelectorAll('.gnn-submenu-toggle[aria-expanded="true"]').forEach(function (button) {
				hovered.delete(button);
				setBranch(button, false);
			});
		}

		function closeSiblings(item) {
			Array.from(item.parentElement.children).forEach(function (sibling) {
				if (sibling !== item) { closeBranches(sibling); }
			});
		}

		function fit() {
			fitQueued = false;
			var width = container.getBoundingClientRect().width;
			if (!layoutChanged && Math.abs(width - previousWidth) < 0.5) { return; }
			layoutChanged = false;
			previousWidth = width;
			var focused = document.activeElement;
			var focusInMenu = lists.some(function (menu) { return menu.list.contains(focused); });
			var wasCollapsed = container.classList.contains('is-collapsed');
			container.classList.remove('is-collapsed', 'is-tablet-cta');
			var fits = true;
			if (!breakpoint.matches) {
				closeBranches(container);
				lists.forEach(function (menu) {
					if (!fitMenu(menu, container.dataset.overflow)) { fits = false; }
				});
				[row, right, actions].forEach(function (element) {
					if (element && element.scrollWidth > element.clientWidth + 1) { fits = false; }
				});
			}
			container.classList.toggle('is-collapsed', !fits);
			if ((breakpoint.matches || !fits) && tabletQuery.matches && row && actions && actions.querySelector('.gnn-header-action--cta .header-cta') && panel.querySelector('.gnn-header-action--cta .header-cta')) {
				container.classList.add('is-tablet-cta');
				var ctaFits = [row, right, actions].every(function (element) {
					return !element || element.scrollWidth <= element.clientWidth + 1;
				});
				if (brand && brand.firstElementChild && brand.getBoundingClientRect().width < Math.min(80, brand.firstElementChild.scrollWidth)) { ctaFits = false; }
				if (!ctaFits) { container.classList.remove('is-tablet-cta'); }
			}
			if (activePanel === instance && !breakpoint.matches && fits) { closePanel(true); }
			if (wasCollapsed !== !fits) { emitLayout(); }
			if (focusInMenu && (document.activeElement !== focused || !fits)) {
				var more = focused.closest('.gnn-menu-more');
				var next = !fits ? toggle : (more ? more.querySelector('.gnn-submenu-toggle') : focused);
				next.focus({preventScroll: true});
			}
			interactionState();
		}

		function scheduleFit(event) {
			if (event) { layoutChanged = true; }
			if (!fitQueued) {
				fitQueued = true;
				window.requestAnimationFrame(fit);
			}
		}

		toggle.addEventListener('click', function () { openPanel(instance); });
		panel.querySelector('[data-gnn-menu-close]').addEventListener('click', function () { closePanel(true); });
		function onBreakpointChange() {
			if (activePanel === instance) { closePanel(true); }
			scheduleFit(true);
		}

		if (breakpoint.addEventListener) {
			breakpoint.addEventListener('change', onBreakpointChange);
		} else {
			breakpoint.addListener(onBreakpointChange);
		}
		if (tabletQuery.addEventListener) {
			tabletQuery.addEventListener('change', scheduleFit);
		} else {
			tabletQuery.addListener(scheduleFit);
		}
		container.addEventListener('click', function (event) {
			var button = event.target.closest('.gnn-submenu-toggle');
			if (button && button.closest('[data-gnn-header]') === container) {
				var item = button.parentElement;
				var open = button.getAttribute('aria-expanded') === 'false' || hovered.has(button);
				closeSiblings(item);
				hovered.delete(button);
				if (!open) { closeBranches(item); }
				setBranch(button, open);
				interactionState();
				return;
			}
			var link = event.target.closest('a[href]');
			if (!link) { return; }
			if (panel.contains(link) && !link.hasAttribute('data-gnn-search')) { closePanel(false); }
			var nav = link.closest('.main-navigation');
			if (nav) {
				closeBranches(nav);
				interactionState();
			}
		});

		container.querySelectorAll('.main-navigation .gnn-submenu-toggle').forEach(function (button) {
			var item = button.parentElement;
			item.addEventListener('pointerenter', function (event) {
				if (event.pointerType !== 'mouse' || activePanel || breakpoint.matches || container.classList.contains('is-collapsed')) { return; }
				window.clearTimeout(timers.get(button));
				if (button.getAttribute('aria-expanded') !== 'true') {
					closeSiblings(item);
					hovered.add(button);
					setBranch(button, true);
					interactionState();
				}
			});
			item.addEventListener('pointerleave', function (event) {
				if (event.pointerType !== 'mouse' || !hovered.has(button)) { return; }
				timers.set(button, window.setTimeout(function () {
					if (hovered.has(button) && !item.contains(document.activeElement)) {
						closeBranches(item);
						interactionState();
					}
				}, 220));
			});
		});
		container.addEventListener('focusout', function (event) {
			var nav = event.target.closest('.main-navigation');
			if (nav && event.relatedTarget && !nav.contains(event.relatedTarget)) {
				closeBranches(nav);
				interactionState();
			}
		});
		if (window.ResizeObserver) {
			var observer = new ResizeObserver(function () { scheduleFit(false); });
			observer.observe(container);
		}
		window.addEventListener('resize', scheduleFit, {passive: true});
		if (document.fonts) {
			document.fonts.ready.then(function () { scheduleFit(true); });
			if (document.fonts.addEventListener) { document.fonts.addEventListener('loadingdone', scheduleFit); }
		}
		container.querySelectorAll('.site-logo img').forEach(function (image) {
			image.addEventListener('load', scheduleFit);
		});
		panel.hidden = true;
		container.dataset.ready = '';
		fit();
		return instance;
	}

	function setUpSearch(link, dialog) {
		if (!dialog || !dialog.showModal) { return; }
		link.setAttribute('aria-haspopup', 'dialog');
		link.setAttribute('aria-controls', dialog.id);
		link.addEventListener('click', function (event) {
			if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey || dialog.open) { return; }
			event.preventDefault();
			var returnTo = activePanel && activePanel.panel.contains(link) ? activePanel.toggle : link;
			closePanel(false);
			activeSearch = dialog;
			interactionState();
			dialog.showModal();
			dialog.addEventListener('close', function () {
				activeSearch = null;
				interactionState();
				if (returnTo.getClientRects().length) { returnTo.focus({preventScroll: true}); }
			}, {once: true});
		});
	}

	document.querySelectorAll('[data-gnn-header]').forEach(function (container) {
		instances.push(initHeader(container));
	});
	document.querySelectorAll('[data-gnn-search]').forEach(function (link) {
		setUpSearch(link, document.getElementById(link.dataset.gnnSearch));
	});
	document.querySelectorAll('[data-gnn-dock-search]').forEach(function (link) {
		setUpSearch(link, document.querySelector('[data-gnn-header-shell] .gnn-search-dialog'));
	});
	document.querySelectorAll('.gnn-search-dialog').forEach(function (dialog) {
		dialog.addEventListener('click', function (event) {
			if (event.target === dialog) {
				var bounds = dialog.getBoundingClientRect();
				if (event.clientX < bounds.left || event.clientX > bounds.right || event.clientY < bounds.top || event.clientY > bounds.bottom) {
					dialog.close();
				}
			}
		});
	});

	document.addEventListener('click', function (event) {
		if (activePanel && !activePanel.panel.contains(event.target) && !activePanel.toggle.contains(event.target)) {
			closePanel(true);
		}
		instances.forEach(function (instance) {
			if (!event.target.closest('.main-navigation') || !instance.root.contains(event.target)) {
				instance.root.querySelectorAll('.main-navigation').forEach(instance.closeBranches);
			}
		});
		interactionState();
	});

	document.addEventListener('keydown', function (event) {
		if (activeSearch) { return; }
		if (activePanel) {
			if (event.key === 'Escape') {
				event.preventDefault();
				closePanel(true);
			} else if (event.key === 'Tab') {
				var items = getControls(activePanel.panel);
				var next = event.shiftKey ? items[items.length - 1] : items[0];
				var edge = event.shiftKey ? items[0] : items[items.length - 1];
				if (!activePanel.panel.contains(document.activeElement) || document.activeElement === edge || !items.length) {
					event.preventDefault();
					(next || activePanel.panel).focus();
				}
			}
			return;
		}
		if (event.key !== 'Escape') { return; }
		var branch = document.activeElement.closest('.main-navigation .is-open');
		var button = branch && directChild(branch, 'gnn-submenu-toggle');
		if (button) {
			event.preventDefault();
			var instance = instances.find(function (item) { return item.root.contains(branch); });
			instance.closeBranches(branch);
			button.focus({preventScroll: true});
			interactionState();
		} else {
			instances.forEach(function (item) { item.closeBranches(item.root); });
			interactionState();
		}
	}, true);

	if (shell) {
		shell.dataset.ready = '';
		var masthead = shell.querySelector('#masthead');
		var hero = shell.nextElementSibling;
		var heroSelector = '.gnn-hero, .gnn-revslider, .gnn-header-overlay-target';
		var floatingHeroSelector = '.gnn-dz-hero, .gnn-gnnonepage-hero';
		var wrappers = 'main, .gnn-page, article.page, .entry-content, .elementor, .wp-site-blocks';
		while (hero && hero.matches(wrappers) && !hero.matches(heroSelector)) {
			hero = Array.from(hero.children).find(function (child) {
				return !child.matches('style, script, link, template');
			});
		}
		var hasHero = Boolean(hero && hero.matches(heroSelector));
		if (shell.dataset.overlay === '1' && hasHero && masthead) {
			shell.dataset.overlayActive = '';
		}
		if (masthead && masthead.dataset.shape === 'floating' && shell.hasAttribute('data-overlay-inherited') && (hasHero || (hero && hero.matches(floatingHeroSelector)))) {
			shell.dataset.floatingOverlay = '';
		}
		var previousY = Math.max(0, window.scrollY);
		var anchorY = previousY;
		var behavior = shell.dataset.behavior;
		var scrollQueued = false;
		function updateScroll() {
			scrollQueued = false;
			var y = Math.max(0, window.scrollY);
			shell.classList.toggle('is-scrolled', y > 12);
			if (behavior === 'shrink') {
				var threshold = shell.classList.contains('is-shrunk') ? 48 : 100;
				shell.classList.toggle('is-shrunk', y >= threshold && !activePanel);
			}
			if (behavior === 'hide') {
				if (y < shell.offsetHeight || y < previousY && anchorY - y > 6) {
					shell.classList.remove('is-hidden');
					anchorY = y;
				} else if (y > previousY && y - anchorY > 6) {
					shell.classList.add('is-hidden');
					anchorY = y;
				}
			}
			previousY = y;
		}
		window.addEventListener('scroll', function () {
			if (!scrollQueued) {
				scrollQueued = true;
				window.requestAnimationFrame(updateScroll);
			}
		}, {passive: true});
		updateScroll();
	}
	emitLayout();
}());
