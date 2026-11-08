import fs from 'node:fs';
import path from 'node:path';

// Readiness acceptance probes, separate from existing regression contracts.
export async function reviewEnterprisePanel({browser,server,php,out,modules}) {
 const cases=[],measurements={tabs:[],scope:'Fresh local WordPress/SQLite admin, no production data'};
 const record=(name,pass,observed,kind='enterprise-acceptance')=>cases.push({name,kind,pass,observed});
 await php("wp_set_password('gnn-qa-local-only',1); update_option('gnn_github_updates_enable',0); update_option('gnn_options',gnn_option_defaults());",true);
 const context=await browser.newContext({viewport:{width:1440,height:1000}});
 await context.route('**/*',route=>route.request().url().startsWith(server.serverUrl)?route.continue():route.abort());
 let nojs;
 try {
  const page=await context.newPage();
  await page.goto(server.serverUrl+'/wp-login.php');
  await page.locator('#user_login').fill('admin');await page.locator('#user_pass').fill('gnn-qa-local-only');
  await Promise.all([page.waitForURL('**/wp-admin/**'),page.locator('#wp-submit').click()]);
  const panel=server.serverUrl+'/wp-admin/admin.php?page=gnn-panel';
  await page.goto(panel,{waitUntil:'networkidle'});
  const tabs=await page.locator('.gnn-tabs a').evaluateAll(items=>items.map(a=>a.dataset.tab));
  record('All nine panel sections can be activated',tabs.length===9,{tabs},'existing-contract');
  const axeSource=fs.readFileSync(path.join(modules,'axe-core/axe.min.js'),'utf8');
  for(const tab of tabs) {
   await page.locator(`.gnn-tabs [data-tab="${tab}"]`).click();
   const active=await page.locator(`#gnn-tab-${tab}`).isVisible();
   await page.evaluate(async()=>{await new Promise(requestAnimationFrame);await Promise.all(document.getAnimations().filter(a=>Number.isFinite(a.effect.getComputedTiming().endTime)).map(a=>a.finished.catch(()=>{})));});
   await page.addScriptTag({content:axeSource});
   const audit=await page.evaluate(()=>axe.run(document.querySelector('.gnn-panel'),{runOnly:{type:'tag',values:['wcag2a','wcag2aa','wcag21a','wcag21aa','wcag22aa']}}));
   measurements.tabs.push({tab,active,violations:audit.violations.map(v=>({id:v.id,impact:v.impact,nodes:v.nodes.map(n=>({target:n.target,summary:n.failureSummary}))})),incomplete:audit.incomplete.map(v=>({id:v.id,targets:v.nodes.map(n=>n.target)}))});
  }
  record('Every panel tab has zero automated WCAG violations',measurements.tabs.every(t=>t.violations.length===0),measurements.tabs.map(t=>({tab:t.tab,violations:t.violations.map(v=>({id:v.id,nodes:v.nodes.length}))})));
  await page.locator('.gnn-tabs [data-tab="global"]').click();
  const unnamed=await page.locator('.gnn-switch input').evaluateAll(inputs=>inputs.filter(i=>!i.getAttribute('aria-label')&&!i.getAttribute('aria-labelledby')&&!Array.from(i.labels||[]).some(l=>l.textContent.trim())).map(i=>i.name));
  record('Switch controls have programmatically associated text labels',unnamed.length===0,{unnamed});
  const tabStates=await page.locator('.gnn-tabs a').evaluateAll(items=>items.map(a=>({id:a.dataset.tab,role:a.getAttribute('role'),selected:a.getAttribute('aria-selected'),current:a.getAttribute('aria-current')})));
  record('Selected section is exposed to assistive technology',tabStates.some(t=>t.selected==='true'||(t.current&&t.current!=='false')),{tabStates});
  const form=page.locator('form[action="options.php"]');
  const before=await php("echo wp_json_encode(get_option('gnn_options')['color_bg_dark']);");
  await page.locator('.gnn-tabs [data-tab="header"]').click();
  await page.locator('#gnn-topbar_phone').fill('SAVED-PANEL-PROBE');
  await Promise.all([page.waitForNavigation({waitUntil:'networkidle'}),form.locator('#submit').click()]);
  const after=await php("echo wp_json_encode(get_option('gnn_options')['color_bg_dark']);");
  record('No-op save preserves inherited color state',before===after,{before,after});
  const savedPhone=await php("echo get_option('gnn_options')['topbar_phone'];");
  record('Ordinary full-form save succeeds',savedPhone==='SAVED-PANEL-PROBE',{url:page.url(),savedPhone},'existing-contract');
  await page.locator('.gnn-tabs [data-tab="header"]').click();
  let dialogCount=0;page.on('dialog',async dialog=>{dialogCount++;await dialog.accept();});
  await page.locator('#gnn-topbar_phone').fill('UNSAVED-REVIEW-VALUE');
  await page.goto(panel);
  record('Navigation protects unsaved changes',dialogCount>0,{dialogs:dialogCount});
  await page.locator('.gnn-tabs [data-tab="header"]').click();
  const stalePayload=await form.evaluate(f=>Array.from(new FormData(f).entries()));
  await php("$o=get_option('gnn_options');$o['topbar_phone']='CONCURRENT-ADMIN';update_option('gnn_options',$o);",true);
  const response=await page.request.post(server.serverUrl+'/wp-admin/options.php',{form:Object.fromEntries(stalePayload),maxRedirects:0});
  const stored=await php("echo get_option('gnn_options')['topbar_phone'];");
  record('Stale admin save detects conflict instead of losing a newer edit',stored==='CONCURRENT-ADMIN',{status:response.status(),stored,expected:'CONCURRENT-ADMIN'});
  await page.screenshot({path:path.join(out,'panel-header-desktop.png'),fullPage:true});
  await page.setViewportSize({width:390,height:844});await page.reload({waitUntil:'networkidle'});
  measurements.mobile=await page.evaluate(()=>({viewport:innerWidth,scrollWidth:document.documentElement.scrollWidth}));
  record('Admin panel fits a mobile viewport without document overflow',measurements.mobile.scrollWidth<=measurements.mobile.viewport,measurements.mobile);
  await page.screenshot({path:path.join(out,'panel-mobile.png'),fullPage:true});
  nojs=await browser.newContext({javaScriptEnabled:false,storageState:await context.storageState()});
  const fallback=await nojs.newPage();await fallback.goto(panel);
  const visible=await fallback.locator('.gnn-tab').evaluateAll(items=>items.filter(e=>getComputedStyle(e).display!=='none').length);
  record('Basic settings remain visible if panel JavaScript is unavailable',visible>0,{visibleSections:visible});
 } finally {if(nojs)await nojs.close();await context.close();}
 fs.writeFileSync(path.join(out,'panel-review.json'),JSON.stringify({cases,measurements},null,2));
 return {cases,measurements};
}
