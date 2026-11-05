/* Consent-first tag loading. No storage or third-party requests before approval. */
(function () {
 'use strict';
 var config=window.gnnTracking||{},loaded=false,granted=false;
 window.dataLayer=window.dataLayer||[];
 function command(){window.dataLayer.push(arguments);}
 command('consent','default',{analytics_storage:'denied',ad_storage:'denied',ad_user_data:'denied',ad_personalization:'denied'});
 function setConsent(allow){
  granted=allow===true;
  command('consent','update',{analytics_storage:granted?'granted':'denied',ad_storage:'denied',ad_user_data:'denied',ad_personalization:'denied'});
  if(!granted||loaded)return;
  var url='';
  if(/^GTM-[A-Z0-9]+$/i.test(config.gtm)){url='https://www.googletagmanager.com/gtm.js?id='+encodeURIComponent(config.gtm);window.dataLayer.push({'gtm.start':Date.now(),event:'gtm.js'});}
  else if(/^G-[A-Z0-9]+$/i.test(config.ga4)){window.gtag=window.gtag||command;command('js',new Date());command('config',config.ga4);url='https://www.googletagmanager.com/gtag/js?id='+encodeURIComponent(config.ga4);}
  if(url){loaded=true;var script=document.createElement('script');script.async=true;script.src=url;document.head.appendChild(script);}
 }
 function readProvider(){
  // The API allows everything when no CMP supplies a consent type; do not treat that as approval.
  var type=typeof window.wp_get_consent_type==='function'?window.wp_get_consent_type():window.wp_consent_type;
  if(type&&typeof window.wp_has_consent==='function')setConsent(window.wp_has_consent('statistics')===true);
 }
 window.GNNConsent=Object.freeze({setAnalyticsConsent:setConsent,hasAnalyticsConsent:function(){return granted;}});
 document.addEventListener('gnn:consent',function(event){setConsent(!!event.detail&&event.detail.analytics===true);});
 document.addEventListener('wp_listen_for_consent_change',readProvider);
 document.addEventListener('wp_consent_api_status_change',readProvider);
 document.addEventListener('DOMContentLoaded',readProvider);readProvider();
})();
