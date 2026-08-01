/**
 * GNN hero slider: translateX track, wrapping arrows, dot nav,
 * autoplay (5s, Customizer-controlled), pause while hovered.
 */
(function () {
	'use strict';

	var hero = document.querySelector('.gnn-hero');
	if (!hero) { return; }

	var track = hero.querySelector('.gnn-hero__track');
	var slides = hero.querySelectorAll('.gnn-hero__slide');
	var dots = hero.querySelectorAll('.gnn-hero__dot');
	var prev = hero.querySelector('.gnn-hero__arrow--prev');
	var next = hero.querySelector('.gnn-hero__arrow--next');
	var count = slides.length;
	var index = 0;
	var timer = null;
	var settings = window.gnnSlider || { autoplay: true, interval: 5000 };

	/* Respect reduced-motion: no autoplay, arrows/dots still work. */
	if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
		settings = { autoplay: false, interval: settings.interval };
	}

	if (count < 2) { return; }

	function render() {
		track.style.transform = 'translateX(-' + (index * 100) + '%)';
		slides.forEach(function (slide, i) { slide.classList.toggle('is-active', i === index); });
		dots.forEach(function (dot, i) {
			dot.classList.toggle('is-active', i === index);
			dot.setAttribute('aria-selected', i === index ? 'true' : 'false');
		});
	}

	function go(to) {
		index = (to + count) % count;
		render();
	}

	function startAutoplay() {
		if (!settings.autoplay || timer) { return; }
		timer = setInterval(function () { go(index + 1); }, settings.interval || 5000);
	}

	function stopAutoplay() {
		if (timer) { clearInterval(timer); timer = null; }
	}

	if (prev) { prev.addEventListener('click', function () { go(index - 1); }); }
	if (next) { next.addEventListener('click', function () { go(index + 1); }); }
	dots.forEach(function (dot, i) {
		dot.addEventListener('click', function () { go(i); });
	});

	hero.addEventListener('mouseenter', stopAutoplay);
	hero.addEventListener('mouseleave', startAutoplay);
	document.addEventListener('visibilitychange', function () {
		if (document.hidden) { stopAutoplay(); } else { startAutoplay(); }
	});

	render();
	startAutoplay();
})();
