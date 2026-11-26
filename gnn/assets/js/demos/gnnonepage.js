/**
 * GNNonepage progressive enhancement: active-section highlighting in the
 * anchor menu using IntersectionObserver, and closing the mobile drawer
 * when an in-page anchor is chosen.
 */
(function () {
	'use strict';

	if (!document.body || !document.body.classList.contains('gnn-site-gnnonepage')) {
		return;
	}

	var stage = document.querySelector('.gnn-gnnonepage-hero-stage');
	if (stage && !document.body.classList.contains('elementor-editor-active') && !document.body.classList.contains('elementor-editor-preview')) {
		var turntable = document.createElement('div');
		turntable.className = 'gnn-onepage-turntable';
		turntable.setAttribute('aria-hidden', 'true');
		var product = document.createElement('div');
		product.className = 'gnn-onepage-product';
		for (var depth = -10; depth <= 10; depth += 1) {
			var layer = document.createElement('span');
			layer.style.setProperty('--product-depth', depth + 'px');
			product.appendChild(layer);
		}
		turntable.appendChild(product);
		stage.appendChild(turntable);
		stage.classList.add('has-product-turn');

		var frame = 0;
		function drawProduct() {
			frame = 0;
			var bounds = stage.getBoundingClientRect();
			var progress = Math.max(0, Math.min(1, (window.innerHeight * 0.75 - bounds.top) / (bounds.height * 0.85)));
			product.style.setProperty('--product-angle', (-24 + progress * 360) + 'deg');
			product.style.setProperty('--product-drift', (24 - progress * 48) + 'px');
		}
		function scheduleProduct() {
			if (!frame) {
				frame = window.requestAnimationFrame(drawProduct);
			}
		}
		window.addEventListener('scroll', scheduleProduct, { passive: true });
		window.addEventListener('resize', scheduleProduct);
		scheduleProduct();
	}

	var navLinks = Array.prototype.slice.call(
		document.querySelectorAll('[data-gnn-header] .main-navigation a[href*="#"], [data-gnn-header] .mobile-navigation a[href*="#"]')
	);

	if (!navLinks.length) {
		return;
	}

	/* Map section IDs to their matching anchor links. */
	var sectionMap = Object.create(null);
	var sections = [];

	navLinks.forEach(function (link) {
		var href = link.getAttribute('href') || '';
		var hashIndex = href.indexOf('#');
		if (hashIndex === -1) {
			return;
		}
		var id = href.slice(hashIndex + 1);
		if (!id) {
			return;
		}
		var target = document.getElementById(id);
		if (!target) {
			return;
		}
		if (!sectionMap[id]) {
			sectionMap[id] = [];
			sections.push(target);
		}
		sectionMap[id].push(link);
	});

	function setActive(activeId) {
		Object.keys(sectionMap).forEach(function (id) {
			var isCurrent = id === activeId;
			sectionMap[id].forEach(function (link) {
				link.classList.toggle('is-active', isCurrent);
				if (isCurrent) {
					link.setAttribute('aria-current', 'location');
				} else {
					link.removeAttribute('aria-current');
				}
			});
		});
	}

	function getHeaderOffset() {
		var rootStyle = window.getComputedStyle(document.documentElement);
		var scrollPad = parseFloat(rootStyle.scrollPaddingTop);
		if (!isNaN(scrollPad) && scrollPad > 0) {
			return Math.round(scrollPad);
		}
		var headerH = parseFloat(rootStyle.getPropertyValue('--gnn-header-h')) || 0;
		var topbarH = parseFloat(rootStyle.getPropertyValue('--gnn-topbar-h')) || 0;
		var adminH = parseFloat(rootStyle.getPropertyValue('--gnn-admin-h')) || 0;
		return Math.round(headerH + topbarH + adminH) || 96;
	}

	if (sections.length && 'IntersectionObserver' in window) {
		var currentId = sections[0].id;
		var observer = null;
		setActive(currentId);

		function observeSections() {
			if (observer) {
				observer.disconnect();
			}
			var topMargin = -(getHeaderOffset() + 24);
			observer = new IntersectionObserver(
				function (entries) {
					entries.forEach(function (entry) {
						if (entry.isIntersecting) {
							currentId = entry.target.id;
							setActive(currentId);
						}
					});
				},
				{
					rootMargin: topMargin + 'px 0px -56% 0px',
					threshold: 0,
				}
			);
			sections.forEach(function (section) {
				observer.observe(section);
			});
		}

		observeSections();
		document.addEventListener('gnn:header-layout', observeSections);
	}
})();
