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
	var saveControls = panel.querySelectorAll( '#gnn-save-header, .gnn-savebar' );

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

		updateSaveControls();

		if ( remember ) {
			history.replaceState( null, '', '#gnn-tab-' + id );
			try {
				sessionStorage.setItem( 'gnn-panel-tab', id );
			} catch ( e ) {
				/* Session storage is optional. */
			}
		}
	}

	function updateSaveControls() {
		var visible = form && form.querySelector( '.gnn-tab:not([hidden])' );
		saveControls.forEach( function ( el ) {
			el.hidden = ! visible;
		} );
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
		if ( tabs.length && ( ! location.hash || location.hash.startsWith( '#gnn-tab-' ) ) ) {
			if ( search && search.value ) {
				search.value = '';
				applySearch();
			}
			activate( location.hash.replace( '#gnn-tab-', '' ), false );
		}
	} );

	// Card navigation uses buttons so the tab hash stays available for tab routing.
	panes.forEach( function ( pane ) {
		var cards = Array.from( pane.querySelectorAll(
			':scope > .gnn-settings-card, :scope > form > .gnn-settings-card, :scope > form > .gnn-choice-grid > .gnn-rule-card, ' +
			':scope > .gnn-start > .gnn-start-card, :scope > .gnn-choice-grid > .gnn-plugin-card'
		) );
		var targets = cards.map( function ( card ) {
			return card.querySelector( ':scope > .gnn-settings-group__header > h2, :scope > .gnn-start-head > h2, :scope > h2, :scope > h3, :scope > summary' );
		} ).filter( Boolean );

		if ( targets.length < 3 ) {
			return;
		}

		var nav = document.createElement( 'nav' );
		nav.className = 'gnn-card-nav';
		nav.setAttribute( 'aria-label', text.groupsLabel );
		targets.forEach( function ( target ) {
			var button = document.createElement( 'button' );
			button.type = 'button';
			button.textContent = ( target.querySelector( ':scope > span' ) || target ).textContent.trim();
			button.addEventListener( 'click', function () {
				var details = target.closest( 'details' );
				if ( details ) {
					details.open = true;
				}
				target.tabIndex = -1;
				target.focus( { preventScroll: true } );
				target.scrollIntoView( { block: 'start' } );
			} );
			nav.appendChild( button );
		} );
		pane.prepend( nav );
	} );

	// ==========================================================================
	// 4. Live Search Filtering
	// ==========================================================================
	var searchRows = '.gnn-setting-row, .gnn-typo-row, .gnn-field';
	var searchGroups = '.gnn-settings-group, .gnn-btn-style-card, .gnn-design-group, .gnn-design-subgroup, .gnn-rule-card';
	var searchOpened = new Set();

	function applySearch() {
		if ( ! search ) {
			return;
		}
		var term = search.value.trim().toLocaleLowerCase();
		panel.classList.toggle( 'gnn-searching', !! term );

		searchOpened.forEach( function ( details ) { details.open = false; } );
		searchOpened.clear();

		var count = 0;
		panes.forEach( function ( pane ) {
			var paneMatches = 0;
			pane.querySelectorAll( searchRows ).forEach( function ( row ) {
				var match = !! term && ( row.textContent + ' ' + Array.from( row.querySelectorAll( 'input,select' ) ).map( function ( el ) { return el.getAttribute( 'aria-label' ) || ''; } ).join( ' ' ) ).toLocaleLowerCase().includes( term );
				row.hidden = !! term && ! match;
				row.classList.toggle( 'is-search-match', match );
				if ( match ) {
					paneMatches++;
				}
			} );

			pane.querySelectorAll( searchGroups ).forEach( function ( card ) {
				var match = !! card.querySelector( '.is-search-match' );
				card.hidden = !! term && ! match;
				if ( term && match && card.tagName === 'DETAILS' && ! card.open ) {
					card.open = true;
					searchOpened.add( card );
				}
			} );

			if ( term ) {
				pane.hidden = ! paneMatches;
				if ( paneMatches ) {
					refreshEditors( pane );
				}
			}
			count += paneMatches;
		} );

		if ( feedback ) {
			feedback.hidden = ! term;
			feedback.textContent = count
				? ( text.searchResults || '%d setting groups found' ).replace( '%d', count )
				: ( text.noResults || 'No settings match your search. Try another keyword.' );
		}

		if ( term ) {
			syncHeaderControls();
			updateSaveControls();
		} else {
			syncBlogControls();
			syncHeaderControls();
			activate( active, false );
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
			var scope = form.querySelector( '[name="gnn_form_scope"]' );
			if ( scope && scope.value === 'design' && active === 'header' && ! dirty ) {
				scope.value = 'header-unchanged';
			}
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
	syncBlogControls();

	// ==========================================================================
	// 11. Header Tab Controls, Elements Editor & Live Preview
	// ==========================================================================
	var headerPane = document.getElementById( 'gnn-tab-header' );
	var headerMeta = headerPane && headerPane.querySelector( '[data-gnn-header-admin]' );
	var heEditor   = headerPane && headerPane.querySelector( '[data-gnn-he-editor]' );
	var hpRoot     = headerPane && headerPane.querySelector( '[data-gnn-header-preview]' );
	var hpLabels   = hpRoot && JSON.parse( hpRoot.dataset.previewLabels );
	var elementsTouched = false;

	function headerVal( key ) {
		var el = headerPane && headerPane.querySelector( '#gnn-' + key );
		return el ? el.value : '';
	}

	function headerChecked( key ) {
		var el = headerPane && headerPane.querySelector( '#gnn-' + key );
		return !!( el && el.checked );
	}

	function headerStructureValue() {
		var checked = headerPane && headerPane.querySelector( 'input[name="gnn_options[header_structure]"]:checked' );
		return checked ? checked.value : '';
	}

	function effectiveHeaderState() {
		if ( ! headerMeta ) {
			return null;
		}
		var rawStruct   = headerStructureValue();
		var rawSurface  = headerVal( 'header_surface' );
		var rawShape    = headerVal( 'header_shape' );
		var rawWidth    = headerVal( 'header_width' );
		var rawBehavior = headerVal( 'header_behavior' );
		var rawOverlay  = headerVal( 'header_overlay' );

		return {
			structure:  rawStruct || headerMeta.dataset.baselineStructure || 'classic',
			surface:    rawSurface || headerMeta.dataset.baselineSurface || 'glass',
			shape:      rawShape || headerMeta.dataset.baselineShape || 'full',
			width:      rawWidth || headerMeta.dataset.baselineWidth || 'contained',
			behavior:   rawBehavior || headerMeta.dataset.baselineBehavior || 'sticky',
			overlay:    rawOverlay === '1' ? true : ( rawOverlay === '0' ? false : headerMeta.dataset.baselineOverlay === '1' ),
			align:      headerVal( 'header_menu_align' ) || 'right',
			height:     parseInt( headerVal( 'header_height' ), 10 ) || 64,
			breakpoint: parseInt( headerVal( 'header_mobile_breakpoint' ), 10 ) || 900,
			topbar:     headerChecked( 'topbar_enable' ),
			topbarMob:  headerVal( 'topbar_mobile' ) !== '0',
			dock:       headerChecked( 'mobile_dock' ),
			brandDot:   headerChecked( 'brand_dot' )
		};
	}

	function syncHeaderControls() {
		if ( ! headerPane || ! headerMeta ) {
			return;
		}
		var searching = !!( search && search.value.trim() );
		var state     = effectiveHeaderState();

		headerPane.querySelectorAll( '.gnn-hs-card' ).forEach( function ( card ) {
			var radio = card.querySelector( 'input[type="radio"]' );
			card.classList.toggle( 'is-selected', !!( radio && radio.checked ) );
		} );

		var alignRow = headerPane.querySelector( '.gnn-setting-row[data-key="header_menu_align"]' );
		if ( alignRow && ! searching ) {
			alignRow.hidden = ! ( state.structure === 'classic' && state.shape === 'full' );
		}

		var overlayLogoRow = headerPane.querySelector( '.gnn-setting-row[data-key="logo_overlay"]' );
		if ( overlayLogoRow && ! searching ) {
			overlayLogoRow.hidden = ! state.overlay;
		}

		var ctaSub = headerPane.querySelector( '[data-he-sub="cta"]' );
		if ( ctaSub ) {
			ctaSub.hidden = ! ( headerChecked( 'header_cta_show' ) || searching );
		}

		var searchSub = headerPane.querySelector( '[data-he-sub="search"]' );
		if ( searchSub ) {
			searchSub.hidden = ! ( headerChecked( 'show_search' ) || searching );
		}

		var topbarFields = headerPane.querySelector( '[data-gnn-topbar-fields]' );
		if ( topbarFields ) {
			topbarFields.hidden = ! ( state.topbar || searching );
		}

		var mobileActions = headerPane.querySelector( '[data-gnn-mobile-actions]' );
		if ( mobileActions && ! searching ) {
			mobileActions.hidden = ! state.dock;
		}

		var centeredNote = headerPane.querySelector( '[data-gnn-centered-menu-note]' );
		if ( centeredNote ) {
			centeredNote.hidden = state.structure !== 'centered';
		}

		var sharedNotice = headerPane.querySelector( '[data-gnn-shared-header-notice]' );
		if ( sharedNotice ) {
			var hasShared = parseInt( headerVal( 'header_template' ), 10 ) > 0
				|| parseInt( headerVal( 'archive_header_template' ), 10 ) > 0
				|| parseInt( headerVal( 'post_header_template' ), 10 ) > 0;
			sharedNotice.hidden = ! hasShared;
		}

		updateHeaderPreview( state );
	}

	function updateMoveButtons() {
		if ( ! heEditor ) {
			return;
		}
		var items = Array.from( heEditor.querySelectorAll( '[data-he-item]' ) );
		items.forEach( function ( item, index ) {
			var up   = item.querySelector( '[data-he-move="up"]' );
			var down = item.querySelector( '[data-he-move="down"]' );
			if ( up ) {
				up.disabled = index === 0;
			}
			if ( down ) {
				down.disabled = index === items.length - 1;
			}
		} );
	}

	function serializeElements() {
		if ( ! heEditor || ! elementsTouched ) {
			return;
		}
		var payload = heEditor.querySelector( '#gnn-header_elements' );
		if ( ! payload ) {
			return;
		}
		var list = Array.from( heEditor.querySelectorAll( '[data-he-item]' ) ).map( function ( item ) {
			var desktop = item.querySelector( '[data-he-device="desktop"]' );
			var mobile  = item.querySelector( '[data-he-device="mobile"]' );
			return {
				id:      item.getAttribute( 'data-he-item' ),
				desktop: !!( desktop && desktop.checked ),
				mobile:  !!( mobile && mobile.checked )
			};
		} );
		if ( headerMeta && headerMeta.dataset.wooActive !== '1' && heEditor.dataset.cartPresent === '1' ) {
			var cartIndex = parseInt( heEditor.dataset.cartIndex, 10 );
			if ( isNaN( cartIndex ) || cartIndex < 0 ) {
				cartIndex = list.length;
			}
			list.splice( Math.min( cartIndex, list.length ), 0, {
				id:      'cart',
				desktop: heEditor.dataset.cartDesktop === '1',
				mobile:  heEditor.dataset.cartMobile === '1'
			} );
		}
		payload.value = JSON.stringify( list );
	}

	function validHex( value ) {
		return /^#(?:[0-9a-f]{3}|[0-9a-f]{6})$/i.test( ( value || '' ).trim() ) ? value.trim() : '';
	}

	function updateHeaderPreview( state ) {
		if ( ! hpRoot || ! state ) {
			return;
		}
		var stage = hpRoot.querySelector( '[data-hp-stage]' );
		if ( ! stage ) {
			return;
		}
		var modeInput = document.getElementById( 'gnn-default_mode' );
		var mode      = modeInput && modeInput.value === 'light' ? 'light' : ( hpRoot.dataset.mode || 'dark' );
		hpRoot.dataset.mode = mode;

		stage.dataset.structure    = state.structure;
		stage.dataset.surface      = state.surface;
		stage.dataset.shape        = state.shape;
		stage.dataset.width        = state.width;
		stage.dataset.align        = state.align;
		stage.dataset.overlay      = state.overlay ? '1' : '0';
		stage.dataset.topbar       = state.topbar ? '1' : '0';
		stage.dataset.topbarMobile = state.topbarMob ? '1' : '0';
		stage.dataset.dock         = state.dock ? '1' : '0';

		var scaledRow = Math.round( 34 + ( Math.max( 48, Math.min( 160, state.height ) ) - 48 ) * 0.25 );
		stage.style.setProperty( '--hp-row-h', scaledRow + 'px' );

		var bg     = validHex( headerVal( 'header_bg_' + mode ) );
		var fg     = validHex( headerVal( 'header_text_' + mode ) );
		var accent = validHex( headerVal( 'header_accent_' + mode ) );
		var tbBg   = validHex( headerVal( 'topbar_bg' ) );
		var tbFg   = validHex( headerVal( 'topbar_text_color' ) );

		stage.style.setProperty( '--hp-bg', bg || '' );
		stage.style.setProperty( '--hp-fg', fg || '' );
		stage.style.setProperty( '--hp-accent', accent || '' );
		stage.style.setProperty( '--hp-topbar-bg', tbBg || '' );
		stage.style.setProperty( '--hp-topbar-fg', tbFg || '' );

		var isMobile = hpRoot.dataset.device === 'mobile';
		var topbarEl = hpRoot.querySelector( '[data-hp-topbar]' );
		if ( topbarEl ) {
			topbarEl.hidden = ! state.topbar || ( isMobile && ! state.topbarMob );
		}
		var tbText = hpRoot.querySelector( '[data-hp-topbar-text]' );
		if ( tbText ) {
		tbText.textContent = headerVal( 'topbar_text' ).trim() || hpLabels.announcement;
		}
		var tbContact = hpRoot.querySelector( '[data-hp-topbar-contact]' );
		if ( tbContact ) {
			var hasContact = !! ( headerVal( 'topbar_phone' ).trim() || headerVal( 'topbar_email' ).trim() || headerVal( 'company_phone' ).trim() || headerVal( 'company_email' ).trim() );
			tbContact.hidden = ! hasContact;
		}

		var dotEl = hpRoot.querySelector( '[data-hp-dot]' );
		if ( dotEl ) {
			dotEl.hidden = ! state.brandDot;
		}

		var navRow = hpRoot.querySelector( '[data-hp-nav-row]' );
		if ( navRow ) {
			navRow.hidden = isMobile || ! ( state.structure === 'stacked' || state.structure === 'shop' );
		}

		var dockEl = hpRoot.querySelector( '[data-hp-dock]' );
		if ( dockEl ) {
			dockEl.hidden = ! ( isMobile && state.dock );
		}

		var deskBox = hpRoot.querySelector( '[data-hp-actions-desktop]' );
		var mobBox  = hpRoot.querySelector( '[data-hp-actions-mobile]' );
		if ( heEditor && deskBox && mobBox ) {
			deskBox.textContent = '';
			mobBox.textContent  = '';
			var ctaLabel = headerVal( 'header_cta_label' ).trim();
			heEditor.querySelectorAll( '[data-he-item]' ).forEach( function ( item ) {
				var id     = item.getAttribute( 'data-he-item' );
				var enable = item.querySelector( '[data-he-enable]' );
				var desk   = item.querySelector( '[data-he-device="desktop"]' );
				var mob    = item.querySelector( '[data-he-device="mobile"]' );
				var title  = item.querySelector( '.gnn-he-item__title' );
				var label  = ( id === 'cta' && ctaLabel ) ? ctaLabel : ( title ? title.textContent.trim() : id );
				if ( ! enable || ! enable.checked ) {
					return;
				}
				if ( desk && desk.checked ) {
					var dp = document.createElement( 'span' );
					dp.className = 'gnn-hp__pill gnn-hp__pill--' + id;
					dp.textContent = label;
					deskBox.appendChild( dp );
				}
				if ( mob && mob.checked ) {
					var mp = document.createElement( 'span' );
					mp.className = 'gnn-hp__pill gnn-hp__pill--' + id;
					mp.textContent = label;
					mobBox.appendChild( mp );
				}
			} );
		}

		var metaEl = hpRoot.querySelector( '[data-hp-meta]' );
		if ( metaEl ) {
			metaEl.textContent = isMobile
				? hpLabels.mobile.replace( /%(?:1\$)?s/, state.breakpoint )
				: [ hpLabels.structure[state.structure], hpLabels.surface[state.surface], hpLabels.shape[state.shape], state.height + ' px' ].join( ' \u00b7 ' );
		}
	}

	if ( heEditor ) {
		heEditor.addEventListener( 'click', function ( event ) {
			var btn = event.target.closest( '[data-he-move]' );
			if ( ! btn ) {
				return;
			}
			var item = btn.closest( '[data-he-item]' );
			var list = heEditor.querySelector( '[data-he-list]' );
			if ( ! item || ! list ) {
				return;
			}
			if ( btn.dataset.heMove === 'up' && item.previousElementSibling ) {
				list.insertBefore( item, item.previousElementSibling );
			} else if ( btn.dataset.heMove === 'down' && item.nextElementSibling ) {
				list.insertBefore( item.nextElementSibling, item );
			} else {
				return;
			}
			elementsTouched = true;
			serializeElements();
			updateMoveButtons();
			markDirty();
			updateHeaderPreview( effectiveHeaderState() );
			btn.focus();
		} );

		heEditor.addEventListener( 'change', function ( event ) {
			if ( event.target.matches( '[data-he-device]' ) ) {
				elementsTouched = true;
				serializeElements();
				markDirty();
				updateHeaderPreview( effectiveHeaderState() );
			}
		} );

		updateMoveButtons();
	}

	if ( hpRoot ) {
		hpRoot.addEventListener( 'click', function ( event ) {
			var btn = event.target.closest( '[data-hp-device]' );
			if ( ! btn ) {
				return;
			}
			hpRoot.dataset.device = btn.dataset.hpDevice;
			hpRoot.querySelectorAll( '[data-hp-device]' ).forEach( function ( b ) {
				var activeBtn = b === btn;
				b.classList.toggle( 'is-active', activeBtn );
				b.setAttribute( 'aria-pressed', String( activeBtn ) );
			} );
			updateHeaderPreview( effectiveHeaderState() );
		} );
	}

	if ( headerPane && headerMeta ) {
		headerPane.addEventListener( 'input', syncHeaderControls );
		headerPane.addEventListener( 'change', syncHeaderControls );
		var defaultModeInput = document.getElementById( 'gnn-default_mode' );
		if ( defaultModeInput ) {
			defaultModeInput.addEventListener( 'change', syncHeaderControls );
		}
		syncHeaderControls();
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
