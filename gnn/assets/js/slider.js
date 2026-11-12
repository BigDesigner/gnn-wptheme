/** GNN hero slider with keyboard-safe slides. */
(function () {
	'use strict';

	function initSliders(){
	document.querySelectorAll('.gnn-hero').forEach(function(hero){
	if(hero.dataset.gnnSliderReady)return;hero.dataset.gnnSliderReady='1';

	var track = hero.querySelector('.gnn-hero__track');
	var slides = hero.querySelectorAll('.gnn-hero__slide');
	var dots = hero.querySelectorAll('.gnn-hero__dot');
	var prev = hero.querySelector('.gnn-hero__arrow--prev');
	var next = hero.querySelector('.gnn-hero__arrow--next');
	var controls = hero.querySelector('.gnn-hero__controls');
	var count = slides.length;
	var index = 0;
	var timer = null;
	var settings = hero.hasAttribute('data-autoplay') ? {autoplay:hero.dataset.autoplay==='1',interval:Number(hero.dataset.interval)||5000} : (window.gnnSlider || { autoplay: true, interval: 5000 });
	var reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
	var enabled = !!settings.autoplay && !reduced.matches;
	var hovered = false;
	var focused = false;

	/* Respect reduced-motion: no autoplay, arrows/dots still work. */
	if (reduced.matches) {
		settings = { autoplay: false, interval: settings.interval };
	}

	if (count < 2) { return; }
	if (controls) { controls.hidden = false; }

	function render() {
		var direction = getComputedStyle(track).direction === 'rtl' ? 1 : -1;
		track.style.transform = 'translateX(' + (direction * index * 100) + '%)';
		slides.forEach(function (slide, i) {
			var active = i === index;
			slide.classList.toggle('is-active', active);
			slide.inert = !active;
			slide.setAttribute('aria-hidden', active ? 'false' : 'true');
			// Also protect focus order in browsers without inert support.
			slide.querySelectorAll('a, button, input, select, textarea, [tabindex]').forEach(function (el) {
				if (!active && !el.hasAttribute('data-gnn-tabindex')) {
					el.setAttribute('data-gnn-tabindex', el.getAttribute('tabindex') || '');
					el.setAttribute('tabindex', '-1');
				} else if (active && el.hasAttribute('data-gnn-tabindex')) {
					var original = el.getAttribute('data-gnn-tabindex');
					if (original === '') { el.removeAttribute('tabindex'); }
					else { el.setAttribute('tabindex', original); }
					el.removeAttribute('data-gnn-tabindex');
				}
			});
		});
		dots.forEach(function (dot, i) {
			dot.classList.toggle('is-active', i === index);
			if (i === index) { dot.setAttribute('aria-current', 'true'); }
			else { dot.removeAttribute('aria-current'); }
		});
	}

	function go(to) {
		index = (to + count) % count;
		render();
	}

	function startAutoplay() {
		if (!enabled || hovered || focused || document.hidden || reduced.matches || timer) { return; }
		timer = setInterval(function () { go(index + 1); }, settings.interval || 5000);
	}

	function stopAutoplay() {
		if (timer) { clearInterval(timer); timer = null; }
	}
	function updateRotation() {
		stopAutoplay();
		track.setAttribute('aria-live', enabled ? 'off' : 'polite');
		startAutoplay();
	}

	if (prev) { prev.addEventListener('click', function () { go(index - 1); }); }
	if (next) { next.addEventListener('click', function () { go(index + 1); }); }
	dots.forEach(function (dot, i) {
		dot.addEventListener('click', function () { go(i); });
	});

	hero.addEventListener('focusin', function () { focused = true; updateRotation(); });
	hero.addEventListener('focusout', function () {
		window.setTimeout(function () {
			focused = hero.contains(document.activeElement);
			updateRotation();
		}, 0);
	});
	hero.addEventListener('mouseenter', function () { hovered = true; updateRotation(); });
	hero.addEventListener('mouseleave', function () { hovered = false; updateRotation(); });
	document.addEventListener('visibilitychange', updateRotation);
	function motionChanged() {
		if (reduced.matches) { enabled = false; }
		updateRotation();
	}
	if (reduced.addEventListener) { reduced.addEventListener('change', motionChanged); }
	else if (reduced.addListener) { reduced.addListener(motionChanged); }

	render();
	updateRotation();
	});
	}
	initSliders();
	function register(){if(window.elementorFrontend?.hooks)window.elementorFrontend.hooks.addAction('frontend/element_ready/gnn-slider.default',initSliders);}
	if(window.elementorFrontend?.hooks)register();else if(window.jQuery)window.jQuery(window).on('elementor/frontend/init',register);
})();
