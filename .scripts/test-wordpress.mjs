/** Real WordPress/SQLite integration and Chromium UI checks; no WordPress stubs.
 * Dev dependencies: pnpm --dir .temp/qa-tools add --ignore-scripts @wp-playground/cli@3.1.58 playwright@1.64.1 axe-core@4.11.0
 *   pnpm --dir .temp/qa-tools exec playwright install --with-deps chromium
 * Run: node .scripts/test-wordpress.mjs
 */
import path from 'node:path';
import fs from 'node:fs';
import {pathToFileURL} from 'node:url';
const root=path.resolve(import.meta.dirname,'..');
const modules=process.env.GNN_QA_MODULES || path.join(root,'.temp/qa-tools/node_modules');
const {runCLI}=await import(pathToFileURL(path.join(modules,'@wp-playground/cli/index.js')));
const {chromium}=await import(pathToFileURL(path.join(modules,'playwright/index.mjs')));
const version=fs.readFileSync(path.join(root,'gnn/style.css'),'utf8').match(/^Version:\s*(\S+)/m)[1];
const out=process.env.GNN_QA_OUT || path.join(root,'.memory-bank/audits/verification-'+version,process.env.GNN_QA_LABEL||'');
fs.mkdirSync(out,{recursive:true});
const results={runtime:{},integration:[],browser:[],coverage:'unmeasured: no PHP coverage extension/instrumented browser coverage configured'};
const plugins=process.env.GNN_QA_MINIMUM !== '1';
const steps=plugins ? [
 {step:'installPlugin',pluginData:{resource:'url',url:'https://downloads.wordpress.org/plugin/elementor.4.3.5.zip'},options:{activate:true}},
 {step:'installPlugin',pluginData:{resource:'url',url:'https://downloads.wordpress.org/plugin/woocommerce.11.2.1.zip'},options:{activate:true}}
] : [];
if(process.env.GNN_QA_INTEGRATIONS==='1') steps.push(...[
 'contact-form-7.6.2.4.1','polylang.3.9.1','wordpress-seo.28.8','wp-consent-api.2.1.0'
].map(slug=>({step:'installPlugin',pluginData:{resource:'url',url:'https://downloads.wordpress.org/plugin/'+slug+'.zip'},options:{activate:true}})));
// Keep pinned test runtimes stable while real browser requests exercise wp-admin.
steps.unshift({step:'defineWpConfigConsts',consts:{AUTOMATIC_UPDATER_DISABLED:true,WP_AUTO_UPDATE_CORE:false,DISABLE_WP_CRON:true,
 // Playground's Windows PHP workers share the disposable SQLite file. Use rollback journaling there.
 ...(process.platform==='win32'?{SQLITE_JOURNAL_MODE:'DELETE'}:{})}});
steps.push({step:'setSiteLanguage',language:'ar'},{step:'setSiteLanguage',language:'tr_TR'},{step:'defineWpConfigConsts',consts:{WPLANG:'en_US'}},{step:'setSiteOptions',options:{WPLANG:'en_US'}},{step:'activateTheme',themeFolderName:'gnn'});
let server,browser;
const decode=response=>response.text ?? new TextDecoder().decode(response.bytes);
async function php(code,admin=false) {
 const response=await server.playground.run({code:`<?php ${admin ? "define('WP_ADMIN',true);" : ''} require '/wordpress/wp-load.php'; ${code}`});
 if(response.exitCode) throw new Error(decode(response)+'\n'+response.errors);
 return decode(response);
}
async function withoutWoo(run) {
 const active=await php("echo wp_json_encode(get_option('active_plugins')); ");
 await php("update_option('active_plugins',array_values(array_diff(get_option('active_plugins'),array('woocommerce/woocommerce.php'))));");
 try { return await run(); }
 finally { await php(`update_option('active_plugins',json_decode(${JSON.stringify(active)},true));`); }
}
try {
 server=await runCLI({command:'server',wp:process.env.GNN_QA_WP || '7.1.3',php:process.env.GNN_QA_PHP || '8.2',port:Number(process.env.GNN_QA_PORT || 9732),workers:Number(process.env.GNN_QA_WORKERS || 6),
  mount:[{hostPath:path.join(root,'gnn'),vfsPath:'/wordpress/wp-content/themes/gnn'},
   {hostPath:path.join(root,'.scripts/tests'),vfsPath:'/gnn-qa'}, {hostPath:path.join(root,'packages'),vfsPath:'/gnn-packages'}],blueprint:{steps},verbosity:'normal'});
 // Use the generated wp-config.php's existing debug definition to avoid duplicate-constant warnings.
 const debugConfig=await server.playground.readFileAsText('/wordpress/wp-config.php');
 const debugLine="define( 'WP_DEBUG', false );";
 if(!debugConfig.includes(debugLine)) throw new Error('WordPress QA configuration is missing its default debug definition.');
 await server.playground.writeFile('/wordpress/wp-config.php',debugConfig.replace(debugLine,
  "define( 'WP_DEBUG', true );\ndefine( 'WP_DEBUG_DISPLAY', false );\ndefine( 'WP_DEBUG_LOG', '/wordpress/wp-content/debug.log' );"));
 console.log('QA: WordPress and plugins ready');
 results.runtime=JSON.parse(await php("echo wp_json_encode(array('wordpress'=>get_bloginfo('version'),'php'=>PHP_VERSION,'elementor'=>defined('ELEMENTOR_VERSION')?ELEMENTOR_VERSION:null,'woocommerce'=>defined('WC_VERSION')?WC_VERSION:null));"));
 const headerOnly=process.env.GNN_QA_ONLY_HEADER==='1';
 const platformOnly=process.env.GNN_QA_ONLY_PLATFORM==='1';
 const headerSuites=plugins?['header','header-no-woo']:['header-no-woo'];
 const suites=headerOnly?headerSuites:platformOnly?(process.env.GNN_QA_INTEGRATIONS==='1'?['platform','compatibility']:['platform']):['updater','templates','woocommerce','reset','hardening','platform','revslider','kitsync','content-plugin','sections','templates-data',...headerSuites];
 for (const suite of suites) {
  if(!plugins && ['templates','woocommerce','templates-data'].includes(suite)) continue;
  console.log('QA suite:',suite);
  const standalone=['hardening','platform','compatibility','revslider','kitsync','content-plugin','sections','templates-data'].includes(suite);
  const runSuite=`$suite=${JSON.stringify(suite)}; require '/gnn-qa/${suite.startsWith('header')?'header.php':standalone?suite+'.php':'wordpress.php'}';`;
  const text=suite==='header-no-woo' && plugins ? await withoutWoo(() => php(runSuite,true)) : await php(runSuite,standalone || suite==='reset' || suite.startsWith('header'));
  const marker=text.indexOf('GNN_QA_RESULT:');
  if(marker<0) throw new Error('Missing suite output: '+text);
  const observed=JSON.parse(text.slice(marker+14));
  results.integration.push(...observed);
  console.log('QA result:',suite,observed.filter(t=>t.pass).length+'/'+observed.length);
  for(const test of observed.filter(t=>!t.pass)) console.log('FAILED:',test.name,test.detail);
  fs.writeFileSync(path.join(out,'partial.json'),JSON.stringify(results,null,2));
 }
 if(!headerOnly) await php("$suite='fixtures'; require '/gnn-qa/wordpress.php';");
 const executable=process.env.GNN_QA_BROWSER || [
  'C:/Program Files/Google/Chrome/Application/chrome.exe',
  'C:/Program Files (x86)/Microsoft/Edge/Application/msedge.exe'
 ].find(p=>fs.existsSync(p));
 browser=await chromium.launch({headless:true,...(executable?{executablePath:executable}:{})});
 if(!platformOnly){
 const {testHeaderBrowser,testHeaderSaves}=await import('./tests/header-browser.mjs');
 const env={browser,server,php,out};
 results.browser.push(...await testHeaderSaves(env,plugins));
 if(plugins) results.browser.push(...await withoutWoo(() => testHeaderSaves(env,false)));
 results.browser.push(...await testHeaderBrowser(env));
 }
 if(!platformOnly && !headerOnly){
 const {testBrowser}=await import('./tests/browser.mjs');
 results.browser.push(...await testBrowser({browser,server,php,out,root}));
 const {testHardeningBrowser}=await import('./tests/hardening-browser.mjs');
 const hardening=await testHardeningBrowser({browser,server,php,out,modules});
 results.browser.push(...hardening.tests);
 results.measurements=hardening.measurements;
 }
 if(!headerOnly){
 const {testPlatformBrowser}=await import('./tests/platform-browser.mjs');
 results.browser.push(...await testPlatformBrowser({browser,server,php,out}));
 }
 if(!headerOnly && (!platformOnly || process.env.GNN_CONTROLS_STAGES)){
 const {testControlsBrowser}=await import('./tests/controls-browser.mjs');
 results.browser.push(...await testControlsBrowser({browser,server,php,out,plugins}));
 }
} catch(error) {
 results.environmentError=String(error.stack || error);
 console.error(String(error).slice(0,1600));
 if(server) {
  try {
   const log=await server.playground.readFileAsText('/wordpress/wp-content/debug.log');
   fs.writeFileSync(path.join(out,'php-debug.log'),log);
   console.error(log.trim().split('\n').slice(-6).join('\n'));
  } catch { /* The runtime may have failed before the debug log was created. */ }
 }
 process.exitCode=1;
} finally {
 if(browser) await browser.close();
 if(server) await server[Symbol.asyncDispose]();
 const suffix=plugins?'':'-minimum';
 fs.writeFileSync(path.join(out,`results${suffix}.json`),JSON.stringify(results,null,2));
 const cases=[...results.integration,...results.browser];
 console.log(JSON.stringify({runtime:results.runtime,total:cases.length,passed:cases.filter(t=>t.pass).length,failed:cases.filter(t=>!t.pass).length,environmentError:results.environmentError?.slice(0,300) || null}));
 if(cases.some(t=>!t.pass)) process.exitCode=1;
}
