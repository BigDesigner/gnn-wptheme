/* Progressive enhancement: the editable portfolio remains readable without JavaScript. */
(function(){
	'use strict';
	document.querySelectorAll('.gnn-architecture').forEach(function(root){
		var filters=root.querySelector('.gnn-arch-filters');
		var grid=root.querySelector('.gnn-arch-projects');
		if(!filters||!grid)return;
		var links=Array.from(filters.querySelectorAll('.wp-block-button__link')).filter(function(link){return /^#architecture-(all|residential|commercial|cultural)$/.test(link.getAttribute('href')||'');});
        filters.setAttribute('role','group');filters.setAttribute('aria-label',(window.gnnArchitecture||{}).filters||'Project categories');
        if(!links.length){filters.classList.add('is-ready');return;}
		var cards=Array.from(grid.querySelectorAll('.gnn-arch-project'));
		var status=document.createElement('p');status.className='gnn-arch-filter-status';status.setAttribute('role','status');filters.after(status);
		function choose(link){
			var key=link.hash.replace('#architecture-','');
			if(!['all','residential','commercial','cultural'].includes(key))return;
			var count=0;cards.forEach(function(card){var visible=key==='all'||card.classList.contains('gnn-arch-project--'+key);card.hidden=!visible;if(visible)count++;});
			links.forEach(function(item){item.setAttribute('aria-pressed',String(item===link));});
			var text=window.gnnArchitecture||{};status.textContent=count===1?(text.one||'1 project shown'):(text.many||'%d projects shown').replace('%d',count);
		}
		links.forEach(function(link){link.setAttribute('role','button');if(grid.id)link.setAttribute('aria-controls',grid.id);link.addEventListener('click',function(event){event.preventDefault();choose(link);});link.addEventListener('keydown',function(event){if(event.key===' '){event.preventDefault();choose(link);}});});
		filters.classList.add('is-ready');if(links.length)choose(links[0]);
	});
})();
