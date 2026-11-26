<?php
/**
 * Mobile navigation uses the same featured cards and badges as desktop.
 *
 * @package GNN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class GNN_Walker_Mobile_Menu extends GNN_Walker_Nav_Menu {
	/** Give mega headings their own accordion controls. */
	protected $accordion = true;
}

