/**
 * GNN Panel — administration script.
 *
 * Handles tab navigation, range slider / number input two-way binding,
 * visual option cards, live search filtering, media pickers, and
 * native color controls.
 *
 * @package GNN
 */
( function () {
	'use strict';

	var panel = document.querySelector( '.gnn-panel' );
	if ( ! panel ) {
		return;
	}

	var text      = window.gnnAdminText || {};
	var tabs      = Array.from( panel.querySelectorAll( '.gnn-tabs .nav-tab, .gnn-sidebar-nav .gnn-nav-item' ) );
	var panes     = Array.from( panel.querySelectorAll( '.gnn-tab' ) );
	var form      = panel.querySelector( '#gnn-settings-form, #gnn-brand-form' ) || panel.querySelector( 'form[action="options.php"]' );
	var search    = panel.querySelector( '#gnn-settings-search' );
	var feedback  = panel.querySelector( '.gnn-search-feedback' );
	var pageTitle = document.getElementById( 'gnn-page-heading' );
	var active    = 'start';
	var dirty     = false;
	var status    = form && form.querySelector( '.gnn-save-status' );

	if ( form && ! status ) {
		status = document.createElement( 'p' );
		status.className = 'gnn-save-status';
		status.setAttribute( 'role', 'status' );
		form.prepend( status );
	}

	// ==========================================================================
	// 1. Toast Notification System
	// ==========================================================================
	function showToast( message, isError ) {
		var container = document.getElementById( 'gnn-toast-container' );
		if ( ! container ) {
			container = document.createElement( 'div' );
			container.id = 'gnn-toast-container';
			container.className = 'gnn-toast-container';
			container.setAttribute( 'aria-live', 'polite' );
			document.body.appendChild( container );
		}

		var toast = document.createElement( 'div' );
		toast.className = 'gnn-toast-alert' + ( isError ? ' gnn-toast--error' : '' );
		toast.setAttribute( 'role', 'alert' );

		var icon = document.createElement( 'span' );
		icon.className = 'dashicons dashicons-' + ( isError ? 'warning' : 'yes-alt' ) + ' gnn-toast-icon';
		icon.setAttribute( 'aria-hidden', 'true' );
		var messageElement = document.createElement( 'div' );
		messageElement.className = 'gnn-toast-msg';
		messageElement.textContent = message;
		var close = document.createElement( 'button' );
		close.type = 'button';
		close.className = 'gnn-toast-close';
		close.setAttribute( 'aria-label', text.close || 'Close' );
		close.textContent = '\u00d7';
		toast.append( icon, messageElement, close );

		toast.querySelector( '.gnn-toast-close' ).addEventListener( 'click', function () {
			toast.classList.add( 'gnn-toast--dismissing' );
			setTimeout( function () {
				if ( toast.parentElement ) {
					toast.remove();
				}
			}, 300 );
		} );

		container.appendChild( toast );

		requestAnimationFrame( function () {
			toast.classList.add( 'is-visible' );
		} );

		setTimeout( function () {
			if ( toast.parentElement ) {
				toast.classList.remove( 'is-visible' );
				toast.classList.add( 'gnn-toast--dismissing' );
				setTimeout( function () {
					if ( toast.parentElement ) {
						toast.remove();
					}
				}, 300 );
			}
		}, 3800 );
	}

	// Trigger toast on successful settings update from query arg
	var saved = new URLSearchParams( window.location.search ).get( 'settings-updated' );
	if ( ( text.settingsUpdated || saved === 'true' || saved === '1' ) && ! panel.querySelector( '.notice-error, .error.settings-error' ) ) {
		showToast( text.savedSuccess || 'Settings saved.', false );
	}

	// ==========================================================================
	// 2. CodeMirror Bindings
	// ==========================================================================
	function bindEditors() {
		panel.querySelectorAll( '.CodeMirror' ).forEach( function ( el ) {
			if ( el.CodeMirror && ! el.dataset.gnnDirtyBound ) {
				el.dataset.gnnDirtyBound = 'true';
				el.CodeMirror.on( 'change', markDirty );
			}
		} );
	}

	function refreshEditors( pane ) {
		requestAnimationFrame( function () {
			bindEditors();
			pane.querySelectorAll( '.CodeMirror' ).forEach( function ( el ) {
				if ( el.CodeMirror ) {
					el.CodeMirror.refresh();
				}
			} );
		} );
	}
	window.addEventListener( 'load', bindEditors );

	// ==========================================================================
	// 3. Tab Navigation & Title Sync
	// ==========================================================================
	if ( tabs.length ) {
		tabs[0].parentNode.setAttribute( 'role', 'tablist' );
		tabs[0].parentNode.setAttribute( 'aria-orientation', 'vertical' );
		tabs.forEach( function ( tab ) {
			tab.setAttribute( 'role', 'tab' );
			tab.setAttribute( 'aria-controls', 'gnn-tab-' + tab.dataset.tab );
		} );
		panes.forEach( function ( pane ) {
			pane.setAttribute( 'role', 'tabpanel' );
			pane.setAttribute( 'aria-labelledby', 'gnn-nav-' + pane.id.replace( 'gnn-tab-', '' ) );
		} );
	}

	function activate( id, remember ) {
		if ( ! panes.some( function ( p ) { return p.id === 'gnn-tab-' + id; } ) ) {
			id = 'start';
		}
		active = id;

		tabs.forEach( function ( tab ) {
			var selected = tab.dataset.tab === id;
			tab.classList.toggle( 'nav-tab-active', selected );
			tab.classList.toggle( 'is-active', selected );
			tab.setAttribute( 'aria-selected', String( selected ) );
			tab.tabIndex = selected ? 0 : -1;
			if ( selected ) {
				tab.setAttribute( 'aria-current', 'page' );
				if ( pageTitle ) {
					pageTitle.textContent = tab.getAttribute( 'data-title' ) || tab.textContent.trim();
				}
			} else {
				tab.removeAttribute( 'aria-current' );
			}
		} );

		panes.forEach( function ( pane ) {
			var selected = pane.id === 'gnn-tab-' + id;
			pane.classList.toggle( 'is-active', selected );
			pane.hidden = ! selected;
			if ( selected ) {
				refreshEditors( pane );
			}
		} );

		var toolTabs = [ 'start', 'advanced', 'templates', 'layouts', 'setup', 'plugins', 'maintenance' ];
		panel.querySelectorAll( '#gnn-save-header, .gnn-savebar' ).forEach( function ( el ) {
			el.hidden = toolTabs.includes( id );
		} );

		if ( remember ) {
			history.replaceState( null, '', '#gnn-tab-' + id );
			try {
				sessionStorage.setItem( 'gnn-panel-tab', id );
			} catch ( e ) {
				/* Session storage is optional. */
			}
		}
	}

	tabs.forEach( function ( tab, index ) {
		tab.addEventListener( 'click', function ( event ) {
			event.preventDefault();
			if ( search && search.value ) {
				search.value = '';
				applySearch();
			}
			activate( tab.dataset.tab, true );
		} );

		tab.addEventListener( 'keydown', function ( event ) {
			var next = index;
			if ( event.key === 'ArrowDown' || event.key === 'ArrowRight' ) {
				next = ( index + 1 ) % tabs.length;
			} else if ( event.key === 'ArrowUp' || event.key === 'ArrowLeft' ) {
				next = ( index + tabs.length - 1 ) % tabs.length;
			} else if ( event.key === 'Home' ) {
				next = 0;
			} else if ( event.key === 'End' ) {
				next = tabs.length - 1;
			} else {
				return;
			}
			event.preventDefault();
			tabs[ next ].click();
			tabs[ next ].focus();
		} );
	} );

	var initial = location.hash.replace( '#gnn-tab-', '' );
	if ( ! initial ) {
		try {
			initial = sessionStorage.getItem( 'gnn-panel-tab' ) || 'start';
		} catch ( e ) {
			initial = 'start';
		}
	}

	if ( tabs.length ) {
		panel.classList.add( 'gnn-admin-ready' );
		activate( initial, false );
	}

	window.addEventListener( 'hashchange', function () {
		if ( tabs.length ) {
			activate( location.hash.replace( '#gnn-tab-', '' ), false );
		}
	} );

	// ==========================================================================
	// 4. Live Search Filtering
	// ==========================================================================
	function applySearch() {
		if ( ! search ) {
			return;
		}
		var term = search.value.trim().toLocaleLowerCase();
		panel.classList.toggle( 'gnn-searching', !! term );

		if ( ! term ) {
			if ( feedback ) {
				feedback.hidden = true;
			}
			panes.forEach( function ( pane ) {
				pane.querySelectorAll( '.gnn-setting-row, .gnn-typo-row, .gnn-field' ).forEach( function ( row ) {
					row.hidden = false;
					row.classList.remove( 'is-search-match' );
				} );
				pane.querySelectorAll( '.gnn-section-bar' ).forEach( function ( bar ) {
					bar.hidden = false;
				} );
			} );
			activate( active, false );
			return;
		}

		var count = 0;
		panes.forEach( function ( pane ) {
			var paneMatches = 0;
			var rows = Array.from( pane.querySelectorAll( '.gnn-setting-row, .gnn-typo-row, .gnn-field' ) );

			rows.forEach( function ( row ) {
				var match = ( row.textContent + ' ' + Array.from( row.querySelectorAll( 'input,select' ) ).map( function ( el ) { return el.getAttribute( 'aria-label' ) || ''; } ).join( ' ' ) ).toLocaleLowerCase().includes( term );
				row.hidden = ! match;
				row.classList.toggle( 'is-search-match', match );
				if ( match ) {
					count++;
					paneMatches++;
				}
			} );

			// Hide section bars that have no visible rows immediately after them
			var bars = Array.from( pane.querySelectorAll( '.gnn-section-bar' ) );
			bars.forEach( function ( bar ) {
				var next = bar.nextElementSibling;
				var hasVisibleRow = false;
				while ( next && ! next.classList.contains( 'gnn-section-bar' ) ) {
					if ( ( next.matches( '.gnn-setting-row, .gnn-typo-row, .gnn-field' ) || next.querySelector( '.gnn-setting-row:not([hidden]), .gnn-typo-row:not([hidden]), .gnn-field:not([hidden])' ) ) && ! next.hidden ) {
						hasVisibleRow = true;
						break;
					}
					next = next.nextElementSibling;
				}
				bar.hidden = ! hasVisibleRow;
			} );

			pane.hidden = ! paneMatches;
		} );

		if ( feedback ) {
			feedback.hidden = false;
			feedback.textContent = count
				? ( text.searchResults || '%d setting groups found' ).replace( '%d', count )
				: ( text.noResults || 'No settings match your search. Try another keyword.' );
		}
	}

	if ( search ) {
		search.addEventListener( 'input', applySearch );
	}

	// Focus invalid field on settings error
	if ( text.invalidField ) {
		var invalid = panel.querySelector( '#gnn-' + text.invalidField );
		if ( invalid ) {
			var invalidPane = invalid.closest( '.gnn-tab' );
			if ( invalidPane ) {
				activate( invalidPane.id.replace( 'gnn-tab-', '' ), true );
			}
			invalid.setAttribute( 'aria-invalid', 'true' );
			invalid.focus();
			invalid.scrollIntoView( { block: 'center' } );
		}
	}

	// ==========================================================================
	// 5. Dual Range Slider & Number Input Two-Way Binding
	// ==========================================================================
	panel.querySelectorAll( '.gnn-range-group' ).forEach( function ( group ) {
		var slider = group.querySelector( '.gnn-range-slider' );
		var number = group.querySelector( '.gnn-range-number' );
		if ( ! slider || ! number ) {
			return;
		}

		slider.addEventListener( 'input', function () {
			number.value = slider.value;
			number.dispatchEvent( new Event( 'input', { bubbles: true } ) );
			number.dispatchEvent( new Event( 'change', { bubbles: true } ) );
		} );

		number.addEventListener( 'input', function () {
			var val = parseInt( number.value, 10 );
			var min = parseInt( slider.min, 10 ) || 0;
			var max = Number.isNaN( parseInt( slider.max, 10 ) ) ? 9999 : parseInt( slider.max, 10 );
			if ( ! isNaN( val ) ) {
				if ( val > max ) val = max;
				if ( val < min ) val = min;
				slider.value = val;
			}
		} );

		number.addEventListener( 'change', function () {
			var val = parseInt( number.value, 10 );
			var min = parseInt( slider.min, 10 ) || 0;
			var max = Number.isNaN( parseInt( slider.max, 10 ) ) ? 9999 : parseInt( slider.max, 10 );
			if ( isNaN( val ) || val < min ) {
				number.value = min;
			} else if ( val > max ) {
				number.value = max;
			}
			if ( ! isNaN( val ) ) number.value = Math.min( max, Math.max( min, val ) );
			slider.value = number.value;
			markDirty();
		} );
	} );

	// ==========================================================================
	// 6. Visual Option Cards Binding
	// ==========================================================================
	panel.querySelectorAll( '.gnn-option-cards' ).forEach( function ( group ) {
		var hiddenInput = group.querySelector( 'input[type="hidden"]' );
		var cards       = Array.from( group.querySelectorAll( '.gnn-option-card' ) );

		function selectCard( card ) {
			var val = card.getAttribute( 'data-value' );
			cards.forEach( function ( c ) {
				c.classList.remove( 'is-selected' );
				c.setAttribute( 'aria-checked', 'false' );
			} );
			card.classList.add( 'is-selected' );
			card.setAttribute( 'aria-checked', 'true' );
			if ( hiddenInput ) {
				hiddenInput.value = val;
				hiddenInput.dispatchEvent( new Event( 'input', { bubbles: true } ) );
				hiddenInput.dispatchEvent( new Event( 'change', { bubbles: true } ) );
			}
			markDirty();
		}

		cards.forEach( function ( card ) {
			card.addEventListener( 'click', function () {
				selectCard( card );
			} );
			card.addEventListener( 'keydown', function ( e ) {
				if ( e.key === 'Enter' || e.key === ' ' ) {
					e.preventDefault();
					selectCard( card );
				}
			} );
		} );
	} );

	// ==========================================================================
	// 7. Dirty State & Form Save
	// ==========================================================================
	function markDirty() {
		dirty = true;
		if ( status ) {
			status.textContent = text.unsaved || 'Unsaved changes';
		}
		var bar = form && form.querySelector( '.gnn-savebar' );
		if ( bar ) {
			bar.classList.add( 'is-dirty' );
		}
	}

	if ( form ) {
		form.addEventListener( 'invalid', function ( event ) {
			var input = event.target;
			if ( search && search.value ) { search.value = ''; applySearch(); }
			var pane = input.closest( '.gnn-tab' );
			if ( pane ) activate( pane.id.replace( 'gnn-tab-', '' ), true );
			var parent = input.parentElement;
			while ( parent && parent !== form ) {
				if ( parent.tagName === 'DETAILS' ) parent.open = true;
				if ( parent.matches( '.gnn-setting-row, .gnn-typo-row, .gnn-field' ) ) parent.hidden = false;
				parent = parent.parentElement;
			}
			input.setAttribute( 'aria-invalid', 'true' );
		}, true );
		form.addEventListener( 'input', function ( event ) {
			if ( event.target.validity && event.target.validity.valid ) event.target.removeAttribute( 'aria-invalid' );
		} );
		if ( status ) {
			status.textContent = text.ready || 'No pending changes';
		}
		form.addEventListener( 'input', markDirty );
		form.addEventListener( 'change', markDirty );

		if ( window.jQuery ) {
			jQuery( form ).on( 'change', '.gnn-color-picker', markDirty );
		}

		form.addEventListener( 'submit', function () {
			form.querySelectorAll( '.CodeMirror' ).forEach( function ( el ) {
				if ( el.CodeMirror ) {
					el.CodeMirror.save();
				}
			} );
			form.querySelectorAll( '.gnn-action-editor' ).forEach( function ( editor ) {
				var items = [];
				editor.querySelectorAll( '.gnn-action-row' ).forEach( function ( row ) {
					var label = row.querySelector( '[data-action-label]' ).value.trim();
					if ( label ) {
						items.push( {
							type: row.querySelector( '[data-action-type]' ).value,
							label: label,
							url: row.querySelector( '[data-action-url]' ).value
						} );
					}
				} );
				editor.querySelector( '[data-action-payload]' ).value = JSON.stringify( items );
			} );
			dirty = false;
			if ( status ) {
				status.textContent = text.saving || 'Saving settings…';
			}
			form.setAttribute( 'aria-busy', 'true' );
		} );

		window.addEventListener( 'beforeunload', function ( event ) {
			if ( dirty ) {
				event.preventDefault();
				event.returnValue = '';
			}
		} );
	}

	// ==========================================================================
	// 8. Media Pickers (WordPress Media Frame)
	// ==========================================================================
	document.addEventListener( 'click', function ( e ) {
		var clearFont = e.target.closest( '.gnn-font-clear' );
		if ( clearFont ) {
			var local = document.getElementById( clearFont.dataset.target );
			local.value = '0';
			local.dispatchEvent( new Event( 'change', { bubbles: true } ) );
			local.closest( '.gnn-local-font' ).querySelector( '.gnn-local-font-name' ).textContent = text.noFont || 'No font selected';
			clearFont.hidden = true;
			return;
		}
		var font = e.target.closest( '.gnn-font-pick' );
		if ( font && window.wp && wp.media ) {
			e.preventDefault();
			var fonts = wp.media( {
				title: font.textContent,
				multiple: false,
				library: { type: 'font/woff2' }
			} );
			fonts.on( 'select', function () {
				var input = document.getElementById( font.dataset.target );
				var attachment = fonts.state().get( 'selection' ).first().toJSON();
				input.value = attachment.id;
				input.closest( '.gnn-local-font' ).querySelector( '.gnn-local-font-name' ).textContent = attachment.title || attachment.filename;
				input.closest( '.gnn-local-font' ).querySelector( '.gnn-font-clear' ).hidden = false;
				input.dispatchEvent( new Event( 'change', { bubbles: true } ) );
			} );
			fonts.open();
			return;
		}

		var pick  = e.target.closest( '.gnn-media-pick' );
		var clear = e.target.closest( '.gnn-media-clear' );

		if ( pick && window.wp && wp.media ) {
			e.preventDefault();
			var target = pick.dataset.target;
			var frame  = wp.media( {
				title: pick.textContent,
				multiple: false,
				library: { type: 'image' }
			} );
			frame.on( 'select', function () {
				var att = frame.state().get( 'selection' ).first().toJSON();
				document.getElementById( 'gnn-media-' + target ).value = att.id;
				document.getElementById( 'gnn-media-' + target ).dispatchEvent( new Event( 'change', { bubbles: true } ) );
				var box = document.querySelector( '.gnn-media-box[data-target="' + target + '"]' );
				var url = ( att.sizes && att.sizes.medium ) ? att.sizes.medium.url : att.url;
				box.textContent = '';
				var img = document.createElement( 'img' );
				img.className = 'gnn-media-preview';
				img.alt = '';
				img.src = url;
				box.appendChild( img );
				markDirty();
			} );
			frame.open();
		}

		if ( clear ) {
			e.preventDefault();
			var t = clear.dataset.target;
			var hiddenMediaInput = document.getElementById( 'gnn-media-' + t );
			if ( hiddenMediaInput ) {
				hiddenMediaInput.value = '0';
				hiddenMediaInput.dispatchEvent( new Event( 'change', { bubbles: true } ) );
			}
			var previewBox = document.querySelector( '.gnn-media-box[data-target="' + t + '"]' );
			if ( previewBox ) {
				previewBox.innerHTML = '';
			}
			markDirty();
		}
	} );

	// ==========================================================================
	// 9. Iris Color Picker Integration
	// ==========================================================================
	panel.querySelectorAll( '.gnn-color-picker' ).forEach( function ( input ) {
		var swatch = document.createElement( 'input' );
		swatch.type = 'color';
		swatch.className = 'gnn-color-swatch';
		swatch.setAttribute( 'aria-label', ( text.chooseColor || 'Choose color' ) + ' — ' + ( input.getAttribute( 'aria-label' ) || Array.from( input.labels || [] ).map( function ( l ) { return l.textContent.trim(); } ).join( ' ' ) ) );
		input.before( swatch );
		function normalize( value ) {
			return /^#[0-9a-f]{3}$/i.test( value ) ? '#' + value.slice( 1 ).split( '' ).map( function ( c ) { return c + c; } ).join( '' ) : value;
		}
		function paint() {
			var value = input.value.trim();
			var fallback = input.id === 'gnn-accent_light' && ! value ? document.getElementById( 'gnn-accent' ).value : input.dataset.defaultColor;
			swatch.value = /^#[0-9a-f]{6}$/i.test( normalize( value ) ) ? normalize( value ) : normalize( fallback || '#ffffff' );
			input.placeholder = normalize( fallback || '#ffffff' );
			input.setCustomValidity( value && ! /^#(?:[0-9a-f]{3}|[0-9a-f]{6})$/i.test( value ) ? text.invalidColor || 'Enter a valid hex color.' : '' );
		}
		swatch.addEventListener( 'input', function () { input.value = swatch.value; input.dispatchEvent( new Event( 'input', { bubbles: true } ) ); } );
		swatch.addEventListener( 'change', function () { input.value = swatch.value; input.dispatchEvent( new Event( 'change', { bubbles: true } ) ); } );
		input.addEventListener( 'input', paint );
		input.addEventListener( 'change', paint );
		if ( input.id === 'gnn-accent' ) input.addEventListener( 'input', function () { var light = document.getElementById( 'gnn-accent_light' ); if ( light && ! light.value ) light.dispatchEvent( new Event( 'change', { bubbles: true } ) ); } );
		paint();
	} );

	// Color Clear buttons
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
		input.value = '';
		input.dispatchEvent( new Event( 'input', { bubbles: true } ) );
		input.dispatchEvent( new Event( 'change', { bubbles: true } ) );
		markDirty();
	} );

	// ==========================================================================
	// 10. Dependent Blog Controls
	// ==========================================================================
	function syncBlogControls() {
		function value( key ) {
			var el = panel.querySelector( '#gnn-' + key );
			return el ? el.value : '';
		}
		function show( key, visible ) {
			var el = panel.querySelector( '#gnn-' + key );
			if ( ! el ) return;
			var row = el.closest( '.gnn-setting-row' );
			if ( row ) {
				row.classList.add( 'gnn-blog-conditional' );
				row.hidden = ! ( visible || ( search && search.value.trim() ) );
			}
		}

		var layout = value( 'post_image_layout' ),
			height = value( 'post_image_height_mode' ),
			card   = value( 'blog_card_image_mode' );
		var image  = layout !== 'hidden';

		show( 'post_image_width', layout === 'wide' );
		show( 'post_image_height_mode', image );
		show( 'post_image_ratio', image && height === 'ratio' );
		show( 'post_featured_image_height', image && height === 'fixed' );
		show( 'post_featured_image_fit', image && height !== 'natural' );
		show( 'post_featured_image_position', image && height !== 'natural' );
		show( 'post_image_caption', image );
		show( 'blog_card_image_ratio', card === 'ratio' );
		show( 'blog_card_image_height', card === 'fixed' );
		show( 'blog_card_image_fit', card === 'ratio' || card === 'fixed' );
		show( 'blog_card_image_position', card === 'ratio' || card === 'fixed' );

		var author = panel.querySelector( '#gnn-blog_author' );
		if ( author ) {
			show( 'post_author_box', author.checked );
		}
	}

	panel.addEventListener( 'change', syncBlogControls );
	if ( search ) {
		search.addEventListener( 'input', syncBlogControls );
	}
	syncBlogControls();
	var headerPreset = document.getElementById( 'gnn-header_preset' );
	var legacyHeader = panel.querySelector( '[data-gnn-legacy-header]' );
	if ( headerPreset && legacyHeader ) {
		var syncHeader = function () { legacyHeader.hidden = headerPreset.value !== 'legacy'; };
		headerPreset.addEventListener( 'change', syncHeader );
		syncHeader();
	}
} )();

/* Cleanup form validation */
( function () {
	function ready() {
		document.querySelectorAll( 'form[data-gnn-cleanup]' ).forEach( function ( form ) {
			var rows = Array.from( form.querySelectorAll( 'input[name^="cleanup["]' ) );
			var button = form.querySelector( '[type="submit"]' );
			function sync() {
				button.disabled = ! rows.some( function ( row ) { return row.checked; } );
			}
			form.addEventListener( 'change', sync );
			form.addEventListener( 'submit', function ( event ) {
				if ( ! rows.some( function ( row ) { return row.checked; } ) ) {
					event.preventDefault();
					var feedback = form.querySelector( '.gnn-cleanup-feedback' );
					if ( feedback ) {
						feedback.focus();
					}
				}
			} );
			var feedback = form.querySelector( '.gnn-cleanup-feedback' );
			if ( feedback ) {
				feedback.tabIndex = -1;
			}
			sync();
		} );
	}
	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', ready );
	} else {
		ready();
	}
} )();
