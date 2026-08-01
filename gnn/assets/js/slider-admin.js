/**
 * GNN Slide meta box: media picker for the 2× retina background image.
 * Vanilla JS; wp.media comes from core.
 */
( function () {
	'use strict';

	document.addEventListener( 'click', function ( e ) {
		var pick = e.target.closest( '.gnn-media-pick' );
		var clear = e.target.closest( '.gnn-media-clear' );

		if ( pick && window.wp && wp.media ) {
			e.preventDefault();
			var box = pick.closest( '.gnn-media' );
			var input = box.querySelector( '.gnn-media-input' );
			var preview = box.querySelector( '.gnn-media-box' );
			var frame = wp.media( { title: pick.textContent, multiple: false, library: { type: 'image' } } );
			frame.on( 'select', function () {
				var att = frame.state().get( 'selection' ).first().toJSON();
				input.value = att.id;
				var url = ( att.sizes && att.sizes.medium ) ? att.sizes.medium.url : att.url;
				preview.textContent = '';
				var img = document.createElement( 'img' );
				img.className = 'gnn-media-preview';
				img.alt = '';
				img.src = url;
				preview.appendChild( img );
			} );
			frame.open();
		}

		if ( clear ) {
			e.preventDefault();
			var b = clear.closest( '.gnn-media' );
			b.querySelector( '.gnn-media-input' ).value = '0';
			b.querySelector( '.gnn-media-box' ).textContent = '';
		}
	} );
} )();
