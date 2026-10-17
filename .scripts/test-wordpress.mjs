/** Real WordPress/SQLite integration and Chromium UI checks; no WordPress stubs.
 * Dev dependencies: npm install --prefix .temp/qa-tools --ignore-scripts
 *   @wp-playground/cli@3.1.58 playwright@1.64.1
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
const out=path.join(root,'.memory-bank/audits/verification-'+version);
fs.mkdirSync(out,{recursive:true});
const results={runtime:{},integration:[],browser:[],coverage:'unmeasured: no PHP coverage extension/instrumented browser coverage configured'};
const plugins=process.env.GNN_QA_MINIMUM !== '1';
const steps=plugins ? [
 {step:'installPlugin',pluginData:{resource:'url',url:'https://downloads.wordpress.org/plugin/elementor.4.3.5.zip'},options:{activate:true}},
 {step:'installPlugin',pluginData:{resource:'url',url:'https://downloads.wordpress.org/plugin/woocommerce.11.2.1.zip'},options:{activate:true}}
] : [];
steps.push({step:'activateTheme',themeFolderName:'gnn'});
let server,browser;
const decode=response=>response.text ?? new TextDecoder().decode(response.bytes);
async function php(code,admin=false) {
 const response=await server.playground.run({code:`<?php ${admin ? "define('WP_ADMIN',true);" : ''} require '/wordpress/wp-load.php'; ${code}`});
 if(response.exitCode) throw new Error(decode(response)+'\n'+response.errors);
 return decode(response);
}
try {
 server=await runCLI({command:'server',wp:process.env.GNN_QA_WP || '7.1.3',php:process.env.GNN_QA_PHP || '8.2',port:Number(process.env.GNN_QA_PORT || 9732),workers:plugins?6:2,
  mount:[{hostPath:path.join(root,'gnn'),vfsPath:'/wordpress/wp-content/themes/gnn'},
   {hostPath:path.join(root,'.scripts/tests'),vfsPath:'/gnn-qa'}],blueprint:{steps},verbosity:'normal'});
 console.log('QA: WordPress and plugins ready');
 results.runtime=JSON.parse(await php("echo wp_json_encode(array('wordpress'=>get_bloginfo('version'),'php'=>PHP_VERSION,'elementor'=>defined('ELEMENTOR_VERSION')?ELEMENTOR_VERSION:null,'woocommerce'=>defined('WC_VERSION')?WC_VERSION:null));"));
 for (const suite of ['updater','templates','woocommerce','reset']) {
  if(!plugins && ['templates','woocommerce'].includes(suite)) continue;
  console.log('QA suite:',suite);
  const text=await php(`$suite=${JSON.stringify(suite)}; require '/gnn-qa/wordpress.php';`,suite==='reset');
  const marker=text.indexOf('GNN_QA_RESULT:');
  if(marker<0) throw new Error('Missing suite output: '+text);
  const observed=JSON.parse(text.slice(marker+14));
  results.integration.push(...observed);
  console.log('QA result:',suite,observed.filter(t=>t.pass).length+'/'+observed.length);
  for(const test of observed.filter(t=>!t.pass)) console.log('FAILED:',test.name,test.detail);
  fs.writeFileSync(path.join(out,'partial.json'),JSON.stringify(results,null,2));
 }
 await php("$suite='fixtures'; require '/gnn-qa/wordpress.php';");
 const executable=process.env.GNN_QA_BROWSER || [
  'C:/Program Files/Google/Chrome/Application/chrome.exe',
  'C:/Program Files (x86)/Microsoft/Edge/Application/msedge.exe'
 ].find(p=>fs.existsSync(p));
 browser=await chromium.launch({headless:true,...(executable?{executablePath:executable}:{})});
 const {testBrowser}=await import('./tests/browser.mjs');
 results.browser=await testBrowser({browser,server,php,out,root});
} catch(error) {
 results.environmentError=String(error.stack || error);
 console.error(error);
 process.exitCode=1;
} finally {
 if(browser) await browser.close();
 if(server) await server[Symbol.asyncDispose]();
 const suffix=plugins?'':'-minimum';
 fs.writeFileSync(path.join(out,`results${suffix}.json`),JSON.stringify(results,null,2));
 const cases=[...results.integration,...results.browser];
 console.log(JSON.stringify({runtime:results.runtime,total:cases.length,passed:cases.filter(t=>t.pass).length,failed:cases.filter(t=>!t.pass).length,environmentError:results.environmentError || null}));
 if(cases.some(t=>!t.pass)) process.exitCode=1;
}
