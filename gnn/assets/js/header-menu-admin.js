/* Edit mega menu fields in the native WordPress menu screen. */
(function () {
	'use strict';

	var list = document.getElementById('menu-to-edit');
	if (!list) { return; }

	function updateFields() {
		var mega = false;
		Array.from(list.children).forEach(function (item) {
			var layout = item.querySelector('[data-gnn-menu-layout]');
			if (!layout) { return; }
			var top = item.classList.contains('menu-item-depth-0');
			var second = item.classList.contains('menu-item-depth-1');
			if (top) { mega = layout.value === 'parent'; }
			var featured = second && mega;
			layout.closest('p').hidden = !top && !featured;
			layout.querySelector('option[value="parent"]').hidden = !top;
			layout.querySelector('option[value="featured"]').hidden = !featured;
			item.querySelector('[data-gnn-featured-image]').hidden = !featured || layout.value !== 'featured';
		});
	}

	list.addEventListener('change', function (event) {
		if (event.target.matches('[data-gnn-menu-layout]')) { updateFields(); }
	});

	list.addEventListener('click', function (event) {
		var button = event.target.closest('[data-gnn-image-pick], [data-gnn-image-clear]');
		if (!button) { return; }
		var field = button.closest('[data-gnn-featured-image]');
		var value = field.querySelector('input');
		var image = field.querySelector('img');
		var remove = field.querySelector('[data-gnn-image-clear]');

		function setImage(id, src) {
			value.value = id;
			image.hidden = !src;
			remove.hidden = !id;
			if (src) { image.src = src; } else { image.removeAttribute('src'); }
		}

		if (button === remove) {
			setImage('', '');
			field.querySelector('[data-gnn-image-pick]').focus();
			return;
		}
		var frame = wp.media({title: button.textContent.trim(), library: {type: 'image'}, multiple: false});
		frame.on('open', function () {
			if (value.value) { frame.state().get('selection').add(wp.media.attachment(value.value)); }
		});
		frame.on('select', function () {
			var selected = frame.state().get('selection').first().toJSON();
			setImage(selected.id, selected.sizes && selected.sizes.thumbnail ? selected.sizes.thumbnail.url : selected.url);
		});
		frame.open();
	});

	var observer = new MutationObserver(function (records) {
		if (records.some(function (entry) { return entry.target === list || entry.target.parentNode === list; })) {
			updateFields();
		}
	});
	observer.observe(list, {childList: true, attributes: true, attributeFilter: ['class'], subtree: true});
	updateFields();
}());

