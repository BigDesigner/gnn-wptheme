(function () {
	'use strict';
	var pending = Object.create( null );
	function page(url) {
		return String( url ).split( '#' )[0]; }
	var current = page( window.location.href );
	if ( ! history.state && document.querySelector( '[data-gnn-collection]' )) {
		history.replaceState( { gnnCollection: true, url: current }, '', window.location.href );
	}
	function update(section, url, restoring) {
		if ( ! window.fetch || ! window.DOMParser || ! window.AbortController) {
			window.location.href = url; return; }
		var type     = section.dataset.gnnCollection;
		var sections = Array.from( document.querySelectorAll( '[data-gnn-collection="' + type + '"]' ) );
		var index    = sections.indexOf( section );
		var previous = pending[type];
		if (previous) {
			previous.abort();
		}
		sections.forEach(
			function (item) {
				item.removeAttribute( 'aria-busy' ); }
		);
		var controller = new AbortController();
		pending[type]  = controller;
		section.setAttribute( 'aria-busy', 'true' );
		fetch( url, { credentials: 'same-origin', signal: controller.signal } ).then(
			function (response) {
				if ( ! response.ok) {
					throw new Error( 'Request failed' );
				}
				return response.text();
			}
		).then(
			function (html) {
				var parsed       = new DOMParser().parseFromString( html, 'text/html' );
				var replacements = parsed.querySelectorAll( '[data-gnn-collection="' + type + '"]' );
				if (replacements.length !== sections.length || ! replacements[index]) {
					throw new Error( 'Collection missing' );
				}
				sections.forEach(
					function (current, position) {
						current.replaceWith( replacements[position] ); }
				);
				var focus = replacements[index].querySelector( '.gnn-module-filters a[aria-current], .gnn-module-filter-select, .gnn-module-pagination [aria-current]' );
				if (focus && ! restoring) {
					focus.focus( { preventScroll: true } );
				}
				current = page( url );
				if ( ! restoring) {
					history.pushState( { gnnCollection: type, url: url }, '', url );
				}
			}
		).catch(
			function (error) {
				if (error.name !== 'AbortError') {
					window.location.href = url;
				}
			}
		).finally(
			function () {
				if (pending[type] === controller) {
					delete pending[type]; section.removeAttribute( 'aria-busy' ); }
			}
		);
	}
	document.addEventListener(
		'click',
		function (event) {
			var link = event.target.closest( '.gnn-module-filters a, .gnn-module-pagination a' );
			if ( ! link || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
				return;
			}
			var section = link.closest( '[data-gnn-collection]' );
			if ( ! section || link.origin !== window.location.origin) {
				return;
			}
			event.preventDefault();
			update( section, link.href );
		}
	);
	window.addEventListener(
		'popstate',
		function (event) {
			var state = event.state;
			// In-page anchor entries carry no state; they still show the collection of their own URL.
			var url = state && state.gnnCollection && state.url ? state.url : ( ! state && document.querySelector( '[data-gnn-collection]' ) ? window.location.href : '');
			if ( ! url || page( url ) === current) {
				return;
			}
			var seen     = Object.create( null );
			var sections = Array.from( document.querySelectorAll( '[data-gnn-collection]' ) ).filter(
				function (item) {
					var type = item.dataset.gnnCollection;
					if (seen[type]) {
						return false;
					}
					seen[type] = true;
					return true;
				}
			);
			if ( ! sections.length) {
				window.location.reload(); return; }
			sections.forEach(
				function (item) {
					update( item, url, true ); }
			);
		}
	);
	document.addEventListener(
		'change',
		function (event) {
			if ( ! event.target.matches( '.gnn-module-filter-select' )) {
				return;
			}
			var form    = event.target.closest( '.gnn-module-filter-form' );
			var section = form && form.closest( '[data-gnn-collection]' );
			if (section) {
				var url = new URL( form.action, window.location.href );
				url.searchParams.set( event.target.name, event.target.value );
				update( section, url.href );
				return;
			}
			if (form) {
				if (form.requestSubmit) {
					form.requestSubmit(); } else {
					form.submit(); }
			}
		}
	);
})();
