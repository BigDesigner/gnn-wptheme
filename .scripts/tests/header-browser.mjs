import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';

function recorder() {
 const tests=[];
 const record=async(name,run)=>{
  try { await run(); tests.push({name,pass:true}); }
  catch(error) { tests.push({name,pass:false,detail:String(error.stack || error)}); console.log('FAILED:',name,String(error)); }
 };
 return {tests,record};
}

async function session(browser,server,options={}) {
 const context=await browser.newContext({viewport:{width:1440,height:900},reducedMotion:'reduce',...options});
 // Isolate local QA from remote fonts, feeds and assets.
 await context.route('**/*',route=>route.request().url().startsWith(server.serverUrl)
  ? route.continue() : route.fulfill({status:204}));
 const errors=[];
 context.on('page',page=>{
  page.setDefaultTimeout(15000);
  page.on('console',message=>{if(message.type()==='error') errors.push(message.text());});
  page.on('pageerror',error=>errors.push(String(error)));
 });
 return {context,errors};
}

const readWarnings=php=>php("echo base64_encode(is_file(WP_DEBUG_LOG) ? file_get_contents(WP_DEBUG_LOG) : '');");
const readSettings=php=>php("echo base64_encode(serialize(get_option('gnn_options')));");
const phpData=value=> "json_decode(base64_decode('"+Buffer.from(JSON.stringify(value)).toString('base64')+"'),true)";
const writeSettings=(php,settings)=>php("update_option('gnn_options',"+phpData(settings)+");");

async function login(page,server,php) {
 await php("wp_set_password('gnn-qa-local-only',1);");
 const admin=server.serverUrl+'/wp-admin/';
 await page.goto(server.serverUrl+'/wp-login.php?redirect_to='+encodeURIComponent(admin));
 await page.locator('#user_login').fill('admin');
 await page.locator('#user_pass').fill('gnn-qa-local-only');
 await Promise.all([page.waitForURL('**/wp-admin/**',{waitUntil:'domcontentloaded',timeout:60000}),page.locator('#wp-submit').click({noWaitAfter:true})]);
}

async function checkWarnings(record,php,before,name) {
 await record(name,async()=>{
  const old=Buffer.from(before,'base64').toString();
  const current=Buffer.from(await readWarnings(php),'base64').toString();
  const warnings=current.slice(old.length).split('\n').filter(line=>/PHP (?:Warning|Notice|Deprecated|Fatal|Parse error):/i.test(line));
  assert.deepEqual(warnings,[]);
 });
}

export async function testHeaderSaves({browser,server,php,out},woo) {
 const {tests,record}=recorder();
 const state=await php("echo base64_encode(serialize(get_option('gnn_options')));");
 const locale=await php("echo base64_encode(get_user_meta(1,'locale',true));update_user_meta(1,'locale','en_US');");
 const debug=await readWarnings(php);
 const mode=woo?'with WooCommerce':'without WooCommerce';
 const {context,errors}=await session(browser,server);
 try {
  assert.equal(await php("echo class_exists('WooCommerce')?'yes':'no';"),woo?'yes':'no');
  const page=await context.newPage();
  await login(page,server,php);
  const panelURL=server.serverUrl+'/wp-admin/admin.php?page=gnn-panel#gnn-tab-header';
  const open=async()=>{
   await page.goto('about:blank');
   await page.goto(panelURL);
   await page.locator('#gnn-nav-header').click();
  };
  const save=async()=>{
   await Promise.all([page.waitForURL(url=>url.searchParams.has('settings-updated'),{timeout:60000}),page.locator('#gnn-save-header').click({noWaitAfter:true})]);
   assert.equal(await page.locator('.notice-error.settings-error').count(),0);
  };
  for(const [label,options] of [
   ['fresh',{}],
   ['legacy',{header_preset:'legacy',header_layout:'centered',sticky_header:0,header_search_bar:1,show_search:0,legacy_unknown:'keep'}],
   ['custom',{header_structure:'shop',header_surface:'solid',header_shape:'floating',header_behavior:'hide',header_overlay:0,header_height:85,
    header_search_scope:'products',header_search_mode:'overlay',show_search:1,show_cart:1,topbar_enable:1,header_bg_dark:'#123456',
    header_elements:'[ {"id":"cart","desktop":false,"mobile":true}, {"id":"search","desktop":true,"mobile":false} ]'}]
  ]) {
   await record('Header unchanged Settings API save preserves '+label+' options '+mode,async()=>{
    if(label==='fresh') await php("delete_option('gnn_options');");
    else await writeSettings(php,options);
    const before=await readSettings(php);
    await open();
    const posted=page.waitForRequest(request=>request.method()==='POST' && request.url().endsWith('/options.php'));
    await save();
    assert.equal(new URLSearchParams((await posted).postData()).get('gnn_form_scope'),'header-unchanged');
    assert.equal(await readSettings(php),before);
   });
  }
  if(woo || process.env.GNN_QA_MINIMUM==='1') {
   await record('Header panel conditions, preview, search, dirty state and saved order',async()=>{
    await writeSettings(php,{});
    await open();
    const pane=page.locator('#gnn-tab-header');
    const align=pane.locator('[data-key="header_menu_align"]');
    const select=(key,value)=>pane.locator('#gnn-'+key).selectOption(value);
    const structure=value=>pane.locator('label[for="gnn-header_structure-'+value+'"]').click();
    assert.match(await pane.locator('.gnn-hs-label').first().innerText(),/^Default \(.+\)$/);
    assert.match(await pane.locator('#gnn-header_shape option[value=""]').innerText(),/^Default \(.+\)$/);
    assert.equal(await align.isVisible(),true);
   assert.ok((await align.locator('.gnn-diagram-align--left > i').first().boundingBox()).width>0);
    await structure('stacked');
    assert.equal(await align.isVisible(),false);
    await select('header_overlay','1');
    assert.equal(await pane.locator('[data-key="logo_overlay"]').isVisible(),true);
    await select('header_overlay','0');
    assert.equal(await pane.locator('[data-key="logo_overlay"]').isVisible(),false);
    await pane.locator('[data-hp-device="mobile"]').click();
    assert.equal(await pane.locator('[data-hp-meta]').innerText(),'Mobile < 900 px');
    await pane.locator('[data-hp-device="desktop"]').click();
    assert.match(await pane.locator('[data-hp-meta]').innerText(),/^Stacked · Glass · Full · /);
    const search=page.locator('#gnn-settings-search');
    await search.fill('Mobile breakpoint');
    await pane.locator('#gnn-header_mobile_breakpoint').waitFor();
    await search.clear();
    await align.waitFor({state:'hidden'});
    const item=pane.locator('[data-he-item="search"]');
    await item.locator('[data-he-move="up"]').click();
    const order=await pane.locator('[data-he-item]').evaluateAll(elements=>elements.map(element=>element.dataset.heItem));
    assert.ok(order.indexOf('search')<order.indexOf('cta'));
    assert.equal(await page.locator('.gnn-savebar').evaluate(el=>el.classList.contains('is-dirty')),true);
    await save();
    const stored=JSON.parse(await php("echo wp_json_encode(gnn_option('header_elements'));"));
    assert.ok(JSON.parse(stored).findIndex(item=>item.id==='search')<JSON.parse(stored).findIndex(item=>item.id==='cta'));
    assert.equal(await php("echo gnn_option('header_structure');"),'stacked');
   });
   if(process.env.GNN_QA_PANEL_SHOTS==='1') {
    const shots=path.join(out,'panel');
    fs.mkdirSync(shots,{recursive:true});
    for(const width of [1440,1024,390]) {
     await page.setViewportSize({width,height:900});
     await open();
     const height=await page.evaluate(async()=>{
      document.querySelectorAll('img[loading="lazy"]').forEach(image=>{image.loading='eager';});
      await document.fonts.ready;
      return document.documentElement.scrollHeight;
     });
     await page.setViewportSize({width,height:Math.min(height,16000)});
     await page.evaluate(()=>scrollTo({top:0,behavior:'instant'}));
     await page.screenshot({path:path.join(shots,width+'.png'),fullPage:true});
    }
   }
   await record('Header preview and default labels are localized in Turkish',async()=>{
    await php("update_user_meta(1,'locale','tr_TR');");
    await open();
    const preview=page.locator('[data-gnn-header-preview]');
    assert.match(await page.locator('#gnn-tab-header .gnn-hs-label').first().innerText(),/^Varsayılan \(.+\)$/);
    assert.equal(await preview.locator('[data-hp-topbar-text]').textContent(),'Duyuru çubuğu');
    await preview.locator('[data-hp-device="mobile"]').click();
    assert.equal(await preview.locator('[data-hp-meta]').innerText(),'Mobil < 900 px');
   });
  }
  await record('Header settings browser errors '+mode,()=>assert.deepEqual(errors,[]));
  await checkWarnings(record,php,debug,'Header settings PHP diagnostics '+mode);
 } finally {
  await context.close();
  await php("$options=unserialize(base64_decode('"+state+"')); if(false===$options){delete_option('gnn_options');}else{update_option('gnn_options',$options);} update_user_meta(1,'locale',base64_decode('"+locale+"'));");
 }
 return tests;
}

const structures=['classic','centered','stacked','shop'];
const surfaces=['glass','solid','transparent'];
const shapes=['full','floating'];
const behaviors=['static','sticky','hide','shrink'];
const widths=[1440,1024,768,390,320];
const matrix=structures.flatMap((structure,i)=>behaviors.map((behavior,j)=>({
 header_structure:structure,header_surface:surfaces[(i+j)%3],header_shape:shapes[(i+Math.floor(j/2))%2],header_behavior:behavior
})));
const testOptions={
 google_material_icons:0,loading_screen:0,scroll_anim:0,header_mobile_breakpoint:900,header_height:82,header_cta_show:1,
 header_cta_label:'Book a consultation',header_cta_url:'/contact/',header_contact:1,header_language:1,show_search:1,
 show_toggle:1,show_cart:1,header_search_mode:'overlay',company_phone:'+1 555 0100',company_email:'header@example.test',
 topbar_enable:1,topbar_text:'A header announcement',mobile_dock:1,mobile_actions:'[{"type":"link","label":"Home","url":"/"}]'
};

const fixturePhp=`
wp_set_current_user(1);
$menu=wp_create_nav_menu('Header browser QA');
$add=static function($title,$parent=0,$classes='') use($menu) {
 return wp_update_nav_menu_item($menu,0,array('menu-item-title'=>$title,'menu-item-url'=>home_url('/#qa'),'menu-item-parent-id'=>$parent,
 'menu-item-classes'=>$classes,'menu-item-status'=>'publish'));
};
$mega=$add('Services',0,'mega-menu-parent');
$column=$add('Solutions',$mega);
$add('Consulting',$column);
$featured=$add('Featured work',$mega,'mega-featured');
$add('News');
$edge=$add('Resources');
$nested=$add('Documentation',$edge);
$add('Third-level article',$nested);
$plain=wp_insert_post(array('post_type'=>'page','post_status'=>'publish','post_title'=>'Header QA without a hero',
 'post_content'=>str_repeat('<p>Header verification content.</p>',90)));
$hero=wp_insert_post(array('post_type'=>'page','post_status'=>'publish','post_title'=>'Header QA hero',
 'post_content'=>'<div class="gnn-header-overlay-target"><h2>First hero</h2></div>'.str_repeat('<p>Header verification content.</p>',90)));
update_post_meta($hero,'_gnn_layout',array('titlebar_template'=>-2));
update_post_meta($hero,'_gnn_hide_title','1');
update_post_meta($hero,'_gnn_hide_breadcrumb','1');
set_theme_mod('nav_menu_locations',array('primary'=>$menu));
update_option('blogname',str_repeat('A very long newspaper name ',18));
echo wp_json_encode(compact('menu','plain','hero','edge','nested','featured'));
`;

export async function testHeaderBrowser({browser,server,php}) {
 const {tests,record}=recorder();
 const {context,errors}=await session(browser,server);
 const snapshot=await php("echo base64_encode(serialize(array(get_option('gnn_options'),get_theme_mods(),get_option('blogname'),get_option('WPLANG'))));");
 const debug=await readWarnings(php);
 let ids;
 try{
  ids=JSON.parse(await php(fixturePhp));
  const page=await context.newPage();
  const mast=page.locator('#masthead');
  const shell=page.locator('[data-gnn-header-shell]');
  const toggle=mast.locator('.menu-toggle');
  const panel=page.locator('#mobile-navigation');
  const overflow=()=>page.evaluate(()=>document.documentElement.scrollWidth-innerWidth);
  async function load(config={},width=1440,id=ids.plain,scheme='light') {
   await writeSettings(php,{...testOptions,...config});
   await page.setViewportSize({width,height:860});
   await page.emulateMedia({colorScheme:scheme});
   await page.goto(server.serverUrl+'/?page_id='+id);
   await shell.locator('[data-gnn-header][data-ready]').waitFor();
   await page.evaluate(()=>document.fonts.ready);
  }
  for(const config of matrix) {
   const label=Object.values(config).join('/');
   for(const width of widths) {
    await record('Header matrix '+label+' at '+width+' px',async()=>{
     if(width===widths[0]) await load(config,width);
     else await page.setViewportSize({width,height:860});
     await page.evaluate(()=>new Promise(resolve=>requestAnimationFrame(()=>requestAnimationFrame(resolve))));
     assert.equal(await mast.getAttribute('data-surface'),config.header_surface);
     assert.equal(await mast.getAttribute('data-shape'),config.header_shape);
     assert.equal(await shell.getAttribute('data-behavior'),config.header_behavior);
     assert.match(await mast.getAttribute('class'),new RegExp('gnn-header-structure-'+config.header_structure));
     assert.equal(await mast.locator('.gnn-header-nav-row').count(),['stacked','shop'].includes(config.header_structure)?1:0);
     assert.ok(await overflow()<=0);
     if(width<900) {
      assert.equal(await toggle.isVisible(),true);
      const brand=await mast.locator('.site-branding').boundingBox();
      const button=await toggle.boundingBox();
      assert.ok(brand && button && brand.y<button.y+button.height && button.y<brand.y+brand.height);
      await toggle.click();
      assert.equal(await panel.getByRole('link',{name:'News',exact:true}).isVisible(),true);
      assert.ok(await overflow()<=0);
      await page.keyboard.press('Escape');
     } else {
      assert.ok(await mast.getByRole('link',{name:'News',exact:true}).isVisible() || await mast.locator('.gnn-menu-more:visible').count() || await toggle.isVisible());
     }
    });
   }
  }
  await record('Header desktop search icon and CTA fit beside the installed menu',async()=>{
   await php("wp_set_current_user(1); update_option('blogname','Header QA');");
   await load({header_search_mode:'link'},1440);
   assert.equal(await toggle.isVisible(),false);
   assert.equal(await mast.locator('.gnn-header-row .header-search-link').isVisible(),true);
   assert.equal(await mast.locator('.gnn-header-row .header-cta').isVisible(),true);
   assert.equal(await mast.getByRole('link',{name:'News',exact:true}).isVisible(),true);
  });
  await record('Header tablet CTA stays in the row when it fits and mobile visibility permits it',async()=>{
   for(const structure of structures) {
    await load({header_structure:structure},768);
    assert.equal(await mast.locator('.gnn-header-row .header-cta').isVisible(),true,structure);
    assert.equal(await toggle.isVisible(),true,structure);
    assert.ok(await overflow()<=0,structure);
   }
   await load({header_cta_label:'An extraordinarily long consultation button label '.repeat(14)},768);
   assert.equal(await mast.locator('.gnn-header-row .header-cta').isVisible(),false);
   assert.ok(await overflow()<=0);
   await load({header_elements:'[{"id":"cta","desktop":true,"mobile":false}]'},768);
   assert.equal(await mast.locator('.gnn-header-row .header-cta').isVisible(),false);
  });
  await record('Header aligns the stacked navigation row and switches at the configured breakpoint',async()=>{
   await load({header_structure:'stacked',header_menu_align:'right',header_mobile_breakpoint:900,header_cta_show:0,header_contact:0},900);
   const nav=mast.locator('.gnn-header-nav-row');
   assert.equal(await nav.isVisible(),true);
   assert.equal(await toggle.isVisible(),false);
   assert.equal(await nav.locator('.menu').evaluate(el=>getComputedStyle(el).justifyContent),'center');
   const header=await mast.boundingBox();
   const row=await mast.locator('.gnn-header-row').boundingBox();
   assert.equal(Math.round(row.height),82);
   assert.ok(header.height>=row.height+44);
   await page.setViewportSize({width:899,height:860});
   await toggle.waitFor();
   assert.equal(await nav.isVisible(),false);
  });
  await record('Header desktop mega and edge dropdowns include a reachable third level',async()=>{
   await php("wp_set_current_user(1); update_option('blogname','Header QA');");
   await load({header_width:'wide',header_structure:'stacked',header_shape:'floating',header_contact:0});
   const services=mast.locator('.main-navigation .mega-menu-parent > .gnn-submenu-toggle');
   await services.waitFor();
   await services.focus();
   await page.keyboard.press('Enter');
   const mega=await mast.locator('.main-navigation .mega-menu').boundingBox();
   const header=await mast.boundingBox();
   assert.ok(mega && Math.abs(mega.y-header.y-header.height)<=2,JSON.stringify({mega,header}));
   assert.equal(await mast.getByRole('link',{name:'Consulting'}).isVisible(),true);
   await page.keyboard.press('Escape');
   await load({header_menu_align:'right',header_width:'wide',header_cta_show:0,header_contact:0,show_cart:0,show_search:0,show_toggle:0});
   const edge=mast.locator('#menu-item-'+ids.edge+' > .gnn-submenu-toggle');
   const nested=mast.locator('#menu-item-'+ids.nested+' > .gnn-submenu-toggle');
   await edge.focus();
   await page.keyboard.press('Enter');
   assert.equal(await edge.evaluate(el=>el.parentElement.classList.contains('gnn-flip')),true);
   await nested.focus();
   await page.keyboard.press('Enter');
   for(const button of [edge,nested]) {
    const submenu=page.locator('#'+await button.getAttribute('aria-controls'));
    const bounds=await submenu.boundingBox();
    assert.ok(bounds && bounds.x>=-1 && bounds.x+bounds.width<=1441,JSON.stringify(bounds));
   }
   assert.equal(await mast.getByRole('link',{name:'Third-level article'}).isVisible(),true);
  });
  await record('Header desktop touch disclosure opens branches without following their links',async()=>{
   await writeSettings(php,{...testOptions,header_structure:'classic'});
   const {context:touch,errors:touchErrors}=await session(browser,server,{hasTouch:true});
   try{
    const touchPage=await touch.newPage();
    await touchPage.goto(server.serverUrl+'/?page_id='+ids.plain);
    const button=touchPage.locator('#menu-item-'+ids.edge+' > button');
    await button.tap();
    assert.equal(await button.getAttribute('aria-expanded'),'true');
    assert.equal(await touchPage.evaluate(()=>location.hash),'');
    const controls=await touchPage.locator('[data-gnn-header] .gnn-submenu-toggle[aria-controls]').evaluateAll(elements=>elements.map(el=>el.getAttribute('aria-controls')));
    assert.equal(controls.length,new Set(controls).size);
    for(const id of controls) assert.equal(await touchPage.locator('#'+id).count(),1);
    await button.tap();
    assert.equal(await button.getAttribute('aria-expanded'),'false');
   }finally{errors.push(...touchErrors); await touch.close();}
  });
  await record('Header More preserves overflowed branches and Collapse shows the mobile menu',async()=>{
   await php("for($i=0;$i<12;$i++){wp_update_nav_menu_item("+ids.menu+",0,array('menu-item-title'=>'Additional resource number '.$i,'menu-item-url'=>home_url('/#qa'),'menu-item-status'=>'publish'));}");
   await load({header_overflow:'more'},1024);
   const more=mast.locator('.gnn-menu-more > button');
   await more.waitFor();
   await more.focus();
   await page.keyboard.press('Enter');
   assert.equal(await mast.getByRole('link',{name:'Additional resource number 11',exact:true}).isVisible(),true);
   assert.ok(await overflow()<=0);
   await load({header_overflow:'collapse'},1024);
   await toggle.click();
   assert.equal(await panel.getByRole('link',{name:'Additional resource number 11',exact:true}).isVisible(),true);
  });
  await record('Header static mobile drawer locks scrolling, manages accordion and focus, and dismisses',async()=>{
   await load({header_behavior:'static'},390);
   await toggle.click();
   const close=panel.locator('[data-gnn-menu-close]');
   assert.ok(await close.getAttribute('aria-label'));
   assert.equal(await close.locator('svg[aria-hidden="true"]').count(),1);
   const iconButton=await close.boundingBox();
   assert.ok(iconButton.width>=44 && iconButton.height>=44);
   assert.equal(await close.evaluate(el=>el===document.activeElement),true);
   assert.equal(await page.evaluate(()=>document.documentElement.classList.contains('mobile-nav-open')),true);
   assert.equal(await page.evaluate(()=>getComputedStyle(document.documentElement).overflow),'hidden');
   const branch=panel.locator('.gnn-submenu-toggle').first();
   assert.equal(await branch.getAttribute('aria-expanded'),'false');
   await branch.click();
   assert.equal(await branch.getAttribute('aria-expanded'),'true');
   assert.equal(await panel.getByRole('link',{name:'Featured work'}).isVisible(),true);
   for(const selector of ['.header-cta','.header-contact','.header-search-link','.theme-toggle']) {
    assert.equal(await panel.locator(selector).isVisible(),true,selector+' should remain available with a non-duplicate dock');
   }
   const bounds=await panel.boundingBox();
   const header=await mast.boundingBox();
   assert.ok(Math.abs(bounds.y-header.y-header.height)<=2 && bounds.y+bounds.height<=861);
   await close.focus();
   await page.keyboard.press('Shift+Tab');
   assert.equal(await panel.evaluate(el=>el.contains(document.activeElement)),true);
   await page.keyboard.press('Tab');
   assert.equal(await close.evaluate(el=>el===document.activeElement),true);
   await page.keyboard.press('Escape');
   assert.equal(await toggle.evaluate(el=>el===document.activeElement),true);
   assert.equal(await panel.isVisible(),false);
   await toggle.click();
   await page.mouse.click(10,10);
   assert.equal(await panel.isVisible(),false);
   await toggle.click();
   await close.click();
   assert.equal(await panel.isVisible(),false);
  });
  await record('Header search overlay auto-focuses and Escape returns focus',async()=>{
   await load({},390);
   await toggle.click();
   await panel.locator('[data-gnn-search]').click();
   const dialog=page.locator('.gnn-search-dialog');
   assert.equal(await dialog.evaluate(el=>el.open),true);
   assert.equal(await dialog.locator('input[name="s"]').evaluate(el=>el===document.activeElement),true);
   await page.keyboard.press('Escape');
   assert.equal(await dialog.evaluate(el=>el.open),false);
   await page.waitForFunction(()=>document.activeElement.matches('#masthead .menu-toggle'));
  });
  for(const behavior of behaviors) {
   await record('Header scroll behavior '+behavior,async()=>{
    await load({header_behavior:behavior},1440);
    const before=await mast.boundingBox();
    await page.evaluate(()=>scrollTo(0,450));
    await page.waitForFunction(()=>document.querySelector('[data-gnn-header-shell]').classList.contains('is-scrolled'));
    const after=await mast.boundingBox();
    if(behavior==='static') assert.ok(after.y<0);
    if(behavior==='sticky') assert.ok(Math.abs(after.y-before.y)<=1);
    if(behavior==='shrink') assert.ok(after.height<before.height);
    if(behavior==='hide') {
     assert.match(await shell.getAttribute('class'),/is-hidden/);
     await page.evaluate(()=>scrollTo(0,250));
     await page.waitForFunction(()=>!document.querySelector('[data-gnn-header-shell]').classList.contains('is-hidden'));
    }
   });
  }
  await record('Header overlays a first hero, solidifies on scroll and stays in flow without a hero',async()=>{
   const config={header_surface:'transparent',header_overlay:1,header_behavior:'sticky'};
   await load(config,1440,ids.hero);
   assert.equal(await shell.getAttribute('data-overlay-active'),'');
   await page.evaluate(()=>scrollTo(0,200));
   await page.waitForFunction(()=>document.querySelector('[data-overlay-active]').classList.contains('is-scrolled'));
   await load(config,1440,ids.plain);
   assert.equal(await shell.getAttribute('data-overlay-active'),null);
   await load({...config,header_overlay:0},1440,ids.hero);
   assert.equal(await shell.getAttribute('data-overlay-active'),null);
  });
  await record('Header floating overlay respects the first hero and explicit Off',async()=>{
   const floating={header_shape:'floating',header_overlay:'',header_behavior:'static'};
   await load(floating,390,ids.hero);
   assert.equal(await shell.getAttribute('data-floating-overlay'),'');
   assert.ok(await shell.evaluate(el=>parseFloat(getComputedStyle(el).marginBlockEnd))<0);
   await toggle.click();
   const drawer=await panel.boundingBox();
   assert.ok(drawer.y+drawer.height<=861);
   await load(floating,1440,ids.plain);
   assert.equal(await shell.getAttribute('data-floating-overlay'),null);
   await load({...floating,header_overlay:0},1440,ids.hero);
   assert.equal(await shell.getAttribute('data-floating-overlay'),null);
   assert.equal(await shell.evaluate(el=>parseFloat(getComputedStyle(el).marginBlockEnd)),0);
  });
  await record('Header Agency legacy overlay is inherited and explicit Off wins',async()=>{
   const base={...testOptions,site_segment:'gnnagency',header_preset:'minimal',topbar_enable:0};
   await page.setViewportSize({width:1440,height:860});
   for(const overlay of ['',0]) {
    await writeSettings(php,{...base,header_overlay:overlay});
    await page.goto(server.serverUrl);
    assert.equal(await page.locator('body').evaluate(el=>el.classList.contains('gnn-site-gnnagency')),true);
    const margin=await shell.evaluate(el=>parseFloat(getComputedStyle(el).marginBlockEnd));
    if(overlay==='') assert.ok(margin<0);
    else assert.equal(margin,0);
   }
  });
  await record('Header custom light and dark palettes apply to the row and panel',async()=>{
   await context.addInitScript(()=>localStorage.removeItem('gnn-theme'));
   await load({header_surface:'solid',header_bg_light:'#f0e7cf',header_text_light:'#18212b',
    header_bg_dark:'#112233',header_text_dark:'#ffffff',header_accent_dark:'#ffd700'},390);
   const token=key=>mast.evaluate((el,key)=>getComputedStyle(el).getPropertyValue(key).trim(),key);
   for(const [mode,color,rgb] of [['light','#f0e7cf','rgb(240, 231, 207)'],['dark','#112233','rgb(17, 34, 51)']]) {
    await page.evaluate(mode=>document.documentElement.dataset.theme=mode,mode);
    assert.equal(await token('--gnn-header-custom-bg'),color);
    await toggle.click();
    assert.equal(await panel.isVisible(),true);
    assert.equal(await panel.evaluate(el=>getComputedStyle(el).backgroundColor),rgb);
    await page.keyboard.press('Escape');
   }
  });
  await record('Header works in RTL at 320 px',async()=>{
   await php("update_option('WPLANG','ar');");
   try {
    await load({},320);
    assert.equal(await page.locator('html').getAttribute('dir'),'rtl');
    await toggle.click();
    assert.equal(await panel.getByRole('link',{name:'News',exact:true}).isVisible(),true);
    assert.ok(await overflow()<=0);
   } finally { await php("update_option('WPLANG','en_US');"); }
  });
  await record('Header works without JavaScript at 390 px',async()=>{
   const {context:nojs,errors:nojsErrors}=await session(browser,server,{javaScriptEnabled:false,viewport:{width:390,height:860}});
   try {
    const plain=await nojs.newPage();
    await plain.goto(server.serverUrl+'/?page_id='+ids.plain);
    assert.equal(await plain.locator('#masthead .menu-toggle').isVisible(),false);
    assert.equal(await plain.locator('#mobile-navigation').getByRole('link',{name:'Third-level article'}).isVisible(),true);
   } finally { errors.push(...nojsErrors); await nojs.close(); }
  });
  if(await php("echo class_exists('WooCommerce')?'yes':'no';")==='yes') {
   await record('Header WooCommerce cart fragment includes the live accessible count',async()=>{
    const product=Number(await php("$product=new WC_Product_Simple(); $product->set_name('Header QA item'); $product->set_regular_price('10'); $product->set_status('publish'); echo $product->save();"));
    try {
     const response=await context.request.post(server.serverUrl+'/?wc-ajax=add_to_cart',{form:{product_id:product,quantity:1}});
     assert.ok(response.ok());
     const result=await response.json();
     assert.match(result.fragments['.cart-count'],/aria-hidden="true">1<\/span>/);
     assert.match(result.fragments['.cart-count'],/class="screen-reader-text"/);
     await load({},1440);
     assert.equal(await mast.locator('.gnn-header-row .cart-count [aria-hidden="true"]').innerText(),'1');
    } finally { await php('wp_delete_post('+product+',true);'); }
   });
  }
  await record('Header sticky position clears desktop and scrolling mobile admin bars',async()=>{
   await login(page,server,php);
   for(const width of [1440,390]) {
    await load({header_behavior:'sticky'},width);
    const toolbar=page.locator('#wpadminbar');
    const top=async()=> (await shell.boundingBox()).y;
    assert.ok(await top()>=(await toolbar.boundingBox()).height-1);
    await page.evaluate(()=>scrollTo(0,400));
    await page.waitForFunction(()=>document.querySelector('[data-gnn-header-shell]').classList.contains('is-scrolled'));
    const bar=await toolbar.boundingBox();
    assert.ok(Math.abs(await top()-Math.max(0,bar.y+bar.height))<=2);
   }
  });
  await record('Header Appearance Menus displays contextual options and opens the native media picker',async()=>{
   await page.setViewportSize({width:1440,height:900});
   await page.goto(server.serverUrl+'/wp-admin/nav-menus.php?menu='+ids.menu);
   const item=page.locator('#menu-item-'+ids.featured);
   await item.locator('.item-edit').click();
   const layout=item.locator('[data-gnn-menu-layout]');
   const image=item.locator('[data-gnn-featured-image]');
   assert.equal(await layout.inputValue(),'featured');
   assert.equal(await image.isVisible(),true);
   await layout.selectOption('');
   assert.equal(await image.isVisible(),false);
   await layout.selectOption('featured');
   await image.locator('[data-gnn-image-pick]').click();
   await page.locator('.media-modal').waitFor();
   await page.locator('.media-modal-close').click();
  });
  await record('Header console and page errors',()=>assert.deepEqual(errors,[]));
  await checkWarnings(record,php,debug,'Header front-end PHP diagnostics');
 } finally {
  await context.close();
  const cleanup=ids?"wp_delete_nav_menu("+ids.menu+");wp_delete_post("+ids.plain+",true);wp_delete_post("+ids.hero+",true);":"";
  await php("wp_set_current_user(1);"+cleanup+"list($options,$mods,$name,$lang)=unserialize(base64_decode('"+snapshot+"'));"+
   "if(false===$options){delete_option('gnn_options');}else{update_option('gnn_options',$options);} update_option('theme_mods_'.get_option('stylesheet'),$mods); update_option('blogname',$name); update_option('WPLANG',$lang);");
 }
 return tests;
}
