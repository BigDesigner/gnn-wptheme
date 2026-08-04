/**
 * GNN Panel — tabs, media pickers, and color pickers. Vanilla JS except
 * for the color picker init below, which uses WordPress core's own
 * wp-color-picker (jQuery + Iris, already loaded by wp_enqueue_style/
 * script( 'wp-color-picker' ) — see gnn_panel_assets()).
 */
( function () {
	'use strict';

	/* Tabs (hash-persistent). */
	var tabs = document.querySelectorAll( '.gnn-tabs .nav-tab' );
	var panes = document.querySelectorAll( '.gnn-tab' );

	function activate( id ) {
		tabs.forEach( function ( t ) {
			t.classList.toggle( 'nav-tab-active', t.dataset.tab === id );
		} );
		panes.forEach( function ( p ) {
			p.classList.toggle( 'is-active', p.id === 'gnn-tab-' + id );
		} );
	}

	tabs.forEach( function ( tab ) {
		tab.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			activate( tab.dataset.tab );
			history.replaceState( null, '', '#gnn-tab-' + tab.dataset.tab );
		} );
	} );

	var initial = ( location.hash || '' ).replace( '#gnn-tab-', '' );
	activate( document.getElementById( 'gnn-tab-' + initial ) ? initial : 'global' );

	/* Media pickers. */
	document.addEventListener( 'click', function ( e ) {
		var pick = e.target.closest( '.gnn-media-pick' );
		var clear = e.target.closest( '.gnn-media-clear' );

		if ( pick && window.wp && wp.media ) {
			e.preventDefault();
			var target = pick.dataset.target;
			var frame = wp.media( { title: pick.textContent, multiple: false, library: { type: 'image' } } );
			frame.on( 'select', function () {
				var att = frame.state().get( 'selection' ).first().toJSON();
				document.getElementById( 'gnn-media-' + target ).value = att.id;
				var box = document.querySelector( '.gnn-media-box[data-target="' + target + '"]' );
				var url = ( att.sizes && att.sizes.medium ) ? att.sizes.medium.url : att.url;
				/* DOM API (not innerHTML) — the URL never enters an HTML parse. */
				box.textContent = '';
				var img = document.createElement( 'img' );
				img.className = 'gnn-media-preview';
				img.alt = '';
				img.src = url;
				box.appendChild( img );
			} );
			frame.open();
		}

		if ( clear ) {
			e.preventDefault();
			var t = clear.dataset.target;
			document.getElementById( 'gnn-media-' + t ).value = '0';
			document.querySelector( '.gnn-media-box[data-target="' + t + '"]' ).innerHTML = '';
		}
	} );

	/* Color pickers: WordPress core's own Iris widget (same as Customizer)
	   instead of the browser's native OS color dialog. Default `hide`
	   behavior: a single swatch button collapsed by default, opening the
	   picker (with its own typeable hex field) on click. */
	if ( window.jQuery && jQuery.fn.wpColorPicker ) {
		jQuery( '.gnn-color-picker' ).wpColorPicker( { palettes: false } );
	}

	/* "Clear" buttons on fields where an empty value means "theme default". */
	document.addEventListener( 'click', function ( e ) {
		var btn = e.target.closest( '.gnn-color-clear' );
		if ( ! btn ) {
			return;
		}
		e.preventDefault();
		var input = document.getElementById( btn.dataset.target );
		if ( ! input ) {
			return;
		}
		/* Set the underlying input directly and let Iris's own change
		   listener pick it up — more reliable than the widget's color()
		   method, which isn't documented to handle an empty string. */
		if ( window.jQuery ) {
			jQuery( input ).val( '' ).trigger( 'change' );
		} else {
			input.value = '';
		}
	} );
} )();
