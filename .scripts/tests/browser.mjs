import path from 'node:path';
import assert from 'node:assert/strict';

export async function testBrowser({browser,server,php,out}) {
 const tests=[];
 const record=async(name,fn)=>{
  try { await fn(); tests.push({name,pass:true}); }
  catch(error) { tests.push({name,pass:false,detail:String(error.stack || error)}); }
 };
 const context=await browser.newContext({viewport:{width:1365,height:900}});
 await context.route('**/*',route=>route.request().url().startsWith(server.serverUrl) ? route.continue() : route.abort());
 const page=await context.newPage();
 await page.clock.install({time:new Date('2026-10-18T00:00:00Z')});
 await page.clock.pauseAt(new Date('2026-10-18T00:00:00Z'));
 const errors=[];
 const rotationStartLabel=await php("echo __('Start slide rotation','gnn');");
 page.on('pageerror',error=>errors.push(String(error)));
 await record('A01 latest-posts homepage displays posts',async()=>{
  await page.goto(server.serverUrl,{waitUntil:'networkidle'});
  assert.equal(await page.locator('.blog-index').count(),1);
  assert.match(await page.locator('.blog-index').innerText(),/Audit post 5/);
  assert.equal(await page.locator('.blog-index article').count(),2);
 });
 await record('A01 pagination displays next posts',async()=>{
  await page.goto(server.serverUrl+'/?paged=2',{waitUntil:'networkidle'});
  const text=await page.locator('.blog-index').innerText();
  assert.match(text,/Audit post 3/); assert.doesNotMatch(text,/Audit post 5/);
 });
 await record('A01 static content and hero retained',async()=>{
  await php("update_option('show_on_front','page'); update_option('page_on_front',get_option('gnn_qa_home_id'));");
  await page.goto(server.serverUrl,{waitUntil:'networkidle'});
  assert.match(await page.locator('main').innerText(),/STATIC FRONT CONTENT/);
  assert.equal(await page.locator('.gnn-hero__slide').count(),3);
 });
 await page.mouse.move(0,0);
 await record('A07 inactive slides excluded from focus',async()=>{
  assert.deepEqual(await page.locator('.gnn-hero__slide').evaluateAll(nodes=>nodes.map(n=>n.inert)),[false,true,true]);
  await page.locator('.gnn-hero__slide').nth(1).locator('a').evaluate(el=>el.focus());
  assert.notEqual(await page.evaluate(()=>document.activeElement?.textContent),'Open slide 2');
 });
 await record('A07 autoplay rotates while unfocused',async()=>{
  await page.clock.runFor(2200);
  assert.equal(await page.locator('.gnn-hero__slide').nth(1).getAttribute('aria-hidden'),'false');
 });
 await record('A07 focus pauses until explicit restart',async()=>{
  await page.locator('.gnn-hero__slide.is-active a').focus();
  const before=await page.locator('.gnn-hero__track').getAttribute('style');
  await page.clock.runFor(5000);
  assert.equal(await page.locator('.gnn-hero__track').getAttribute('style'),before);
  assert.equal(await page.locator('.gnn-hero__rotation').textContent(),rotationStartLabel);
  await page.locator('.gnn-hero__rotation').focus();
  await page.keyboard.press('Enter');
  await page.mouse.move(0,0);
  await page.clock.runFor(2200);
  assert.notEqual(await page.locator('.gnn-hero__track').getAttribute('style'),before);
 });
 await record('A07 ordinary dot buttons select and expose current slide',async()=>{
  await page.locator('.gnn-hero__dot').nth(0).click();
  assert.equal(await page.locator('.gnn-hero__dot[aria-current=true]').count(),1);
  assert.equal(await page.locator('.gnn-hero__slide').nth(0).getAttribute('aria-hidden'),'false');
  assert.equal(await page.locator('.gnn-hero [role=tab]').count(),0);
 });
 await record('A07 reduced motion stops and hides rotation control',async()=>{
  await page.emulateMedia({reducedMotion:'reduce'});
  await page.waitForFunction(()=>document.querySelector('.gnn-hero__rotation').hidden);
  const before=await page.locator('.gnn-hero__track').getAttribute('style');
  await page.clock.runFor(5000);
  assert.equal(await page.locator('.gnn-hero__track').getAttribute('style'),before);
 });
 await record('A07 RTL slide translation follows flex direction',async()=>{
  await page.evaluate(()=>document.documentElement.dir='rtl');
  await page.locator('.gnn-hero__dot').nth(1).click();
  assert.match(await page.locator('.gnn-hero__track').getAttribute('style'),/translateX\(100%\)/);
 });
 await record('A07 mobile layout remains within viewport',async()=>{
  await page.evaluate(()=>document.documentElement.dir='ltr');
  await page.setViewportSize({width:390,height:844});
  await page.locator('.gnn-hero__dot').first().click();
  assert.equal(await page.evaluate(()=>document.documentElement.scrollWidth<=innerWidth),true);
  await page.screenshot({path:path.join(out,'mobile-slider.png'),fullPage:true});
 });
 await record('A07 no JavaScript keeps first slide and hides unusable controls',async()=>{
  const nojs=await browser.newContext({javaScriptEnabled:false});
  const p=await nojs.newPage();
  await p.goto(server.serverUrl);
  assert.equal(await p.locator('.gnn-hero__controls').isVisible(),false);
  assert.equal(await p.locator('.gnn-hero__slide').first().isVisible(),true);
  assert.equal(await p.locator('.gnn-hero__slide').nth(1).getAttribute('inert'),'');
  await nojs.close();
 });
 await record('A07 single slide has no rotation controls',async()=>{
  await php("$ids=get_posts(array('post_type'=>'gnn_slide','posts_per_page'=>-1,'fields'=>'ids','orderby'=>'menu_order','order'=>'ASC')); foreach(array_slice($ids,1) as $id){wp_delete_post($id,true);}");
  await page.goto(server.serverUrl,{waitUntil:'networkidle'});
  assert.equal(await page.locator('.gnn-hero__slide').count(),1);
  assert.equal(await page.locator('.gnn-hero__controls').count(),0);
 });
 await record('A01 empty blog displays its empty state',async()=>{
  await php("foreach(get_posts(array('post_type'=>'post','posts_per_page'=>-1,'fields'=>'ids')) as $id){wp_delete_post($id,true);} update_option('show_on_front','posts');");
  await page.goto(server.serverUrl,{waitUntil:'networkidle'});
  assert.equal(await page.locator('.blog-index').count(),1);
  assert.equal(await page.locator('.blog-index article').count(),0);
  assert.ok((await page.locator('.blog-index').innerText()).length>20);
 });
 await record('No browser JavaScript exceptions',async()=>assert.deepEqual(errors,[]));
 await context.close();
 return tests;
}
