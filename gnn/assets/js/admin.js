/**
 * GNN Panel — tabs, media pickers, and color pickers. Vanilla JS except
 * for the color picker init below, which uses WordPress core's own
 * wp-color-picker (jQuery + Iris, already loaded by wp_enqueue_style/
 * script( 'wp-color-picker' ) — see gnn_panel_assets()).
 */
( function () {
	'use strict';

	var panel = document.querySelector('.gnn-panel');
 if(!panel)return;
 var text=window.gnnAdminText||{};
 var tabs=Array.from(panel.querySelectorAll('.gnn-tabs .nav-tab'));
 var panes=Array.from(panel.querySelectorAll('.gnn-tab'));
 var form=panel.querySelector('form[action="options.php"]')||panel.querySelector('form:has(input[value="brand"])');
 var search=panel.querySelector('#gnn-settings-search');
 var feedback=panel.querySelector('.gnn-search-feedback');
 var active='global';
 var selections={};
 var dirty=false;
 var status=form&&form.querySelector('.gnn-save-status');
 if(form&&!status){status=document.createElement('p');status.className='gnn-save-status';status.setAttribute('role','status');form.prepend(status);}
 function bindEditors(){panel.querySelectorAll('.CodeMirror').forEach(function(el){if(el.CodeMirror&&!el.dataset.gnnDirtyBound){el.dataset.gnnDirtyBound='true';el.CodeMirror.on('change',markDirty);}});}
 function refreshEditors(pane){requestAnimationFrame(function(){bindEditors();pane.querySelectorAll('.CodeMirror').forEach(function(el){if(el.CodeMirror)el.CodeMirror.refresh();});});}
 window.addEventListener('load',bindEditors);
 function groupCards(pane){return Array.from(pane.querySelectorAll('.gnn-panel-grid>.gnn-settings-card'));}
 function showGroup(pane,key){
  selections[pane.id]=key;
  groupCards(pane).forEach(function(card){card.hidden=key!=='all'&&card.dataset.group!==key;});
  pane.querySelectorAll('.gnn-subtabs button').forEach(function(button){button.setAttribute('aria-pressed',String(button.dataset.group===key));});
  refreshEditors(pane);
 }
 panes.forEach(function(pane){
  var cards=groupCards(pane);
  if(!cards.length)return;
  var nav=document.createElement('div');nav.className='gnn-subtabs';nav.setAttribute('role','group');nav.setAttribute('aria-label',text.groupsLabel||'Setting groups');
  function add(label,key){var button=document.createElement('button');button.type='button';button.textContent=label;button.dataset.group=key;button.setAttribute('aria-pressed',String(key==='all'));button.addEventListener('click',function(){showGroup(pane,key);});nav.appendChild(button);}
  add(text.allGroups||'All groups','all');
  cards.forEach(function(card,i){var heading=card.querySelector('h3,.gnn-field__label');card.dataset.group=String(i);if(heading)add(heading.textContent.trim(),String(i));});
  var grid=pane.querySelector('.gnn-panel-grid');pane.insertBefore(nav,grid);selections[pane.id]='all';
 });
 if(tabs.length){
  tabs[0].parentNode.setAttribute('role','tablist');tabs[0].parentNode.setAttribute('aria-orientation','vertical');
  panel.querySelectorAll('.gnn-nav-group').forEach(function(el){el.setAttribute('role','presentation');});
  tabs.forEach(function(tab){tab.setAttribute('role','tab');tab.setAttribute('aria-controls','gnn-tab-'+tab.dataset.tab);});
  panes.forEach(function(pane){pane.setAttribute('role','tabpanel');pane.setAttribute('aria-labelledby','gnn-nav-'+pane.id.replace('gnn-tab-',''));});
 }
 function activate(id,remember){
  if(!panes.some(function(p){return p.id==='gnn-tab-'+id;}))id='global';
  active=id;
  tabs.forEach(function(tab){var selected=tab.dataset.tab===id;tab.classList.toggle('nav-tab-active',selected);tab.setAttribute('aria-selected',String(selected));tab.tabIndex=selected?0:-1;if(selected)tab.setAttribute('aria-current','page');else tab.removeAttribute('aria-current');});
  panes.forEach(function(pane){var selected=pane.id==='gnn-tab-'+id;pane.classList.toggle('is-active',selected);pane.hidden=!selected;if(selected)showGroup(pane,selections[pane.id]||'all');});
  panel.querySelectorAll('.gnn-top-save,.gnn-savebar').forEach(function(el){el.hidden=id==='advanced';});
  if(remember){history.replaceState(null,'','#gnn-tab-'+id);try{sessionStorage.setItem('gnn-panel-tab',id);}catch(e){/* Browser storage is optional. */}}
 }
 function applySearch(){
  if(!search)return;
  var term=search.value.trim().toLocaleLowerCase();
  panel.classList.toggle('gnn-searching',!!term);
  if(!term){if(feedback)feedback.hidden=true;panes.forEach(function(pane){pane.querySelectorAll('.is-search-match').forEach(function(el){el.classList.remove('is-search-match');});pane.querySelectorAll('.gnn-subtabs').forEach(function(nav){nav.hidden=false;});});activate(active,false);return;}
  var count=0;
  panes.forEach(function(pane){var visible=0;groupCards(pane).forEach(function(card){var match=card.textContent.toLocaleLowerCase().includes(term);card.hidden=!match;if(match){count++;visible++;}card.querySelectorAll('.gnn-field').forEach(function(field){field.classList.toggle('is-search-match',match&&field.textContent.toLocaleLowerCase().includes(term));});});pane.hidden=!visible;pane.querySelectorAll('.gnn-subtabs').forEach(function(nav){nav.hidden=true;});});
  if(feedback){feedback.hidden=false;feedback.textContent=count?(text.searchResults||'%d setting groups found').replace('%d',count):(text.noResults||'No settings match your search. Try another keyword.');}
  panel.querySelectorAll('.gnn-top-save,.gnn-savebar').forEach(function(el){el.hidden=false;});
 }
 tabs.forEach(function(tab,index){
  tab.addEventListener('click',function(event){event.preventDefault();if(search&&search.value){search.value='';applySearch();}activate(tab.dataset.tab,true);});
  tab.addEventListener('keydown',function(event){var next=index;if(event.key==='ArrowDown'||event.key==='ArrowRight')next=(index+1)%tabs.length;else if(event.key==='ArrowUp'||event.key==='ArrowLeft')next=(index+tabs.length-1)%tabs.length;else if(event.key==='Home')next=0;else if(event.key==='End')next=tabs.length-1;else return;event.preventDefault();tabs[next].click();tabs[next].focus();});
 });
 var initial=location.hash.replace('#gnn-tab-','');
 if(!initial){try{initial=sessionStorage.getItem('gnn-panel-tab')||'global';}catch(e){initial='global';}}
 // Add enhanced visibility only after navigation and group controls are initialized.
 if(tabs.length){panel.classList.add('gnn-admin-ready');activate(initial,false);}
 window.addEventListener('hashchange',function(){if(tabs.length)activate(location.hash.replace('#gnn-tab-',''),false);});
 if(search)search.addEventListener('input',applySearch);
 if(text.invalidField){var invalid=panel.querySelector('#gnn-'+text.invalidField);if(invalid){var invalidPane=invalid.closest('.gnn-tab');if(invalidPane){activate(invalidPane.id.replace('gnn-tab-',''),true);showGroup(invalidPane,'all');}invalid.setAttribute('aria-invalid','true');invalid.focus();invalid.scrollIntoView({block:'center'});}}
 function markDirty(){dirty=true;if(status)status.textContent=text.unsaved||'Unsaved changes';var bar=form&&form.querySelector('.gnn-savebar');if(bar)bar.classList.add('is-dirty');}
 if(form){
  if(status)status.textContent=text.ready||'No pending changes';
  form.addEventListener('input',markDirty);form.addEventListener('change',markDirty);
  // Core's Iris picker emits jQuery changes rather than native DOM change events.
  if(window.jQuery)jQuery(form).on('change','.gnn-color-picker',markDirty);
  form.addEventListener('submit',function(){
   form.querySelectorAll('.CodeMirror').forEach(function(el){if(el.CodeMirror)el.CodeMirror.save();});
   form.querySelectorAll('.gnn-action-editor').forEach(function(editor){var items=[];editor.querySelectorAll('.gnn-action-row').forEach(function(row){var label=row.querySelector('[data-action-label]').value.trim();if(label)items.push({type:row.querySelector('[data-action-type]').value,label:label,url:row.querySelector('[data-action-url]').value});});editor.querySelector('[data-action-payload]').value=JSON.stringify(items);});
   dirty=false;if(status)status.textContent=text.saving||'Saving settings…';form.setAttribute('aria-busy','true');
  });
  window.addEventListener('beforeunload',function(event){if(dirty){event.preventDefault();event.returnValue='';}});
 }
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
                document.getElementById( 'gnn-media-' + target ).dispatchEvent(new Event('change',{bubbles:true}));
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
            document.getElementById( 'gnn-media-' + t ).dispatchEvent(new Event('change',{bubbles:true}));
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
