<?php
/** Real WordPress content-plugin upgrade and independent-theme checks. */
wp_set_current_user(1);
$checks = array();
$check = function($name,$pass,$detail='') use (&$checks) { $checks[]=array('name'=>$name,'pass'=>(bool)$pass,'detail'=>$detail); };
require_once ABSPATH.'wp-admin/includes/plugin.php';
require_once ABSPATH.'wp-admin/includes/theme.php';
require_once ABSPATH.'wp-admin/includes/file.php';
require_once ABSPATH.'wp-admin/includes/class-wp-upgrader.php';
$copy = function($source,$target) use (&$copy) {
 wp_mkdir_p($target);
 foreach(new DirectoryIterator($source) as $entry) {
  if($entry->isDot()) { continue; }
  if($entry->isDir()) { $copy($entry->getPathname(),$target.'/'.$entry->getFilename()); }
  else { copy($entry->getPathname(),$target.'/'.$entry->getFilename()); }
 }
};
$copy('/gnn-packages/gnn-content',WP_PLUGIN_DIR.'/gnn-content');
wp_clean_plugins_cache(true);
$activated=activate_plugin('gnn-content/gnn-content.php');
$check('C01 legacy theme activation is conflict-free',!is_wp_error($activated));
// A fresh HTTP request is essential: themes and plugins load in their real order.
$probe = <<<'PHP'
<?php
$notices=array();
set_error_handler(function($level,$message)use(&$notices){if(error_reporting() & $level){$notices[]=$message;}return false;});
require __DIR__.'/wp-load.php';
wp_set_current_user(1);
$types=array();$tax=array();$meta=array();
foreach(array('gnn_project','gnn_product','gnn_album','gnn_slide','gnn_layout') as $name){
 $o=get_post_type_object($name);
 $types[$name]=$o ? (array)$o : null;
 if($o){$types[$name]['cap']=(array)$o->cap;$types[$name]['supports']=get_all_post_type_supports($name);}
 $meta[$name]=get_registered_meta_keys('post',$name);
}
foreach(array('gnn_project_category','gnn_product_category','gnn_slide_group') as $name){
 $o=get_taxonomy($name);$tax[$name]=$o ? (array)$o : null;
 if($o){$tax[$name]['cap']=(array)$o->cap;}
}
$blocks=array();foreach(array('portfolio','catalog','gallery','products','slider') as $module){$block=WP_Block_Type_Registry::get_instance()->get_registered('gnn/'.$module);$blocks[$module]=$block?$block->attributes:null;}
$widgets=class_exists('\Elementor\Plugin')?array_keys(\Elementor\Plugin::$instance->widgets_manager->get_widget_types()):array();
$roles=array();foreach(array('gnn_client','gnn_designer','gnn_support')as $role){$roles[$role]=(bool)get_role($role);}
$id=wp_insert_post(array('post_type'=>'gnn_layout','post_status'=>'publish','post_title'=>'QA shared','post_content'=>'Content probe'));
$shared=do_shortcode('[gnn_block id="'.$id.'"]');wp_delete_post($id,true);
$slide=wp_insert_post(array('post_type'=>'gnn_slide','post_status'=>'publish','post_title'=>'Slide probe'));
$slider=gnn_render_module('slider',array());wp_delete_post($slide,true);
$render=array();foreach(array('portfolio','catalog','gallery','products','slider')as $module){$render[$module]=is_string(gnn_render_module($module,array()));}
if(function_exists('gnn_content_sanitize_settings')){$prior_options=get_option('gnn_options');update_option('gnn_options',array('unrelated'=>'preserved','custom_js_head'=>'keep'));$saved=gnn_content_sanitize_settings(array('ga4_id'=>'G-ABCD1234'));$merge=isset($saved['unrelated'])&&'preserved'===$saved['unrelated']&&'keep'===$saved['custom_js_head'];update_option('gnn_options',$prior_options);}else{$merge=null;}
echo 'CONTENT_PROBE:'.wp_json_encode(compact('types','tax','meta','blocks','widgets','roles','shared','slider','render','merge','notices'));
PHP;
file_put_contents(ABSPATH.'gnn-content-qa.php',$probe);
$fetch=function() {
 $response=wp_remote_get(site_url('/gnn-content-qa.php'),array('timeout'=>90));
 if(is_wp_error($response)){return array('error'=>$response->get_error_message());}
 $body=wp_remote_retrieve_body($response);$marker=strpos($body,'CONTENT_PROBE:');
 return false===$marker?array('error'=>substr($body,0,2000)):json_decode(substr($body,$marker+14),true);
};
$original=get_stylesheet();
try{
 $legacy=$fetch();
 $check('C02 legacy theme fresh request has no PHP notices',isset($legacy['notices'])&&!$legacy['notices'],wp_json_encode($legacy['error']??$legacy['notices']??null));
 if(!wp_get_theme('twentytwentyfive')->exists()){
  $upgrader=new Theme_Upgrader(new Automatic_Upgrader_Skin());
  $installed=$upgrader->install('https://downloads.wordpress.org/theme/twentytwentyfive.latest-stable.zip');
  $check('C03 WordPress.org theme installed',!is_wp_error($installed)&&$installed,wp_json_encode($installed));
 }
 switch_theme('twentytwentyfive');
 $independent=$fetch();
 $check('C04 independent theme fresh request has no PHP notices',isset($independent['notices'])&&!$independent['notices'],wp_json_encode($independent['error']??$independent['notices']??null));
 foreach(array('types','tax','meta','blocks')as $group){
  foreach(($legacy[$group]??array())as $name=>$args){$check('C05 '.$group.' '.$name.' argument parity',isset($independent[$group][$name])&&$args===$independent[$group][$name],wp_json_encode(array('expected'=>$args,'actual'=>$independent[$group][$name]??null)));}
 }
 foreach(array('portfolio','catalog','gallery','products','slider','block')as $widget){$check('C06 independent widget '.$widget,in_array('gnn-'.$widget,$independent['widgets']??array(),true));}
 foreach(array('gnn_client','gnn_designer','gnn_support')as $role){$check('C07 independent role '.$role,!empty($independent['roles'][$role]));}
 $check('C08 shared shortcode renders published content',false!==strpos($independent['shared']??'','Content probe'));
 $check('C11 slider renders independent template',false!==strpos($independent['slider']??'','Slide probe'));
 $check('C09 all renderers return strings',5===count(array_filter($independent['render']??array())));
 $check('C10 settings preserve unrelated fields',!empty($independent['merge']));
}finally{switch_theme($original);unlink(ABSPATH.'gnn-content-qa.php');deactivate_plugins('gnn-content/gnn-content.php');}
echo 'GNN_QA_RESULT:'.wp_json_encode($checks);
