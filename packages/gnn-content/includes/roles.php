<?php
/**
 * GNN Content independent API.
 *
 * @package GNN_Content
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; }
/** Add only missing roles; preserve custom capabilities. */
function gnn_register_agency_roles() {
	$editor = get_role( 'editor' );
	if ( ! $editor ) {
		return;
	}
	foreach ( array(
		'gnn_client'   => array( 'GNN Client', 'gnn_manage_brand' ),
		'gnn_designer' => array( 'GNN Designer', 'gnn_manage_design' ),
	) as $name => $profile ) {
		if ( ! get_role( $name ) ) {
			$caps                    = $editor->capabilities;
			$caps['unfiltered_html'] = false;
			$caps[ $profile[1] ]     = true;
			add_role( $name, $profile[0], $caps );
		}
	}
	if ( ! get_role( 'gnn_support' ) ) {
		add_role(
			'gnn_support',
			'GNN Support',
			array(
				'read'                 => true,
				'gnn_view_diagnostics' => true,
			)
		);
	}
}
add_action( 'init', 'gnn_register_agency_roles' );
