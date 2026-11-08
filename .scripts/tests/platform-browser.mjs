import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';

export async function testPlatformBrowser({browser,server,php,out}) {
 const tests=[];
 async function login(p,username){
  for(let attempt=0;attempt<2;attempt++){
   await p.goto(server.serverUrl+'/wp-login.php?redirect_to='+encodeURIComponent(server.serverUrl+'/wp-admin/'));
   await p.locator('#user_login').fill(username);await p.locator('#user_pass').fill('gnn-qa-local-only');
   await Promise.all([p.waitForNavigation({waitUntil:'domcontentloaded'}),p.locator('#wp-submit').click()]);
   if((await p.context().cookies()).some(c=>c.name.startsWith('wordpress_logged_in_')))return;
  }
  assert.fail('Login did not issue an authenticated cookie for '+username+': '+(await p.locator('body').innerText()).slice(0,500));
 }

 const record=async(name,fn)=>{try{await fn();tests.push({name,pass:true});}catch(error){tests.push({name,pass:false,detail:String(error.stack||error)});console.log('FAILED:',name,String(error));
 if(out){const pages=[...context.pages(),...(reader?reader.pages():[]),...(support?support.pages():[])];const observed=[];for(const p of pages){try{observed.push({url:p.url(),body:(await p.locator('body').innerText()).slice(0,4000),config:await p.evaluate(()=>window.gnnTracking||null)});}catch{}}fs.writeFileSync(path.join(out,'platform-debug-'+tests.length+'.json'),JSON.stringify(observed,null,2));}}};
 await php("gnn_reset_panel_settings();update_option('gnn_github_updates_enable',0);$o=gnn_option_defaults();$o['ga4_id']='G-TEST12345';$o['gtm_id']='GTM-TEST12345';$o['analytics_delivery']='consent';update_option('gnn_options',$o);",true);
 const context=await browser.newContext();let reader,support;
 const tracking=[];
 context.on('request',request=>{if(request.url().includes('googletagmanager.com'))tracking.push(request.url());});
 await context.route('**/*',route=>route.request().url().startsWith(server.serverUrl)?route.continue():route.abort());
 const page=await context.newPage();
 try {
  await record('P31 no tracking request before explicit consent',async()=>{
   await page.goto(server.serverUrl,{waitUntil:'networkidle'});assert.equal(tracking.length,0);assert.equal(await page.evaluate(()=>GNNConsent.hasAnalyticsConsent()),false);
  });
  await record('P32 consent loads only GTM once and revocation updates state',async()=>{
   await Promise.all([context.waitForEvent('request',{predicate:r=>r.url().includes('googletagmanager.com')}),page.evaluate(()=>{GNNConsent.setAnalyticsConsent(true);GNNConsent.setAnalyticsConsent(true);})]);
   assert.equal(tracking.length,1);assert.ok(tracking[0].includes('/gtm.js'));assert.ok(!tracking[0].includes('/gtag/js'));
   await page.evaluate(()=>GNNConsent.setAnalyticsConsent(false));assert.equal(await page.evaluate(()=>GNNConsent.hasAnalyticsConsent()),false);
  });
  await record('P33 client cannot forge a custom-code write',async()=>{
   assert.equal(await php("$u=wp_authenticate('platform-client','gnn-qa-local-only');echo is_wp_error($u)?$u->get_error_code():'authenticated';"),'authenticated');
   reader=await browser.newContext();const p=await reader.newPage();await login(p,'platform-client');
   await p.goto(server.serverUrl+'/wp-admin/admin.php?page=gnn-panel');assert.equal(await p.locator('input[name="gnn_options[company_phone]"]').count(),1);assert.equal(await p.locator('[name="gnn_options[custom_js_head]"]').count(),0);
   const nonce=await p.locator('input[name=_wpnonce]').inputValue();const rev=await p.locator('input[name=gnn_revision]').inputValue();
   const r=await p.request.post(server.serverUrl+'/wp-admin/admin-post.php',{form:{action:'gnn_workspace',operation:'brand',_wpnonce:nonce,gnn_revision:rev,'gnn_options[custom_js_head]':'alert(1)'},maxRedirects:0});assert.equal(r.status(),403);
  });
  await record('P34 client can save public contact data without administrator rights',async()=>{
   const p=reader.pages()[0];await p.locator('#gnn-company_phone').fill('+90 212 555 0101');
   await Promise.all([p.waitForNavigation({waitUntil:'domcontentloaded'}),p.locator('form:has(input[value="brand"]) input[type=submit]').click()]);
   assert.equal(await php("echo gnn_option('company_phone');"),'+90 212 555 0101');
  });
  await record('P35 server renders reusable header and page override precedence',async()=>{
   await php("$a=wp_insert_post(array('post_type'=>'gnn_layout','post_status'=>'publish','post_title'=>'Global header','post_content'=>'<!-- wp:paragraph --><p>GLOBAL REGION</p><!-- /wp:paragraph -->'));$b=wp_insert_post(array('post_type'=>'gnn_layout','post_status'=>'publish','post_title'=>'Page header','post_content'=>'<!-- wp:paragraph --><p>PAGE REGION</p><!-- /wp:paragraph -->'));$o=get_option('gnn_options');$o['header_template']=$a;update_option('gnn_options',$o);update_post_meta(get_option('gnn_qa_home_id'),'_gnn_layout',array('header_template'=>$b));",true);
   const id=await php("echo get_option('gnn_qa_home_id');");await page.goto(server.serverUrl+'/?page_id='+id,{waitUntil:'networkidle'});assert.match(await page.locator('.gnn-layout-region--header').innerText(),/PAGE REGION/);assert.doesNotMatch(await page.locator('body').innerText(),/GLOBAL REGION/);
  });
  await record('P36 support role receives masked diagnostics but cannot restore',async()=>{
   await php("$id=wp_create_user('platform-support','gnn-qa-local-only','platform-support@example.invalid');(new WP_User($id))->set_role('gnn_support');$o=get_option('gnn_options');$o['company_name']='PRIVATE CUSTOMER';$o['custom_js_head']='window.privateCode=123;';update_option('gnn_options',$o);",true);
   support=await browser.newContext();const p=await support.newPage();await login(p,'platform-support');await p.goto(server.serverUrl+'/wp-admin/admin.php?page=gnn-panel');
   const nonce=await p.locator('input[name=_wpnonce]').first().inputValue();const endpoint=server.serverUrl+'/wp-admin/admin-post.php';
   const result=await p.request.post(endpoint,{form:{action:'gnn_workspace',operation:'support',_wpnonce:nonce}});assert.equal(result.status(),200);assert.doesNotMatch(await result.text(),/PRIVATE CUSTOMER|privateCode/);
   const denied=await p.request.post(endpoint,{form:{action:'gnn_workspace',operation:'restore',confirmed:'1',_wpnonce:nonce,snapshot:'forged'},maxRedirects:0});assert.equal(denied.status(),403);
  });
  await record('P37 stale Settings API form cannot overwrite a newer HTTP-visible value',async()=>{
   await php("wp_set_password('gnn-qa-local-only',1);",true);
   await login(page,'admin');
   await page.goto(server.serverUrl+'/wp-admin/admin.php?page=gnn-panel');const form=page.locator('form[action="options.php"]');const nonce=await form.locator('input[name=_wpnonce]').inputValue(),rev=await form.locator('input[name=gnn_revision]').inputValue();
   await php("$o=get_option('gnn_options');$o['company_phone']='CONCURRENT EDIT';update_option('gnn_options',$o);",true);
   await page.request.post(server.serverUrl+'/wp-admin/options.php',{form:{option_page:'gnn_options_group',action:'update',_wpnonce:nonce,gnn_revision:rev,gnn_form_scope:'design','gnn_options[company_phone]':'STALE EDIT'}});
   assert.equal(await php("echo gnn_option('company_phone');"),'CONCURRENT EDIT');
  });
  let workspaceNonce,snapshot;
  await record('P38 confirmed operational save creates a snapshot and returns visitor HTTP 503',async()=>{
   await page.goto(server.serverUrl+'/wp-admin/admin.php?page=gnn-maintenance');const form=page.locator('form:has(input[value="operations"])');workspaceNonce=await form.locator('input[name=_wpnonce]').inputValue();const rev=await form.locator('input[name=gnn_revision]').inputValue();
   const r=await page.request.post(server.serverUrl+'/wp-admin/admin-post.php',{form:{action:'gnn_workspace',operation:'operations',confirmed:'1',_wpnonce:workspaceNonce,gnn_revision:rev,'gnn_options[maintenance_mode]':'1','gnn_options[compatibility_mode]':'1'},maxRedirects:0});assert.equal(r.status(),302);
   snapshot=await php("$h=get_option('gnn_configuration_history');echo $h[0]['id'];");assert.ok(snapshot);
   const visitor=await browser.newContext();try{const response=await visitor.request.get(server.serverUrl);assert.equal(response.status(),503);}finally{await visitor.close();}
  });
  await record('P39 HTTP recovery restores the configuration before maintenance',async()=>{
   const rev=await php("echo gnn_configuration_revision();");const r=await page.request.post(server.serverUrl+'/wp-admin/admin-post.php',{form:{action:'gnn_workspace',operation:'restore',confirmed:'1',_wpnonce:workspaceNonce,gnn_revision:rev,snapshot},maxRedirects:0});assert.equal(r.status(),302);assert.equal(await php("echo (int)gnn_option('maintenance_mode');"),'0');assert.equal(await php("echo gnn_option('company_phone');"),'CONCURRENT EDIT');
  });
  async function preview(value){
   await page.goto(server.serverUrl+'/wp-admin/admin.php?page=gnn-panel');const form=page.locator('form:has(input[value="gnn_import_settings"])');const nonce=await form.locator('input[name=_wpnonce]').inputValue();
   const doc=JSON.stringify({_gnn_export:'2.0.0',schema_version:2,scope:'site',options:{company_phone:value}});
   const r=await page.request.post(server.serverUrl+'/wp-admin/admin-post.php',{multipart:{action:'gnn_import_settings',_wpnonce:nonce,gnn_preview:'1',gnn_import_file:{name:'settings.json',mimeType:'application/json',buffer:Buffer.from(doc)}}});assert.equal(r.status(),200);const html=await r.text();assert.ok(!html.includes(value));await page.setContent(html);return {token:await page.locator('input[name=token]').inputValue(),nonce:await page.locator('input[name=_wpnonce]').inputValue()};
  }
  await record('P40 multipart preview is private and confirmation applies the reviewed document',async()=>{
   const before=await php("echo gnn_option('company_phone');");const p=await preview('REVIEWED PHONE');assert.equal(await php("echo gnn_option('company_phone');"),before);
   const r=await page.request.post(server.serverUrl+'/wp-admin/admin-post.php',{form:{action:'gnn_confirm_import',confirmed:'1',token:p.token,_wpnonce:p.nonce},maxRedirects:0});assert.equal(r.status(),302);assert.equal(await php("echo gnn_option('company_phone');"),'REVIEWED PHONE');
  });
  await record('P41 import confirmation rejects changes after preview with HTTP 409',async()=>{
   const p=await preview('STALE IMPORT');await php("$o=get_option('gnn_options');$o['company_phone']='LATEST EDIT';update_option('gnn_options',$o);",true);
   const r=await page.request.post(server.serverUrl+'/wp-admin/admin-post.php',{form:{action:'gnn_confirm_import',confirmed:'1',token:p.token,_wpnonce:p.nonce},maxRedirects:0});assert.equal(r.status(),409);assert.equal(await php("echo gnn_option('company_phone');"),'LATEST EDIT');
  });
  if(process.env.GNN_QA_INTEGRATIONS==='1')await record('P42 real WP Consent API propagates deny grant and revocation',async()=>{
   await page.goto(server.serverUrl,{waitUntil:'networkidle'});
   assert.equal(await page.evaluate(()=>typeof wp_set_consent),'function');
   await page.evaluate(()=>{window.wp_consent_type='optin';wp_set_consent('statistics','deny');});assert.equal(await page.evaluate(()=>GNNConsent.hasAnalyticsConsent()),false);
   await page.evaluate(()=>wp_set_consent('statistics','allow'));assert.equal(await page.evaluate(()=>GNNConsent.hasAnalyticsConsent()),true);
   await page.evaluate(()=>wp_set_consent('statistics','deny'));assert.equal(await page.evaluate(()=>GNNConsent.hasAnalyticsConsent()),false);
  });
  if(process.env.GNN_QA_INTEGRATIONS==='1')await record('P43 real contact-form validation and success run without sending mail',async()=>{
   const id=await php("echo get_option('gnn_qa_form_id');");const visitor=await browser.newContext();
   try{
    const endpoint=server.serverUrl+'/wp-json/contact-form-7/v1/contact-forms/'+id+'/feedback';
    const base={_wpcf7_unit_tag:'wpcf7-f'+id+'-o1'};
    const invalid=await visitor.request.post(endpoint,{multipart:base});assert.equal(invalid.status(),200);assert.equal((await invalid.json()).status,'validation_failed');
    const valid=await visitor.request.post(endpoint,{multipart:{...base,'your-name':'Local QA','your-email':'qa@example.invalid','your-subject':'Local validation','your-message':'Test data remains local. Native CF7 skip_mail is enabled.'}});assert.equal(valid.status(),200);assert.equal((await valid.json()).status,'mail_sent');
   }finally{await visitor.close();}
  });
 }finally {if(reader)await reader.close();if(support)await support.close();await context.close();}
 return tests;
}
