(function(){'use strict';document.addEventListener('click',function(event){var link=event.target.closest('.gnn-gallery-expand');if(!link||typeof HTMLDialogElement==='undefined'||event.ctrlKey||event.metaKey||event.shiftKey)return;event.preventDefault();var dialog=document.createElement('dialog'),image=document.createElement('img'),close=document.createElement('button');dialog.className='gnn-gallery-dialog';dialog.setAttribute('aria-label',link.getAttribute('aria-label')||'Image');image.src=link.href;image.alt=link.querySelector('img')?.alt||'';close.textContent='×';close.setAttribute('aria-label',link.closest('.gnn-module-gallery')?.dataset.closeLabel||window.gnnModules?.close||'Close');close.addEventListener('click',function(){dialog.close();});dialog.addEventListener('click',function(e){if(e.target===dialog)dialog.close();});dialog.addEventListener('close',function(){dialog.remove();link.focus();});dialog.append(close,image);document.body.append(dialog);dialog.showModal();close.focus();});})();
(function () {
	'use strict';
	var pending = Object.create(null);
	function update(section, url) {
		if (!window.fetch || !window.DOMParser || !window.AbortController) { window.location.href = url; return; }
		var type = section.dataset.gnnCollection;
		var sections = Array.from(document.querySelectorAll('[data-gnn-collection="' + type + '"]'));
		var index = sections.indexOf(section);
		var previous = pending[type];
		if (previous) previous.abort();
		sections.forEach(function (item) { item.removeAttribute('aria-busy'); });
		var controller = new AbortController();
		pending[type] = controller;
		section.setAttribute('aria-busy', 'true');
		fetch(url, { credentials: 'same-origin', signal: controller.signal }).then(function (response) {
			if (!response.ok) throw new Error('Request failed');
			return response.text();
		}).then(function (html) {
			var parsed = new DOMParser().parseFromString(html, 'text/html');
			var replacements = parsed.querySelectorAll('[data-gnn-collection="' + type + '"]');
			if (replacements.length !== sections.length || !replacements[index]) throw new Error('Collection missing');
			sections.forEach(function (current, position) { current.replaceWith(replacements[position]); });
			var focus = replacements[index].querySelector('.gnn-module-filters a[aria-current], .gnn-module-filter-select, .gnn-module-pagination [aria-current]');
			if (focus) focus.focus({ preventScroll: true });
			history.pushState({ gnnCollection: true }, '', url);
		}).catch(function (error) {
			if (error.name !== 'AbortError') window.location.href = url;
		}).finally(function () {
			if (pending[type] === controller) { delete pending[type]; section.removeAttribute('aria-busy'); }
		});
	}
	document.addEventListener('click', function (event) {
		var link = event.target.closest('.gnn-module-filters a, .gnn-module-pagination a');
		if (!link || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
		var section = link.closest('[data-gnn-collection]');
		if (!section || link.origin !== window.location.origin) return;
		event.preventDefault();
		update(section, link.href);
	});
	window.addEventListener('popstate', function () { window.location.reload(); });
	document.addEventListener('change', function (event) {
		if (!event.target.matches('.gnn-module-filter-select')) return;
		var form = event.target.closest('.gnn-module-filter-form');
		var section = form && form.closest('[data-gnn-collection]');
		if (section) {
			var url = new URL(form.action, window.location.href);
			url.searchParams.set(event.target.name, event.target.value);
			update(section, url.href);
			return;
		}
		if (form) {
			if (form.requestSubmit) { form.requestSubmit(); }
			else { form.submit(); }
		}
	});
})();
