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

 /* Full-color editable fields, using the bundled WordPress Iris picker. */
 if(window.jQuery&&jQuery.fn.wpColorPicker){
  jQuery('.gnn-color-picker').each(function(){
   var input=this,$input=jQuery(input),wrap,initialValue=input.value;
   function paint(value){
    if(!wrap)return;
    var color=/^#[0-9a-f]{6}$/i.test(value)?value:input.dataset.defaultColor;
    if(input.id==='gnn-accent_light'&&!input.value)color=document.getElementById('gnn-accent').value;
    if(!/^#[0-9a-f]{6}$/i.test(color))color='#ffffff';
    var channels=[1,3,5].map(function(i){var c=parseInt(color.slice(i,i+2),16)/255;return c<=.04045?c/12.92:Math.pow((c+.055)/1.055,2.4);});
    var luminance=.2126*channels[0]+.7152*channels[1]+.0722*channels[2];
    wrap.style.setProperty('--gnn-picker-color',color);
    wrap.style.setProperty('--gnn-picker-ink',luminance>.179?'#000000':'#ffffff');
    input.placeholder=color;
   }
   $input.wpColorPicker({palettes:false,width:220,change:function(event,ui){paint(ui.color.toString());if(wrap)markDirty();},clear:function(){paint('');if(wrap)markDirty();}});
   $input.iris('option','controls',{horiz:'s',vert:'v',strip:'h'});
   $input.val(initialValue);
   wrap=input.closest('.wp-picker-container');if(!wrap)return;
   wrap.classList.add('gnn-picker');
   var toggle=wrap.querySelector('.wp-color-result'),holder=wrap.querySelector('.wp-picker-holder'),reset=wrap.querySelector('.wp-picker-default,.wp-picker-clear');
   var label=input.getAttribute('aria-label')||Array.from(input.labels||[]).map(function(l){return l.textContent.trim();}).filter(Boolean).join(' — ');
   if(toggle){toggle.setAttribute('aria-label',(text.chooseColor||'Choose color')+' — '+label);toggle.querySelector('.wp-color-result-text').classList.add('screen-reader-text');var arrow=document.createElement('span');arrow.className='dashicons dashicons-arrow-down-alt2';arrow.setAttribute('aria-hidden','true');toggle.appendChild(arrow);}
   holder.querySelectorAll('.iris-square-value').forEach(function(control){control.setAttribute('aria-label',(text.chooseColor||'Choose color')+' — '+label);});
   if(reset){holder.appendChild(reset);jQuery(reset).on('click',function(){paint(input.value);input.dispatchEvent(new Event('change',{bubbles:true}));});}
   $input.on('input change',function(){paint(input.value);if(input.id==='gnn-accent'){var light=document.getElementById('gnn-accent_light');if(light&&!light.value)jQuery(light).triggerHandler('input');}});
   function showPicker(){if(!wrap.classList.contains('wp-picker-active'))$input.wpColorPicker('open');$input.iris('show');}
   input.addEventListener('click',showPicker);
   if(toggle)toggle.addEventListener('click',function(){if(wrap.classList.contains('wp-picker-active'))$input.iris('show');});
   wrap.addEventListener('keydown',function(event){if(event.key==='Escape'&&wrap.classList.contains('wp-picker-active')){$input.wpColorPicker('close');input.focus();event.stopPropagation();}});
   paint(input.value);
  });
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

 /* Keep dependent blog controls understandable without discarding saved values. */
 function syncBlogControls(){
  function value(key){var el=panel.querySelector('#gnn-'+key);return el?el.value:'';}
  function show(key,visible){var el=panel.querySelector('#gnn-'+key);if(!el)return;var field=el.closest('.gnn-field');if(field){field.classList.add('gnn-blog-conditional');field.hidden=!(visible||(search&&search.value.trim()));}}
  var layout=value('post_image_layout'),height=value('post_image_height_mode'),card=value('blog_card_image_mode');
  var image=layout!=='hidden';
  show('post_image_width',layout==='wide');show('post_image_height_mode',image);show('post_image_ratio',image&&height==='ratio');
  show('post_featured_image_height',image&&height==='fixed');show('post_featured_image_fit',image&&height!=='natural');show('post_featured_image_position',image&&height!=='natural');show('post_image_caption',image);
  show('blog_card_image_ratio',card==='ratio');show('blog_card_image_height',card==='fixed');show('blog_card_image_fit',card==='ratio'||card==='fixed');show('blog_card_image_position',card==='ratio'||card==='fixed');
  var author=panel.querySelector('#gnn-blog_author');if(author)show('post_author_box',author.checked);
 }
 panel.addEventListener('change',syncBlogControls);if(search)search.addEventListener('input',syncBlogControls);syncBlogControls();
} )();

/* Cleanup requires a reviewed row; disabled controls are progressive enhancement only. */
(function(){function ready(){document.querySelectorAll('form[data-gnn-cleanup]').forEach(function(form){var rows=Array.from(form.querySelectorAll('input[name^="cleanup["]')),button=form.querySelector('[type="submit"]');function sync(){button.disabled=!rows.some(function(row){return row.checked;});}form.addEventListener('change',sync);form.addEventListener('submit',function(event){if(!rows.some(function(row){return row.checked;})){event.preventDefault();form.querySelector('.gnn-cleanup-feedback').focus();}});var feedback=form.querySelector('.gnn-cleanup-feedback');if(feedback)feedback.tabIndex=-1;sync();});}if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',ready);else ready();})();
