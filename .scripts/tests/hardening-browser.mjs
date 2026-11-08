import fs from 'node:fs';
import path from 'node:path';
import assert from 'node:assert/strict';

export async function testHardeningBrowser({browser,server,php,out,modules}) {
 const tests=[],measurements={accessibility:[],navigation:[],scope:'Local WordPress/SQLite, Chromium; not production Web Vitals. INP and manual screen-reader use unmeasured.'};
 const record=async(name,fn)=>{try{await fn();tests.push({name,pass:true});}catch(error){tests.push({name,pass:false,detail:String(error.stack||error)});console.log('FAILED:',name,String(error));}};
 const context=await browser.newContext({viewport:{width:1365,height:900}});
 await context.route('**/*',route=>route.request().url().startsWith(server.serverUrl)?route.continue():route.abort());
 await context.addInitScript(()=>{
  window.gnnMetrics={cls:0,lcp:0};
  new PerformanceObserver(list=>list.getEntries().forEach(e=>{if(!e.hadRecentInput)window.gnnMetrics.cls+=e.value;})).observe({type:'layout-shift',buffered:true});
  new PerformanceObserver(list=>list.getEntries().forEach(e=>window.gnnMetrics.lcp=e.startTime)).observe({type:'largest-contentful-paint',buffered:true});
 });
 const page=await context.newPage();
 const errors=[];page.on('pageerror',error=>errors.push(String(error)));
 await php("update_option('gnn_options',array_merge(gnn_option_defaults(),array('loading_screen'=>1,'scroll_anim'=>1,'mobile_dock'=>1,'google_material_icons'=>0))); update_option('show_on_front','page'); update_option('page_on_front',get_option('gnn_qa_home_id')); wp_update_post(array('ID'=>get_option('gnn_qa_home_id'),'post_content'=>'<h2>Accessible content</h2><p>Local theme verification.</p><h3>More information</h3><p>Keyboard and layout checks.</p>')); ",true);
 await page.goto(server.serverUrl,{waitUntil:'networkidle'});
 await record('Loading overlay closes and animated content is readable',async()=>{
  await page.locator('.gnn-loading').waitFor({state:'detached'});
  await page.locator('.gnn-anim').first().scrollIntoViewIfNeeded();
  await page.waitForFunction(()=>getComputedStyle(document.querySelector('.gnn-anim')).opacity==='1');
  assert.equal(await page.locator('.gnn-anim').first().evaluate(el=>getComputedStyle(el).opacity),'1');
 });
 const axeSource=fs.readFileSync(path.join(modules,'axe-core/axe.min.js'),'utf8');
 async function audit(name){
  await page.evaluate(async()=>{await new Promise(requestAnimationFrame);await Promise.all(document.getAnimations().filter(a=>Number.isFinite(a.effect.getComputedTiming().endTime)).map(a=>a.finished.catch(()=>{})));});
  await page.addScriptTag({content:axeSource});
  const result=await page.evaluate(()=>axe.run(document,{runOnly:{type:'tag',values:['wcag2a','wcag2aa','wcag21a','wcag21aa','wcag22aa']}}));
  const violations=result.violations.map(v=>({id:v.id,impact:v.impact,help:v.help,nodes:v.nodes.map(n=>({target:n.target,summary:n.failureSummary}))}));
  measurements.accessibility.push({name,violations,incomplete:result.incomplete.map(v=>({id:v.id,nodes:v.nodes.map(n=>n.target)})),passes:result.passes.length});
  assert.deepEqual(violations,[],name+' has accessibility violations');
 }
 await record('Dark desktop WCAG automation',()=>audit('dark desktop'));
 await record('Light desktop WCAG automation',async()=>{
  await page.locator('.header-actions .theme-toggle').click();await audit('light desktop');
 });
 await record('Mobile menu opens and Escape restores focus',async()=>{
  await page.setViewportSize({width:390,height:844});
  await page.locator('.menu-toggle').click();
  assert.equal(await page.locator('#mobile-navigation').isVisible(),true);
  await page.locator('#mobile-navigation a').first().focus();await page.keyboard.press('Escape');
  assert.equal(await page.locator('#mobile-navigation').isVisible(),false);
  assert.equal(await page.locator('.menu-toggle').evaluate(el=>el===document.activeElement),true);
 });
 await record('Mobile WCAG automation and overflow',async()=>{
  await audit('mobile');assert.equal(await page.evaluate(()=>document.documentElement.scrollWidth<=innerWidth),true);
  await page.screenshot({path:path.join(out,'hardening-mobile.png'),fullPage:true});
 });
 await record('Special-character anchor cannot throw selector errors',async()=>{
  await page.evaluate(()=>{const a=document.createElement('a');a.textContent='Special anchor';a.href='#missing"]';document.querySelector('main').appendChild(a);a.click();});
  assert.deepEqual(errors,[]);
 });
 await record('RTL stylesheet loads with mirrored controls and no overflow',async()=>{
  await php("update_option('WPLANG','ar');");
  await page.goto(server.serverUrl,{waitUntil:'networkidle'});
  assert.equal(await page.locator('html').getAttribute('dir'),'rtl');
  assert.equal(await page.locator('link#gnn-rtl-css').count(),1);
  assert.equal(await page.evaluate(()=>document.documentElement.scrollWidth<=innerWidth),true);
  assert.equal(await page.locator('.gnn-scrolltop').evaluate(el=>Math.round(el.getBoundingClientRect().left)),22);
  await page.screenshot({path:path.join(out,'hardening-rtl.png'),fullPage:true});
 });
 await record('RTL WCAG automation',()=>audit('RTL mobile'));
 await php("update_option('WPLANG','tr_TR');");
 await record('Turkish catalog loads through WordPress gettext',async()=>{
  const translated=await php("switch_to_locale('tr_TR'); unload_textdomain('gnn',true); load_textdomain('gnn',get_template_directory().'/languages/tr_TR.mo'); echo __('Search','gnn');");
  assert.equal(translated,'Ara');
 });
 await record('No-JavaScript content and overlay fallback',async()=>{
  const nojs=await browser.newContext({javaScriptEnabled:false,viewport:{width:390,height:844}});
  try{const p=await nojs.newPage();await p.goto(server.serverUrl,{waitUntil:'networkidle'});
   assert.equal(await p.locator('.gnn-loading').isVisible(),false);
   assert.equal(await p.locator('.gnn-anim').first().evaluate(el=>getComputedStyle(el).opacity),'1');
  }finally{await nojs.close();}
 });
 await php("update_option('WPLANG','en_US'); update_option('gnn_options',array_merge(gnn_option_defaults(),array('loading_screen'=>0,'scroll_anim'=>0,'google_material_icons'=>0)));",true);
 await record('Local performance budgets and layout stability',async()=>{
  await page.setViewportSize({width:1365,height:900});
  for(let i=0;i<3;i++){
   await page.goto(server.serverUrl,{waitUntil:'networkidle'});
   const sample=await page.evaluate(()=>{const n=performance.getEntriesByType('navigation')[0];return {...window.gnnMetrics,domContentLoadedMs:n.domContentLoadedEventEnd,loadMs:n.loadEventEnd,resources:performance.getEntriesByType('resource').length,bytes:performance.getEntriesByType('resource').reduce((s,r)=>s+r.decodedBodySize,0)};});
   measurements.navigation.push(sample);assert.ok(sample.cls<=0.1,'CLS exceeds 0.1');
  }
  const themeResources=await page.evaluate(()=>performance.getEntriesByType('resource').filter(r=>r.name.includes('/themes/gnn/')).map(r=>({url:new URL(r.name).pathname,bytes:r.decodedBodySize})));
  measurements.themeResources=themeResources;
  const js=themeResources.filter(r=>r.url.endsWith('.js')).reduce((s,r)=>s+r.bytes,0);
  const css=themeResources.filter(r=>r.url.endsWith('.css')).reduce((s,r)=>s+r.bytes,0);
  assert.ok(js<32768,'Theme JS budget 32 KiB');assert.ok(css<98304,'Theme CSS budget 96 KiB');
 });
 await record('Real admin export, malformed upload and partial import',async()=>{
  await page.goto(server.serverUrl+'/wp-login.php');
  await page.locator('#user_login').fill('admin');await page.locator('#user_pass').fill('gnn-qa-local-only');
  await Promise.all([page.waitForURL('**/wp-admin/**'),page.locator('#wp-submit').click()]);
  await page.goto(server.serverUrl+'/wp-admin/admin.php?page=gnn-panel');
  const nonce=await page.locator('form:has(input[value=gnn_import_settings]) input[name=_wpnonce]').inputValue();
  const exportNonce=await page.locator('form:has(input[value=gnn_export_settings]) input[name=_wpnonce]').inputValue();
  const endpoint=server.serverUrl+'/wp-admin/admin-post.php';
  const exported=await page.request.post(endpoint,{form:{action:'gnn_export_settings',_wpnonce:exportNonce}});
  assert.equal(exported.status(),200);assert.match(exported.headers()['content-type'],/application\/json/);
  assert.match(exported.headers()['content-disposition'],/attachment/);assert.ok((await exported.json())._gnn_export);
  const snapshot=await php("echo wp_json_encode(array(get_option('gnn_options'),get_theme_mods()));");
  const upload=async(buffer,name='settings.json')=>page.request.post(endpoint,{multipart:{action:'gnn_import_settings',_wpnonce:nonce,gnn_import_file:{name,mimeType:'application/json',buffer:Buffer.from(buffer)}},maxRedirects:0});
  const rejected=await upload('{');assert.equal(rejected.status(),302);assert.match(rejected.headers().location,/import_error/);
  assert.equal(await php("echo wp_json_encode(array(get_option('gnn_options'),get_theme_mods()));"),snapshot);
  const badName=await upload('{}','settings.php');assert.match(badName.headers().location,/import_error/);
  const tooLarge=await upload(' '.repeat(1048577));assert.match(tooLarge.headers().location,/import_error/);
  const success=await upload(JSON.stringify({_gnn_export:'1.5.1',options:{excerpt_length:37},theme_mods:{gnn_accent_color:'#123abc'}}));
  assert.match(success.headers().location,/import_ok/);assert.equal(await php("echo gnn_option('excerpt_length').'|'.get_theme_mod('gnn_accent_color');"),'37|#123abc');
  const missingNonce=await page.request.post(endpoint,{form:{action:'gnn_import_settings'},maxRedirects:0});assert.equal(missingNonce.status(),403);
 });
 await record('A single legacy logo stays visible in both color modes',async()=>{
  await php("$o=get_option('gnn_options'); unset($o['logo_light'],$o['logo_light_2x'],$o['logo_dark'],$o['logo_dark_2x']); update_option('gnn_options',$o); set_theme_mod('logo_light',get_template_directory_uri().'/screenshot.png'); remove_theme_mod('logo_dark');",true);
  await page.goto(server.serverUrl,{waitUntil:'networkidle'});
  assert.equal(await page.locator('.site-logo img.gnn-logo--shared').isVisible(),true);
  await page.locator('.header-actions .theme-toggle').click();
  assert.equal(await page.locator('.site-logo img.gnn-logo--shared').isVisible(),true);
 });
 await record('Subscriber cannot access import/export handlers',async()=>{
  const reader=await browser.newContext();
  try{const p=await reader.newPage();await p.goto(server.serverUrl+'/wp-login.php');
   await p.locator('#user_login').fill('gnn-qa-reader');await p.locator('#user_pass').fill('gnn-qa-local-only');
   await Promise.all([p.waitForURL(url=>url.pathname!=='/wp-login.php'),p.locator('#wp-submit').click()]);
   assert.ok((await reader.cookies()).some(cookie=>cookie.name.startsWith('wordpress_logged_in_')),'Subscriber must be authenticated before checking access');
   for(const action of ['gnn_import_settings','gnn_export_settings'])assert.equal((await p.request.post(server.serverUrl+'/wp-admin/admin-post.php',{form:{action},maxRedirects:0})).status(),403);
  }finally{await reader.close();}
 });
 await context.close();
 fs.writeFileSync(path.join(out,'hardening-measurements.json'),JSON.stringify(measurements,null,2));
 return {tests,measurements};
}
