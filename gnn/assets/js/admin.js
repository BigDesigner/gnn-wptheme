/**
 * GNN Panel — tabs + media pickers. Vanilla JS, no dependencies
 * (wp.media comes from core's media library).
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
} )();
