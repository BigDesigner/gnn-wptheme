<?php
/** Real optional plugin APIs and child/content-continuity upgrade checks. Local only. */
wp_set_current_user( 1 );
$checks = array();
$check = function ( $name, $pass, $detail = '' ) use ( &$checks ) { $checks[] = array( 'name' => $name, 'pass' => (bool) $pass, 'detail' => $detail ); };
$check( 'I01 real Contact Form 7 is active', defined( 'WPCF7_VERSION' ), defined( 'WPCF7_VERSION' ) ? WPCF7_VERSION : '' );
$form = WPCF7_ContactForm::get_template(); $form->set_title( 'GNN contact verification' ); $properties=$form->get_properties();$properties['additional_settings']='skip_mail: on';$form->set_properties($properties);$form->save();update_option('gnn_qa_form_id',$form->id());
$html = do_shortcode( '[contact-form-7 id="' . $form->id() . '" title="GNN contact verification"]' );
$check( 'I02 native form shortcode renders actual controls', false !== strpos( $html, 'your-email' ) && false !== strpos( $html, 'wpcf7-submit' ) );
$check( 'I03 theme styles real CF7 markup', false !== strpos( file_get_contents( get_template_directory() . '/assets/css/main.css' ), '.wpcf7' ) );
$check( 'I04 real Yoast SEO API is available', function_exists( 'yoast_breadcrumb' ), defined( 'WPSEO_VERSION' ) ? WPSEO_VERSION : '' );
ob_start();$provided=gnn_provider_breadcrumb();$trail=ob_get_clean();
$check( 'I05 SEO adapter claims ownership only when its real provider renders output', $provided === ( '' !== trim($trail) ) );
$check( 'I06 real Polylang API is available', function_exists( 'pll_register_string' ) && function_exists( 'pll_get_post' ), defined( 'POLYLANG_VERSION' ) ? POLYLANG_VERSION : '' );
gnn_register_translatable_options();
$check( 'I07 public copy registration tolerates active Polylang', is_string( gnn_translated_option( 'company_name' ) ) );
if ( function_exists( 'PLL' ) && isset( PLL()->model ) ) {
 foreach ( array( array( 'name'=>'English','slug'=>'en','locale'=>'en_US' ), array( 'name'=>'Türkçe','slug'=>'tr','locale'=>'tr_TR' ) ) as $lang ) {
  if ( ! PLL()->model->get_language( $lang['slug'] ) ) { PLL()->model->add_language( array_merge( $lang, array( 'rtl'=>0,'term_group'=>0 ) ) ); }
 }
 $a=wp_insert_post(array('post_type'=>'page','post_status'=>'publish','post_title'=>'English contact'));$b=wp_insert_post(array('post_type'=>'page','post_status'=>'publish','post_title'=>'Türkçe iletişim'));
 pll_set_post_language($a,'en');pll_set_post_language($b,'tr');pll_save_post_translations(array('en'=>$a,'tr'=>$b));
 $check('I08 real language relationship resolves explicit target', $b === pll_get_post($a,'tr'));
} else { $check('I08 real language relationship resolves explicit target',false); }
$check( 'I09 real WP Consent API is active', function_exists( 'wp_has_consent' ) );
wp_enqueue_scripts();
$check( 'I10 integration styles coexist with the shared design system', wp_style_is( 'gnn-design-system', 'enqueued' ) );
$plugin='/wordpress/wp-content/plugins/gnn-content';wp_mkdir_p($plugin);copy('/gnn-packages/gnn-content/gnn-content.php',$plugin.'/gnn-content.php');
require_once ABSPATH.'wp-admin/includes/plugin.php';wp_clean_plugins_cache(true);$activated=activate_plugin('gnn-content/gnn-content.php');
$check('I11 optional content plugin activates without duplicating existing types', !is_wp_error($activated));
$layout=wp_insert_post(array('post_type'=>'gnn_layout','post_status'=>'publish','post_title'=>'Continuity record'));
$slide=wp_insert_post(array('post_type'=>'gnn_slide','post_status'=>'publish','post_title'=>'Continuity slide'));
unregister_post_type('gnn_layout');unregister_post_type('gnn_slide');gnn_content_register_types();
$check('I12 content plugin registers compatible types independently', post_type_exists('gnn_layout')&&post_type_exists('gnn_slide')&&'Continuity record'===get_the_title($layout)&&'Continuity slide'===get_the_title($slide));
$u=get_user_by('login','platform-designer');wp_set_current_user($u->ID);
$check('I13 content continuity preserves designer capabilities',current_user_can('edit_post',$layout)&&!current_user_can('manage_options'));
wp_set_current_user(1);
$child='/wordpress/wp-content/themes/gnn-child';wp_mkdir_p($child);copy('/gnn-packages/gnn-child/style.css',$child.'/style.css');copy('/gnn-packages/gnn-child/functions.php',$child.'/functions.php');
set_theme_mod('gnn_accent_color','#334455');set_theme_mod('gnn_default_theme','light');
switch_theme('gnn-child');require_once $child.'/functions.php';do_action('after_switch_theme');
$check('I14 child activation preserves parent brand settings', '#334455'===get_theme_mod('gnn_accent_color')&&'light'===get_theme_mod('gnn_default_theme'));
$check('I15 child keeps parent template contract', 'gnn'===get_template()&&'gnn-child'===get_stylesheet());
switch_theme('gnn');
echo 'GNN_QA_RESULT:'.wp_json_encode($checks);
