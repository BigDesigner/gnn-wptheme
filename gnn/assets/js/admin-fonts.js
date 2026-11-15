/* Searchable, keyboard-accessible font selection. No font files load in admin. */
( function () {
	'use strict';
	var config = window.gnnFontCatalog || {};
	var pending;
	var opened;
	function catalogue() {
		if ( ! pending ) {
			var body = new URLSearchParams( { action: 'gnn_font_catalog', nonce: config.nonce } );
			pending = fetch( config.ajax, { method: 'POST', credentials: 'same-origin', body: body } ).then( function ( response ) {
				if ( ! response.ok ) throw new Error( 'catalogue' );
				return response.json();
			} ).then( function ( result ) {
				if ( ! result.success || ! result.data || ! result.data.families ) throw new Error( 'catalogue' );
				return result.data;
			} ).catch( function () {
				return fetch( config.fallback, { credentials: 'same-origin' } ).then( function ( response ) {
					if ( ! response.ok ) throw new Error( 'catalogue' );
					return response.json();
				} ).then( function ( families ) { return { families: families, source: 'bundled' }; } );
			} ).catch( function ( error ) { pending = null; throw error; } );
		}
		return pending;
	}
	document.querySelectorAll( '.gnn-font-family' ).forEach( function ( input, index ) {
		var row = input.closest( '.gnn-typo-row' );
		var weight = row.querySelector( 'select' );
		var wrap = document.createElement( 'div' );
		wrap.className = 'gnn-font-combobox';
		input.before( wrap );
		wrap.appendChild( input );
		var button = document.createElement( 'button' );
		button.type = 'button';
		button.className = 'button gnn-font-toggle';
		button.textContent = '\u25be';
		button.setAttribute( 'aria-label', ( config.choose || 'Choose font' ) + ' — ' + input.getAttribute( 'aria-label' ) );
		var list = document.createElement( 'ul' );
		list.id = 'gnn-font-list-' + index;
		list.className = 'gnn-font-list';
		list.setAttribute( 'role', 'listbox' );
		list.setAttribute( 'aria-label', config.choose || 'Choose font' );
		list.hidden = true;
		var status = document.createElement( 'span' );
		status.className = 'gnn-font-status';
		status.setAttribute( 'role', 'status' );
		wrap.append( button, list, status );
		input.setAttribute( 'role', 'combobox' );
		input.setAttribute( 'aria-autocomplete', 'list' );
		input.setAttribute( 'aria-controls', list.id );
		input.setAttribute( 'aria-expanded', 'false' );
		button.setAttribute( 'aria-controls', list.id );
		button.setAttribute( 'aria-expanded', 'false' );
		var fonts;
		var position = -1;
		function close() {
			list.hidden = true;
			input.setAttribute( 'aria-expanded', 'false' );
			button.setAttribute( 'aria-expanded', 'false' );
			input.removeAttribute( 'aria-activedescendant' );
			position = -1;
			if ( opened === close ) opened = null;
		}
		function select( family ) {
			input.value = family;
			var available = fonts[ family ];
			Array.from( weight.options ).forEach( function ( option ) {
				option.disabled = !! available && ! available.includes( option.value );
			} );
			if ( available && ! available.includes( weight.value ) ) {
				weight.value = available.includes( '400' ) ? '400' : available[0];
				weight.dispatchEvent( new Event( 'change', { bubbles: true } ) );
			}
			input.dispatchEvent( new Event( 'change', { bubbles: true } ) );
			close();
			input.focus();
		}
		function render( term ) {
			list.replaceChildren();
			position = -1;
			input.removeAttribute( 'aria-activedescendant' );
			var query = term.trim().toLocaleLowerCase().replace( /\s/g, '' );
			var names = Object.keys( fonts ).filter( function ( family ) {
				return ! query || family.toLocaleLowerCase().replace( /\s/g, '' ).includes( query );
			} );
			if ( query ) {
				var rank = function ( family ) {
					var name = family.toLocaleLowerCase().replace( /\s/g, '' );
					return name === query ? 0 : name.startsWith( query ) ? 1 : 2;
				};
				names.sort( function ( a, b ) { return rank( a ) - rank( b ) || a.localeCompare( b ); } );
			}
			if ( ! query ) names.unshift( '' );
			var fragment = document.createDocumentFragment();
			names.forEach( function ( family, i ) {
				var option = document.createElement( 'li' );
				option.id = list.id + '-' + i;
				option.setAttribute( 'role', 'option' );
				option.setAttribute( 'aria-selected', String( family === input.value ) );
				option.textContent = family || config.default || 'Theme default';
				option.addEventListener( 'mousedown', function ( event ) { event.preventDefault(); } );
				option.addEventListener( 'click', function () { select( family ); } );
				fragment.appendChild( option );
			} );
			list.appendChild( fragment );
			if ( ! names.length ) status.textContent = config.empty || 'No fonts found.';
		}
		function open( filter ) {
			if ( opened && opened !== close ) opened();
			opened = close;
			list.hidden = false;
			input.setAttribute( 'aria-expanded', 'true' );
			button.setAttribute( 'aria-expanded', 'true' );
			if ( fonts ) { render( filter ? input.value : '' ); return; }
			status.textContent = config.loading || 'Loading fonts…';
			catalogue().then( function ( result ) {
				fonts = result.families;
				status.textContent = result.source === 'bundled' ? config.cached : '';
				if ( ! list.hidden ) render( filter ? input.value : '' );
			} ).catch( function () { status.textContent = config.unavailable; } );
		}
		button.addEventListener( 'click', function () { if ( list.hidden ) { open( false ); input.focus(); } else close(); } );
		input.addEventListener( 'input', function () { open( true ); } );
		input.addEventListener( 'keydown', function ( event ) {
			if ( event.key === 'Escape' ) { close(); event.stopPropagation(); return; }
			if ( event.key === 'Tab' ) { close(); return; }
			if ( ! [ 'ArrowDown', 'ArrowUp', 'Enter' ].includes( event.key ) ) return;
			if ( list.hidden ) {
				if ( event.key !== 'Enter' ) { event.preventDefault(); open( false ); }
				return;
			}
			var options = Array.from( list.children );
			if ( event.key === 'Enter' ) {
				event.preventDefault();
				if ( options[position] ) options[position].click();
				return;
			}
			event.preventDefault();
			position = Math.max( 0, Math.min( options.length - 1, position + ( event.key === 'ArrowDown' ? 1 : -1 ) ) );
			options.forEach( function ( option, i ) { option.classList.toggle( 'is-active', i === position ); } );
			if ( options[position] ) {
				input.setAttribute( 'aria-activedescendant', options[position].id );
				options[position].scrollIntoView( { block: 'nearest' } );
			}
		} );
		wrap.addEventListener( 'focusout', function () { setTimeout( function () { if ( ! wrap.contains( document.activeElement ) ) close(); }, 0 ); } );
		document.addEventListener( 'pointerdown', function ( event ) { if ( ! wrap.contains( event.target ) ) close(); } );
		window.addEventListener( 'hashchange', close );
		var enabled = document.getElementById( 'gnn-typography_google_enable' );
		function syncEnabled() {
			var disabled = enabled && ! enabled.checked;
			input.disabled = disabled;
			weight.disabled = disabled;
			button.disabled = disabled;
			if ( disabled ) close();
		}
		if ( enabled ) enabled.addEventListener( 'change', syncEnabled );
		syncEnabled();
	} );
} )();
