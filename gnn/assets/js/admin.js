/**
 * GNN Panel — tabs, media pickers, and color pickers. Vanilla JS except
 * for the color picker init below, which uses WordPress core's own
 * wp-color-picker (jQuery + Iris, already loaded by wp_enqueue_style/
 * script( 'wp-color-picker' ) — see gnn_panel_assets()).
 */
( function () {
	'use strict';

	var panel = document.querySelector('.gnn-panel');
	if (panel) panel.classList.add('gnn-admin-ready');
	/* Tabs (hash-persistent). */
	var tabs = document.querySelectorAll( '.gnn-tabs .nav-tab' );
	var panes = document.querySelectorAll( '.gnn-tab' );

	function activate( id ) {
		tabs.forEach( function ( t ) {
			t.classList.toggle( 'nav-tab-active', t.dataset.tab === id );
			if(t.dataset.tab === id) t.setAttribute('aria-current','page'); else t.removeAttribute('aria-current');
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
 if(window.gnnAdminText && gnnAdminText.invalidField){
  var invalid=document.getElementById('gnn-'+gnnAdminText.invalidField);
  if(invalid){var invalidPane=invalid.closest('.gnn-tab');if(invalidPane)activate(invalidPane.id.replace('gnn-tab-',''));invalid.setAttribute('aria-invalid','true');invalid.focus();invalid.scrollIntoView({block:'center'});}
 }


	var form = document.querySelector('form[action="options.php"]') || document.querySelector('form:has(input[value="brand"])');
	var dirty = false;
	if(form){
		var status = document.createElement('p'); status.className='gnn-save-status';status.setAttribute('role','status');form.prepend(status);
		form.addEventListener('input',function(){dirty=true;status.textContent=window.gnnAdminText ? gnnAdminText.unsaved : 'Unsaved changes';});
		form.addEventListener('change',function(){dirty=true;});
		form.addEventListener('submit',function(){
			document.querySelectorAll('.gnn-action-editor').forEach(function(editor){var items=[];editor.querySelectorAll('.gnn-action-row').forEach(function(row){var label=row.querySelector('[data-action-label]').value.trim();if(label)items.push({type:row.querySelector('[data-action-type]').value,label:label,url:row.querySelector('[data-action-url]').value});});editor.querySelector('[data-action-payload]').value=JSON.stringify(items);});
			dirty=false;
		});
		window.addEventListener('beforeunload',function(event){if(dirty){event.preventDefault();event.returnValue='';}});
	}
	var search=document.getElementById('gnn-settings-search');
	if(search)search.addEventListener('input',function(){var term=search.value.trim().toLowerCase();document.querySelectorAll('.gnn-field').forEach(function(field){field.hidden=!!term&&!field.textContent.toLowerCase().includes(term);});if(panel)panel.classList.toggle('gnn-searching',!!term);});
	/* Media pickers. */
	document.addEventListener( 'click', function ( e ) {
		var font = e.target.closest('.gnn-font-pick');
		if (font && window.wp && wp.media) {
			e.preventDefault();
			var fonts = wp.media({title:font.textContent,multiple:false,library:{type:'font/woff2'}});
			fonts.on('select',function(){var input=document.getElementById(font.dataset.target);input.value=fonts.state().get('selection').first().toJSON().id;input.dispatchEvent(new Event('change',{bubbles:true}));});
			fonts.open();return;
		}
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
